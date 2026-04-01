<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'license_number', 'phone', 'address', 'status'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
