<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripplanReport;
use Illuminate\Support\Facades\DB;

class CronJob extends Controller
{
    public function add_trip_plan(Request $request)
    {

        $data = $request->all();
        $vehiclename = trim($data['vehiclename']);
        $check_vehicle =  DB::table("vehicles")->select('vehicle_name', 'id')->where('vehicle_name', '=', $vehiclename)->first();
        if (!empty($check_vehicle)) {

            $check_trip = DB::table('tripplan_reports')->select('trip_id', 'status')->where('vehicleid', '=', $check_vehicle->id)->orderBy('trip_id', 'DESC')->first();
            if (!empty($check_trip)) {

                $status = $check_trip->status;
                if ($status == 3) {
                    DB::table('trip_polylines')->insert([
                        'vehicleid' => $check_vehicle->id,
                        'poc_number' => $data['transactionid'],
                        'polyline' => $data['polyline'],
                        'route_name' => $data['routename'],
                    ]);

                    $created_date = strtotime($data['beginDate']);
                    $created_date = date('Y-m-d H:i:s', $created_date);
                    DB::table('tripplan_reports')->insert([
                        'vehicleid' => $check_vehicle->id,
                        'vehicle_name' => $vehiclename,
                        's_lat' => $data['startlat'],
                        's_lng' => $data['startlng'],
                        'e_lat' => $data['endlat'],
                        'e_lng' => $data['endlng'],
                        'route_name' => $data['routename'],
                        'client_id' => 1,
                        // 'start_location' => $start_location_id,
                        // 'end_location' => $end_location_id,
                        // 'route_namedevi' => $route_id,
                        'trip_date' => $created_date,
                        'poc_number' => $data['transactionid'],
                        'status' => 1
                    ]);
                    $data1['status'] = 1;
                    $data1['message'] = 'Data Inserted SuccessFully';
                    return $data1;
                } else {
                    $data1['status'] = 0;
                    $data1['message'] = 'Previous Trip Not Completed';
                    return $data1;
                }
            }
            // $start_location_id = $this->addGeofence($data['startlat'], $data['startlng']);
            // $end_location_id = $this->addGeofence($data['endlat'], $data['endlng']);
            // $route_id = $this->addRoute($data['routename'], $data['polyline']);
            DB::table('trip_polylines')->insert([
                'vehicleid' => $check_vehicle->id,
                'poc_number' => $data['transactionid'],
                'polyline' => $data['polyline'],
                'route_name' => $data['routename'],
            ]);

            $created_date = strtotime($data['beginDate']);
            $created_date = date('Y-m-d H:i:s', $created_date);
            DB::table('tripplan_reports')->insert([
                'vehicleid' => $check_vehicle->id,
                'vehicle_name' => $vehiclename,
                's_lat' => $data['startlat'],
                's_lng' => $data['startlng'],
                'e_lat' => $data['endlat'],
                'e_lng' => $data['endlng'],
                'route_name' => $data['routename'],
                'client_id' => 1,
                // 'start_location' => $start_location_id,
                // 'end_location' => $end_location_id,
                // 'route_namedevi' => $route_id,
                'trip_date' => $created_date,
                'poc_number' => $data['transactionid'],
                'status' => 1
            ]);
            $data1['status'] = 1;
            $data1['message'] = 'Data Inserted SuccessFully';
            return $data1;
        } else {
            $data1['status'] = 0;
            $data1['message'] = 'Vehicle Not Found..';
            return $data1;
        }
    }
}
