<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'device_no',
        'start_location',
        'end_location',
        'start_day',
        'end_day',
        'duration',
        'created_at',
    ];
}
