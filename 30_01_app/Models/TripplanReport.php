<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripplanReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id', 'vehicleid', 'vehicle_name','start_location','end_location','poc_number','route_name',
    ];
}
