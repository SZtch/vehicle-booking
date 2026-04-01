<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    protected $fillable = [
        'vehicle_id', 'booking_id', 'liters', 'cost', 'odometer', 'logged_at', 'notes',
    ];

    protected $casts = [
        'logged_at' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
