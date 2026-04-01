<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('plate_number', 'like', "%{$request->search}%")
                  ->orWhere('brand', 'like', "%{$request->search}%");
            });
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $vehicles = $query->paginate(10)->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'brand'        => 'nullable|string|max:50',
            'model'        => 'nullable|string|max:50',
            'year'         => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'type'         => 'required|in:angkutan_orang,angkutan_barang',
            'ownership'    => 'required|in:owned,rented',
            'status'       => 'required|in:available,in_use,maintenance',
            'odometer'     => 'nullable|integer|min:0',
            'last_service' => 'nullable|date',
            'next_service' => 'nullable|date|after_or_equal:last_service',
        ]);

        $vehicle = Vehicle::create($validated);

        ActivityLog::record(
            'created_vehicle',
            "Menambahkan kendaraan baru: {$vehicle->name} ({$vehicle->plate_number})",
            $vehicle
        );

        Log::info("Vehicle created: {$vehicle->plate_number} by user " . auth()->id());

        return redirect()->route('vehicles.index')
            ->with('success', "Kendaraan {$vehicle->name} berhasil ditambahkan.");
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['bookings.driver', 'bookings.admin', 'fuelLogs']);

        $usageStats = [
            'total_bookings'    => $vehicle->bookings()->count(),
            'approved_bookings' => $vehicle->bookings()->where('status', 'approved')->count(),
            'total_fuel'        => $vehicle->fuelLogs()->sum('liters'),
            'total_fuel_cost'   => $vehicle->fuelLogs()->sum('cost'),
        ];

        return view('vehicles.show', compact('vehicle', 'usageStats'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'brand'        => 'nullable|string|max:50',
            'model'        => 'nullable|string|max:50',
            'year'         => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'type'         => 'required|in:angkutan_orang,angkutan_barang',
            'ownership'    => 'required|in:owned,rented',
            'status'       => 'required|in:available,in_use,maintenance',
            'odometer'     => 'nullable|integer|min:0',
            'last_service' => 'nullable|date',
            'next_service' => 'nullable|date|after_or_equal:last_service',
        ]);

        $vehicle->update($validated);

        ActivityLog::record(
            'updated_vehicle',
            "Mengubah data kendaraan: {$vehicle->name} ({$vehicle->plate_number})",
            $vehicle
        );

        Log::info("Vehicle updated: {$vehicle->plate_number} by user " . auth()->id());

        return redirect()->route('vehicles.index')
            ->with('success', "Data kendaraan {$vehicle->name} berhasil diperbarui.");
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->bookings()->whereIn('status', ['pending', 'approved_l1', 'approved'])->exists()) {
            return back()->with('error', 'Kendaraan tidak dapat dihapus karena masih memiliki pemesanan aktif.');
        }

        $name = $vehicle->name;
        $plate = $vehicle->plate_number;

        ActivityLog::record(
            'deleted_vehicle',
            "Menghapus kendaraan: {$name} ({$plate})",
            $vehicle
        );

        $vehicle->delete();

        Log::info("Vehicle deleted: {$plate} by user " . auth()->id());

        return redirect()->route('vehicles.index')
            ->with('success', "Kendaraan {$name} berhasil dihapus.");
    }
}
