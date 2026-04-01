<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['vehicle', 'driver', 'admin', 'approvals.approver'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $vehicles  = Vehicle::where('status', 'available')->get();
        $drivers   = Driver::where('status', 'available')->get();
        $approvers = User::where('role', 'approver')->orderBy('approval_level')->get();

        return view('bookings.create', compact('vehicles', 'drivers', 'approvers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id',
            'driver_id'       => 'required|exists:drivers,id',
            'approver_l1_id'  => 'required|exists:users,id',
            'approver_l2_id'  => 'required|exists:users,id|different:approver_l1_id',
            'purpose'         => 'required|string|max:255',
            'origin'          => 'required|string|max:255',
            'destination'     => 'required|string|max:255',
            'start_date'      => 'required|date|after_or_equal:now',
            'end_date'        => 'required|date|after:start_date',
            'passenger_count' => 'required|integer|min:1',
            'notes'           => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $booking = Booking::create([
                ...$validated,
                'admin_id' => auth()->id(),
                'status'   => 'pending',
            ]);

            // Create 2-level approvals
            BookingApproval::create([
                'booking_id'  => $booking->id,
                'approver_id' => $request->approver_l1_id,
                'level'       => 1,
                'status'      => 'pending',
            ]);

            BookingApproval::create([
                'booking_id'  => $booking->id,
                'approver_id' => $request->approver_l2_id,
                'level'       => 2,
                'status'      => 'pending',
            ]);

            // Update vehicle & driver status
            Vehicle::find($validated['vehicle_id'])->update(['status' => 'in_use']);
            Driver::find($validated['driver_id'])->update(['status' => 'on_duty']);

            // Log
            ActivityLog::record(
                'created_booking',
                "Admin membuat pemesanan kendaraan #{$booking->booking_code}",
                $booking,
                ['vehicle_id' => $booking->vehicle_id, 'driver_id' => $booking->driver_id]
            );

            Log::info("Booking created: {$booking->booking_code} by user " . auth()->id());
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Pemesanan berhasil dibuat dan menunggu persetujuan.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['vehicle', 'driver', 'admin', 'approvals.approver', 'fuelLogs']);

        ActivityLog::record(
            'viewed_booking',
            "Melihat detail pemesanan #{$booking->booking_code}",
            $booking
        );

        return view('bookings.show', compact('booking'));
    }

    public function destroy(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Hanya pemesanan dengan status pending yang dapat dibatalkan.');
        }

        DB::transaction(function () use ($booking) {
            $booking->vehicle->update(['status' => 'available']);
            $booking->driver->update(['status' => 'available']);

            ActivityLog::record(
                'cancelled_booking',
                "Membatalkan pemesanan #{$booking->booking_code}",
                $booking
            );

            $booking->delete();
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}
