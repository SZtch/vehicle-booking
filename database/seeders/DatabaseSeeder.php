<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'       => 'Admin Fleet',
            'email'      => 'admin@fleet.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'department' => 'Pool Kendaraan',
            'phone'      => '081234567890',
        ]);

        // Approver Level 1 (Supervisor/Kepala Bagian)
        User::create([
            'name'           => 'Budi Santoso',
            'email'          => 'approver1@fleet.com',
            'password'       => Hash::make('password'),
            'role'           => 'approver',
            'approval_level' => 1,
            'department'     => 'Operasional',
            'phone'          => '081234567891',
        ]);

        // Approver Level 2 (Manager/Direktur)
        User::create([
            'name'           => 'Siti Rahayu',
            'email'          => 'approver2@fleet.com',
            'password'       => Hash::make('password'),
            'role'           => 'approver',
            'approval_level' => 2,
            'department'     => 'Manajemen',
            'phone'          => '081234567892',
        ]);

        // Vehicles
        $vehicles = [
            ['name' => 'Toyota Avanza', 'plate_number' => 'B 1234 ABC', 'type' => 'angkutan_orang', 'brand' => 'Toyota', 'model' => 'Avanza', 'year' => 2022, 'ownership' => 'owned'],
            ['name' => 'Mitsubishi L300', 'plate_number' => 'B 5678 DEF', 'type' => 'angkutan_barang', 'brand' => 'Mitsubishi', 'model' => 'L300', 'year' => 2021, 'ownership' => 'owned'],
            ['name' => 'Toyota Innova', 'plate_number' => 'B 9012 GHI', 'type' => 'angkutan_orang', 'brand' => 'Toyota', 'model' => 'Innova', 'year' => 2023, 'ownership' => 'owned'],
            ['name' => 'Isuzu Traga', 'plate_number' => 'B 3456 JKL', 'type' => 'angkutan_barang', 'brand' => 'Isuzu', 'model' => 'Traga', 'year' => 2020, 'ownership' => 'rented'],
            ['name' => 'Honda Brio (Sewa)', 'plate_number' => 'B 7890 MNO', 'type' => 'angkutan_orang', 'brand' => 'Honda', 'model' => 'Brio', 'year' => 2023, 'ownership' => 'rented'],
        ];

        foreach ($vehicles as $v) {
            Vehicle::create(array_merge($v, [
                'status'       => 'available',
                'odometer'     => rand(5000, 80000),
                'last_service' => now()->subMonths(rand(1, 6)),
                'next_service' => now()->addMonths(rand(1, 3)),
            ]));
        }

        // Drivers
        $drivers = [
            ['name' => 'Joko Widodo', 'license_number' => 'SIM123456', 'phone' => '081111111111'],
            ['name' => 'Ahmad Fauzi', 'license_number' => 'SIM789012', 'phone' => '082222222222'],
            ['name' => 'Dewi Lestari', 'license_number' => 'SIM345678', 'phone' => '083333333333'],
        ];

        foreach ($drivers as $d) {
            Driver::create(array_merge($d, ['status' => 'available']));
        }
    }
}
