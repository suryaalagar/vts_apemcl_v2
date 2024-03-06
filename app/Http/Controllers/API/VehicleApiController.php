<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TripplanReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleApiController extends Controller
{
    // public function vehicle_status(Request $request)
    // {
    //     $get_data = $request->all();
    //     $device_imei = $get_data['device_imei'];
    //     date_default_timezone_set('Asia/Kolkata');
    //     $current_time = date("Y-m-d H:i:s");
    //     $query = DB::table('live_data')
    //         ->where('deviceimei', '=', $device_imei)
    //         ->select(
    //             'vehicle_name',
    //             'deviceimei',
    //             'lattitute',
    //             'longitute',
    //             'ignition',
    //             'speed',
    //             'device_updatedtime',
    //             DB::raw('TIMESTAMPDIFF(MINUTE, device_updatedtime, "' . $current_time . '") AS update_time')
    //         )->first();
    //     return $query;
    // }
    public function vehicle_status(Request $request)
    {
        $get_data = $request->all();
        $device_imei = $get_data['device_imei'];
        date_default_timezone_set('Asia/Kolkata');
        $current_time = date("Y-m-d H:i:s");
        $query = 
            TripplanReport::join('live_data AS l','l.deviceimei', '=', 'tripplan_reports.device_imei')
            ->where('tripplan_reports.device_imei', '=', $device_imei)
            ->select(
                'l.vehicle_name',
                'l.deviceimei',
                'l.lattitute',
                'l.longitute',
                'l.ignition',
                'l.speed',
                'l.angle',
                DB::raw('TIMESTAMPDIFF(MINUTE, l.device_updatedtime, "' . $current_time . '") AS update_time'),
                DB::raw('(CASE 
                WHEN tripplan_reports.status = 1 THEN "In Hub"
                WHEN tripplan_reports.status = 2 THEN "In Processing"
                ELSE "Complete"
                END) AS trip_status')
            )
            ->orderBy('tripplan_reports.trip_id', 'DESC')
            ->first();
            
        return $query;
    }
}
