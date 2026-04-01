<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'plate_number', 'brand', 'model', 'year',
        'type', 'ownership', 'status', 'last_service', 'next_service', 'odometer',
    ];

    protected $casts = [
        'last_service' => 'date',
        'next_service' => 'date',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'angkutan_orang'  => 'Angkutan Orang',
            'angkutan_barang' => 'Angkutan Barang',
            default           => $this->type,
        };
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'available'   => '<span class="badge badge-success">Tersedia</span>',
            'in_use'      => '<span class="badge badge-warning">Digunakan</span>',
            'maintenance' => '<span class="badge badge-danger">Maintenance</span>',
            default       => $this->status,
        };
    }
}
