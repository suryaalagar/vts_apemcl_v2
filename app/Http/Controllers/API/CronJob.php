<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripplanReport;
use Illuminate\Support\Facades\DB;
use App\Service\AddressService;
use Carbon\Carbon;


class CronJob extends Controller
{
    use AddressService;

    public function add_trip_plan(Request $request)
    {

        $data = $request->all();
        // print_r($data);
        $vehiclename = trim($data['vehiclename']);
        $check_vehicle =  DB::table("vehicles")->select('vehicle_name', 'id','device_imei')->where('vehicle_name', '=', $vehiclename)->first();
        if (!empty($check_vehicle)) {

            $check_trip = DB::table('tripplan_reports')->select('trip_id', 'status')->where('vehicleid', '=', $check_vehicle->id)->orderBy('trip_id', 'DESC')->first();
            if (!empty($check_trip)) {

                $status = $check_trip->status;
                if ($status == 3) {
                    // DB::table('trip_polylines')->insert([
                    //     'vehicleid' => $check_vehicle->id,
                    //     'poc_number' => $data['transactionid'],
                    //     'polyline' => $data['polyline'],
                    //     'route_name' => $data['routename'],
                    // ]);
                    $routes = DB::table('routes')
                    ->select('id', 'routename')
                    ->where('routename', '=', $data['routename'])
                    ->first();

                    if (!empty($routes)) {
                        $created_date = strtotime($data['beginDate']);
                        $created_date = date('Y-m-d H:i:s', $created_date);
                        DB::table('tripplan_reports')->insert([
                            'vehicleid' => $check_vehicle->id,
                            'device_imei' => $check_vehicle->device_imei,
                            'vehicle_name' => $vehiclename,
                            's_lat' => $data['startlat'],
                            's_lng' => $data['startlng'],
                            'e_lat' => $data['endlat'],
                            'e_lng' => $data['endlng'],
                            'route_name' => $data['routename'],
                            'client_id' => 1,
                            'trip_date' => $created_date,
                            'poc_number' => $data['transactionid'],
                            'status' => 1
                        ]);
                        $data1['status'] = 1;
                        $data1['message'] = 'Data Inserted SuccessFully';
                        return $data1;
                    }else{
                        $data1['status'] = 1;
                        $data1['message'] = 'Route Not Found..';
                        return $data1;
                    }
                 
                } else {
                    $data1['status'] = 0;
                    $data1['message'] = 'Previous Trip Not Completed';
                    return $data1;
                }
            }

            $routes = DB::table('routes')
            ->select('id', 'routename')
            ->where('routename', '=', $data['routename'])
            ->first();

            if (!empty($routes)) {
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
                    'trip_date' => $created_date,
                    'poc_number' => $data['transactionid'],
                    'status' => 1
                ]);
                $data1['status'] = 1;
                $data1['message'] = 'Data Inserted SuccessFully';
                return $data1;
            }else{
                $data1['status'] = 1;
                $data1['message'] = 'Route Not Found..';
                return $data1;
            }
        } else {
            $data1['status'] = 0;
            $data1['message'] = 'Vehicle Not Found..';
            return $data1;
        }
    }

    public function route_devation_cron()
    {
        // $vehicle_list =  $this->Cron_test_model->get_route_deviation_vehicles();
        $vehicle_list = DB::table('tripplan_reports AS tr')
            ->select(
                'tr.route_name',
                'tr.status',
                'tp.route_polyline',
                'l.lattitute',
                'l.longitute',
                'l.ignition',
                'l.speed',
                'tr.device_imei',
                'tr.vehicle_name',
                'l.device_updatedtime'
            )
            ->join('routes AS tp', 'tr.route_name', '=', 'tp.routename')
            ->join('live_data AS l', 'l.deviceimei', '=', 'tr.device_imei')
            ->whereIn('tr.status', [2])
            ->get()->toArray();

        if (!empty($vehicle_list)) {

            foreach ($vehicle_list as $key) {
                $decodedPolyline = $this->decodePolyline($key->route_polyline);
                // dd($decodedPolyline);
                $yes = 0;
                // $route_id = $route_list->route_id;
                $radius = 500;
                $lat = $key->lattitute; //latitude
                $lng =  $key->longitute; //longitude

                $isInside = $this->isPointInsidePolylinewithRadius($lat, $lng, $decodedPolyline, $radius);
                if ($isInside) {

                    $rd_qry = DB::table('routedeviation_reports')
                        ->select('id', 'route_name', 'vehicle_imei', 'vehicle_name', 'route_deviate_outtime', 'route_deviate_intime', 'route_out_lat', 'route_out_lng', 'route_in_lat', 'route_in_lng')
                        ->where('vehicle_imei', $key->device_imei)
                        ->whereNull('route_deviate_intime')
                        ->orderBy('route_deviate_outtime', 'desc')
                        ->limit(1);
                    // $rd_qry = $this->db->query("select rd_id,vehicle_imei,route_id from route_deviate_report where vehicle_imei = '" . $key->deviceimei . "' AND client_id = '" . $key->client_id . "' AND (route_deviate_intime IS NULL) ORDER BY route_deviate_outtime DESC LIMIT 1");
                    $rd_fet = $rd_qry->first();
                    if (!empty($rd_fet)) {
                        $route_in_address = $this->get_address($key->lattitute, $key->longitute);
                        $deivate_data = array(
                            'route_deviate_intime' =>  $key->device_updatedtime,
                            "route_in_lat" => $key->lattitute,
                            "route_in_lng" => $key->longitute,
                            "route_in_location" => $route_in_address,
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        DB::table('routedeviation_reports')
                            ->where('id', array($rd_fet->id))
                            ->update($deivate_data);

                        dd("Updated");
                    }
                } else {
                    $rd_qry = DB::table('routedeviation_reports')
                        ->select('id', 'route_name', 'vehicle_imei', 'vehicle_name', 'route_deviate_outtime', 'route_deviate_intime', 'route_out_lat', 'route_out_lng', 'route_in_lat', 'route_in_lng')
                        ->where('vehicle_imei', $key->device_imei)
                        ->whereNull('route_deviate_intime')
                        ->orderBy('route_deviate_outtime', 'desc')
                        ->limit(1);
                    // $rd_qry = $this->db->query("select rd_id,vehicle_imei,route_id from route_deviate_report where vehicle_imei = '" . $key->deviceimei . "' AND client_id = '" . $key->client_id . "' AND (route_deviate_intime IS NULL) ORDER BY route_deviate_outtime DESC LIMIT 1");
                    $rd_fet = $rd_qry->first();
                    if (empty($rd_fet)) {
                        $route_out_address = $this->get_address($key->lattitute, $key->longitute);
                        DB::table('routedeviation_reports')->insert(
                            [
                                "route_deviate_outtime" => $key->device_updatedtime,
                                "route_out_lat" => $key->lattitute,
                                "route_out_lng" => $key->longitute,
                                "route_out_location" => $route_out_address,
                                "client_id" => 1,
                                "vehicle_imei" => $key->device_imei,
                                "route_name" => $key->route_name,
                                "vehicle_name" => $key->vehicle_name,
                                'created_at' => date('Y-m-d H:i:s')
                            ]
                        );
                        dd("inserted");
                    } else {
                        dd("Already Inserted");
                    }
                }
            }
        }
        // $this->complete_trip_routein_get();
    }

    function encodePolyline($points)
    {
        $encodedString = '';
        $previousLat = 0;
        $previousLng = 0;

        foreach ($points as $point) {
            $lat = $point[0]; // Latitude is the first element of the array.
            $lng = $point[1]; // Longitude is the second element of the array.

            $late5 = round($lat * 1e5);
            $lnge5 = round($lng * 1e5);

            $dLat = $late5 - $previousLat;
            $dLng = $lnge5 - $previousLng;

            $previousLat = $late5;
            $previousLng = $lnge5;

            $encodedString .= encodeSignedNumber($dLat) . encodeSignedNumber($dLng);
        }

        return $encodedString;
    }

    // function encodeSignedNumber($num)
    // {
    //     $sgn_num = $num << 1;
    //     if ($num < 0) {
    //         $sgn_num = ~($sgn_num);
    //     }
    //     return encodeNumber($sgn_num);
    // }

    // function encodeNumber($num)
    // {
    //     $encodeString = '';
    //     while ($num >= 0x20) {
    //         $nextValue = (0x20 | ($num & 0x1f)) + 63;
    //         $encodeString .= chr($nextValue);
    //         $num >>= 5;
    //     }
    //     $finalValue = $num + 63;
    //     $encodeString .= chr($finalValue);
    //     return $encodeString;
    // }

    function decodePolyline($encodedString)
    {
        $length = strlen($encodedString);
        $index = 0;
        $points = [];
        $lat = 0;
        $lng = 0;

        while ($index < $length) {
            $b = 0;
            $shift = 0;
            $result = 0;

            do {
                $b = ord($encodedString[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);

            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            $b = 0;
            $shift = 0;
            $result = 0;

            do {
                $b = ord($encodedString[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);

            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            $points[] = [$lat * 1e-5, $lng * 1e-5];
        }

        return $points;
    }

    function isPointInsidePolyline($point, $polyline)
    {
        $lat = $point['lat'];
        $lng = $point['lng'];

        $numPoints = count($polyline);
        $i = 0;
        $j = $numPoints - 1;
        $inside = false;

        for (; $i < $numPoints; $j = $i++) {
            $xi = $polyline[$i][1];
            $yi = $polyline[$i][0];
            $xj = $polyline[$j][1];
            $yj = $polyline[$j][0];

            $intersect = (($yi > $lat) != ($yj > $lat)) && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
    function isPointInsidePolylinewithRadius($lat, $lng, $points, $radiusArray)
    {
        // The Ray-Casting algorithm to check if a point is inside a polygon
        $inside = false;
        $count = count($points);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $points[$i][1];
            $yi = $points[$i][0];
            $xj = $points[$j][1];
            $yj = $points[$j][0];
            $radius = $radiusArray;
            print_r($radius);
            echo "<br>";
            // Check if the point is inside the circle around the current polyline point
            $distance = $this->haversineDistance($lat, $lng, $yi, $xi);
            $distance = round($distance * 1000);
            echo "<pre>";
            print_r($distance);
            echo "<br>";
            if ($distance <= $radius) {
                return true;
            }

            $intersect = (($yi > $lat) != ($yj > $lat)) &&
                ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
    function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers
        // Convert latitude and longitude from degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        // Calculate the differences between latitudes and longitudes
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        // Haversine formula
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        // dd($distance);
        return $distance;
    }

    public function trip_polyline_create()
    {
        date_default_timezone_set('Asia/Kolkata');

        $complete_list = DB::table('tripplan_reports AS tr')
            ->select('tr.trip_id', 'tr.created_at', 'tr.updated_at')
            ->where('tr.status', '=', 3)
            ->where('tr.polyline_flag', '=', 0)
            ->get();
        // dd($complete_list);

        if (!empty($complete_list)) {
            foreach ($complete_list as $list) {

                $start_time = Carbon::parse($list->created_at)->timestamp;
                $end_time = Carbon::parse($list->updated_at)->timestamp;
                $dateTime = Carbon::createFromTimestamp($start_time);
                $dateTime1 = Carbon::createFromTimestamp($end_time);
                $formattedDateTime = $dateTime->format('Y-m-d H:i:s');
                $formattedDateTime1 = $dateTime1->format('Y-m-d H:i:s');

                // DB::enableQueryLog();
                $playback = DB::table('play_back_histories')
                    ->select('latitude', 'longitude')
                    ->whereBetween('device_datetime', [$formattedDateTime, $formattedDateTime1])->get();
                // dd(DB::getQueryLog());
                // dd($playback);

                $polyline = $this->trip_plan_encoded($playback);
                dd($polyline);

                if ($polyline) {
                    DB::table('completed_polylines')->insert([
                        'trip_id' => $list->trip_id,
                        'polyline' => $polyline,
                    ]);
                }
            }
        }
    }

    public function create_polyline(Request $request)
    {
        $data = $request->all();

        $insert = DB::table('routes')->insert([
            'route_id' => $data['id'],
            'routename' => $data['route_name'],
            'route_start_lat' => $data['route_start_lat'],
            'route_start_lng' => $data['route_start_lng'],
            'route_end_lat' => $data['route_end_lat'],
            'route_end_lng' => $data['route_end_lng'],
            'route_polyline' => $data['encoded_latlngs'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($insert) {
            $data1['status'] = 1;
            $data1['message'] = 'Data Inserted SuccessFully';
            return $data1;
        } else {
            $data1['status'] = 1;
            $data1['message'] = 'Data Not Inserted';
            return $data1;
        }
    }
}
