<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('license_number', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $drivers = $query->paginate(10)->withQueryString();

        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'license_number' => 'required|string|max:50|unique:drivers,license_number',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:255',
            'status'         => 'required|in:available,on_duty',
        ]);

        $driver = Driver::create($validated);

        ActivityLog::record(
            'created_driver',
            "Menambahkan driver baru: {$driver->name}",
            $driver
        );

        Log::info("Driver created: {$driver->name} (ID {$driver->id}) by user " . auth()->id());

        return redirect()->route('drivers.index')
            ->with('success', "Driver {$driver->name} berhasil ditambahkan.");
    }

    public function show(Driver $driver)
    {
        $driver->load(['bookings.vehicle', 'bookings.admin']);

        $stats = [
            'total_bookings'    => $driver->bookings()->count(),
            'approved_bookings' => $driver->bookings()->where('status', 'approved')->count(),
        ];

        return view('drivers.show', compact('driver', 'stats'));
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'license_number' => 'required|string|max:50|unique:drivers,license_number,' . $driver->id,
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:255',
            'status'         => 'required|in:available,on_duty',
        ]);

        $driver->update($validated);

        ActivityLog::record(
            'updated_driver',
            "Mengubah data driver: {$driver->name}",
            $driver
        );

        Log::info("Driver updated: {$driver->name} (ID {$driver->id}) by user " . auth()->id());

        return redirect()->route('drivers.index')
            ->with('success', "Data driver {$driver->name} berhasil diperbarui.");
    }

    public function destroy(Driver $driver)
    {
        if ($driver->bookings()->whereIn('status', ['pending', 'approved_l1', 'approved'])->exists()) {
            return back()->with('error', 'Driver tidak dapat dihapus karena masih memiliki pemesanan aktif.');
        }

        $name = $driver->name;

        ActivityLog::record(
            'deleted_driver',
            "Menghapus driver: {$name}",
            $driver
        );

        $driver->delete();

        Log::info("Driver deleted: {$name} by user " . auth()->id());

        return redirect()->route('drivers.index')
            ->with('success', "Driver {$name} berhasil dihapus.");
    }
}
