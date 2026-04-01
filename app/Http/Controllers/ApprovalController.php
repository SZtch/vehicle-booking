<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\BookingApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    public function index()
    {
        $approvals = BookingApproval::with(['booking.vehicle', 'booking.driver', 'booking.admin'])
            ->where('approver_id', auth()->id())
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->paginate(10);

        return view('approvals.index', compact('approvals'));
    }

    public function show(BookingApproval $approval)
    {
        // Approver hanya bisa lihat approval yang ditugaskan ke dia
        if ($approval->approver_id !== auth()->id()) {
            abort(403);
        }

        $approval->load(['booking.vehicle', 'booking.driver', 'booking.admin', 'booking.approvals.approver']);

        return view('approvals.show', compact('approval'));
    }

    public function process(Request $request, BookingApproval $approval)
    {
        if ($approval->approver_id !== auth()->id()) {
            abort(403);
        }

        if ($approval->status !== 'pending') {
            return back()->with('error', 'Persetujuan ini sudah diproses sebelumnya.');
        }

        // Level 2 hanya bisa approve setelah level 1 approve
        if ($approval->level === 2) {
            $level1 = $approval->booking->approvalLevel1;
            if (!$level1 || $level1->status !== 'approved') {
                return back()->with('error', 'Menunggu persetujuan Level 1 terlebih dahulu.');
            }
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes'  => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $approval) {
            $action   = $validated['action'];
            $isApprove = $action === 'approve';
            $booking  = $approval->booking;

            // Update approval record
            $approval->update([
                'status'     => $isApprove ? 'approved' : 'rejected',
                'notes'      => $validated['notes'],
                'decided_at' => now(),
            ]);

            if (!$isApprove) {
                // Rejected: update booking, free vehicle & driver
                $booking->update(['status' => 'rejected']);
                $booking->vehicle->update(['status' => 'available']);
                $booking->driver->update(['status' => 'available']);

                ActivityLog::record(
                    'rejected_booking',
                    "Menolak pemesanan #{$booking->booking_code} (Level {$approval->level})",
                    $booking,
                    ['notes' => $validated['notes']]
                );

                Log::info("Booking {$booking->booking_code} rejected at level {$approval->level} by user " . auth()->id());

            } elseif ($approval->level === 1) {
                $booking->update(['status' => 'approved_l1']);

                ActivityLog::record(
                    'approved_booking_l1',
                    "Menyetujui pemesanan #{$booking->booking_code} (Level 1)",
                    $booking
                );

                Log::info("Booking {$booking->booking_code} approved at level 1 by user " . auth()->id());

            } elseif ($approval->level === 2) {
                $booking->update(['status' => 'approved']);

                ActivityLog::record(
                    'approved_booking_l2',
                    "Menyetujui pemesanan #{$booking->booking_code} (Level 2) - Final",
                    $booking
                );

                Log::info("Booking {$booking->booking_code} fully approved by user " . auth()->id());
            }
        });

        $message = $validated['action'] === 'approve' ? 'Pemesanan berhasil disetujui.' : 'Pemesanan berhasil ditolak.';

        return redirect()->route('approvals.index')->with('success', $message);
    }
}
