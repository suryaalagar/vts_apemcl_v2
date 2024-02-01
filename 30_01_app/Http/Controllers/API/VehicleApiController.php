<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleApiController extends Controller
{
    public function vehicle_status(Request $request)
    {
        $get_data = $request->all();
        $device_imei = $get_data['device_imei'];
        date_default_timezone_set('Asia/Kolkata');
        $current_time = date("Y-m-d H:i:s");
        $query = DB::table('live_data')
            ->where('deviceimei', '=', $device_imei)
            ->select(
                'vehicle_name',
                'deviceimei',
                'lattitute',
                'longitute',
                'ignition',
                'speed',
                'device_updatedtime',
                DB::raw('TIMESTAMPDIFF(MINUTE, device_updatedtime, "' . $current_time . '") AS update_time')
            )->first();
        return $query;
    }
}
