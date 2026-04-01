<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Stats cards
        $stats = [
            'total_vehicles'    => Vehicle::count(),
            'available_vehicles'=> Vehicle::where('status', 'available')->count(),
            'total_bookings'    => Booking::count(),
            'pending_bookings'  => Booking::where('status', 'pending')->count(),
            'approved_bookings' => Booking::where('status', 'approved')->count(),
            'total_drivers'     => Driver::count(),
        ];

        // Monthly bookings chart - last 6 months
        $monthlyBookings = Booking::select(
                DB::raw('MONTH(start_date) as month'),
                DB::raw('YEAR(start_date) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->where('start_date', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'label' => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                'total' => $item->total,
            ]);

        // Bookings by vehicle type
        $byType = Booking::join('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
            ->select('vehicles.type', DB::raw('COUNT(*) as total'))
            ->groupBy('vehicles.type')
            ->get();

        // Bookings by status
        $byStatus = Booking::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        // Top 5 most used vehicles
        $topVehicles = Booking::join('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
            ->select('vehicles.name', 'vehicles.plate_number', DB::raw('COUNT(*) as usage_count'))
            ->where('bookings.status', 'approved')
            ->groupBy('vehicles.id', 'vehicles.name', 'vehicles.plate_number')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        // Recent activity logs
        $recentLogs = ActivityLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        // Pending approvals for approver
        $pendingApprovals = 0;
        if ($user->isApprover()) {
            $pendingApprovals = \App\Models\BookingApproval::where('approver_id', $user->id)
                ->where('status', 'pending')
                ->count();
        }

        return view('dashboard.index', compact(
            'stats', 'monthlyBookings', 'byType', 'byStatus',
            'topVehicles', 'recentLogs', 'pendingApprovals'
        ));
    }
}
