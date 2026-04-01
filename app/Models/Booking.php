<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_code', 'admin_id', 'vehicle_id', 'driver_id',
        'purpose', 'origin', 'destination',
        'start_date', 'end_date', 'passenger_count', 'notes', 'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_code = 'BK-' . strtoupper(Str::random(8));
        });
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function approvals()
    {
        return $this->hasMany(BookingApproval::class)->orderBy('level');
    }

    public function approvalLevel1()
    {
        return $this->hasOne(BookingApproval::class)->where('level', 1);
    }

    public function approvalLevel2()
    {
        return $this->hasOne(BookingApproval::class)->where('level', 2);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending'     => 'Menunggu Persetujuan',
            'approved_l1' => 'Disetujui Level 1',
            'approved'    => 'Disetujui',
            'rejected'    => 'Ditolak',
            default       => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending'     => 'warning',
            'approved_l1' => 'info',
            'approved'    => 'success',
            'rejected'    => 'danger',
            default       => 'secondary',
        };
    }
}
