<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\Models\TripplanReport;
use Illuminate\Support\Facades\DB;
// use DataTables;
// use Validator;

class TripplanReportController extends Controller
{

    public function index(Request $request)
    {

        // if ($request->ajax()) {

        // $data = TripplanReport::select('*');
        // return Datatables::of($data)
        //     ->addIndexColumn()
        //     ->addColumn('action', function($row){
        //         $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
        //         return $actionBtn;
        //     })
        //     ->rawColumns(['action'])
        //     ->make(true);
        //return view('report.trip_plan',compact($data));
        $trip_plans = TripplanReport::get();
        return view('report.trip_plan', compact('trip_plans'));
        // }

    }

    public function getData(Request $request)
    {

        $draw                 =         $request->get('draw'); // Internal use
        $start                 =         $request->get("start"); // where to start next records for pagination
        $rowPerPage         =         $request->get("length"); // How many recods needed per page for pagination

        $orderArray        =         $request->get('order');
        $columnNameArray     =         $request->get('columns'); // It will give us columns array

        $searchArray         =         $request->get('search');
        $columnIndex         =         $orderArray[0]['column'];  // This will let us know,
        // which column index should be sorted 
        // 0 = id, 1 = name, 2 = email , 3 = created_at

        $columnName         =         $columnNameArray[$columnIndex]['data']; // Here we will get column name, 
        // Base on the index we get

        $columnSortOrder     =         $orderArray[0]['dir']; // This will get us order direction(ASC/DESC)
        $searchValue         =         $searchArray['value']; // This is search value 


        $users = TripplanReport::count();
        $total = $users->count();

        $totalFilter = \DB::table('tripplan_reports');
        if (!empty($searchValue)) {
            $totalFilter = $totalFilter->where('client_id', 'like', '%' . $searchValue . '%');
            $totalFilter = $totalFilter->orWhere('vehicleid', 'like', '%' . $searchValue . '%');
            $totalFilter = $totalFilter->orWhere('vehicle_name', 'like', '%' . $searchValue . '%');
            $totalFilter = $totalFilter->orWhere('start_location', 'like', '%' . $searchValue . '%');
            $totalFilter = $totalFilter->orWhere('end_location', 'like', '%' . $searchValue . '%');
            $totalFilter = $totalFilter->orWhere('poc_number', 'like', '%' . $searchValue . '%');
            $totalFilter = $totalFilter->orWhere('route_name', 'like', '%' . $searchValue . '%');
        }
        $totalFilter = $totalFilter->count();


        $arrData = \DB::table('tripplan_reports');
        $arrData = $arrData->skip($start)->take($rowPerPage);
        $arrData = $arrData->orderBy($columnName, $columnSortOrder);
        if (!empty($searchValue)) {
            // $arrData = $arrData->where('name','like','%'.$searchValue.'%');
            // $arrData = $arrData->orWhere('email','like','%'.$searchValue.'%');
            $arrData = $arrData->where('client_id', 'like', '%' . $searchValue . '%');
            $arrData = $arrData->orWhere('vehicleid', 'like', '%' . $searchValue . '%');
            $arrData = $arrData->orWhere('vehicle_name', 'like', '%' . $searchValue . '%');
            $arrData = $arrData->orWhere('start_location', 'like', '%' . $searchValue . '%');
            $arrData = $arrData->orWhere('end_location', 'like', '%' . $searchValue . '%');
            $arrData = $arrData->orWhere('poc_number', 'like', '%' . $searchValue . '%');
            $arrData = $arrData->orWhere('route_name', 'like', '%' . $searchValue . '%');
        }

        $arrData = $arrData->get();

        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $totalFilter,
            "data" => $arrData,
        );

        return response()->json($response);
    }

    public function zigma_trip_plan()
    {

        $this->db->select('z.*,v.vehiclename,v.lat,v.lng,v.odometer,v.client_id,v.latlon_address,v.deviceimei,v.vehicleid');
        $this->db->from('zigma_plantrip as z');
        $this->db->join('vehicletbl as v', 'z.vehicleid = v.vehicleid', 'inner join');
        $where = '(z.status=1 OR z.status=2)';
        $this->db->where($where);
        $query = $this->db->get();
        $vehicle =  $query->result();
        foreach ($vehicle as $v_list) {
            if ($v_list->status == 1) {
                $start_geoid = $v_list->start_location;
                $get_geodata =  $this->cron_test_model->get_geodata($start_geoid);

                $latitude1 = $get_geodata->g_lat;
                $longitude1 = $get_geodata->g_lng;
                $latitude2 = $v_list->lat;
                $longitude2 = $v_list->lng;
                $radius = $get_geodata->radius;
                $distance = $this->geo_distance($latitude1, $longitude1, $latitude2, $longitude2);

                if ($distance < $radius) {

                    $data1 = array('geo_status' => 1);
                    $this->db->where('trip_id', $v_list->trip_id);
                    $this->db->where('client_id', $v_list->client_id);
                    $this->db->where('vehicleid', $v_list->vehicleid);
                    // $this->db->where('poc_number',$v_list->poc_number);
                    $this->db->update('zigma_plantrip', $data1);
                } elseif (($distance > $radius) && ($v_list->geo_status == 1)) {

                    $data = array(
                        'trip_id' => $v_list->trip_id,
                        'trip_date' => $v_list->created_date,
                        'client_id' => $v_list->client_id,
                        'vehicle_id' => $v_list->vehicleid,
                        'poc_number' => $v_list->poc_number,
                        'deviceimei' => $v_list->deviceimei,
                        'vehicle_name' => $v_list->vehiclename,
                        'start_geo_id' => $v_list->start_location,
                        'start_location' => $v_list->latlon_address,
                        'end_geo_id' => $v_list->end_location,
                        'start_odometer' => $v_list->odometer,
                        's_lat' => $v_list->lat,
                        's_lng' => $v_list->lng,
                        'flag' => 2,
                        'create_datetime' => date('Y-m-d H:i:s'),
                        'plam_duration' => $v_list->pl_duration,
                        'plan_km' => $v_list->pl_km,
                    );


                    $this->db->insert('zigma_plantrip_report1', $data);

                    $data1 = array('status' => 2, 'geo_status' => 0,);
                    $this->db->where('trip_id', $v_list->trip_id);
                    $this->db->where('vehicleid', $v_list->vehicleid);
                    $this->db->where('client_id', $v_list->client_id);
                    $this->db->update('zigma_plantrip', $data1);
                }
            } else {
                $start_geoid = $v_list->start_location;
                $end_geoid = $v_list->end_location;
                $get_geodata =  $this->cron_test_model->get_geodata($end_geoid);
                $latitude1 = $get_geodata->g_lat;
                $longitude1 = $get_geodata->g_lng;
                $latitude2 = $v_list->lat;
                $longitude2 = $v_list->lng;
                $radius = $get_geodata->radius;
                $distance = $this->geo_distance($latitude1, $longitude1, $latitude2, $longitude2);
                echo '<br>' . $distance . '<br>';
                $delay_status = $v_list->delay_status;
                $pl_duration = $v_list->pl_duration;
                $pl_duration = str_replace(':', '.', $pl_duration);
                $pl_duration  = floatval($pl_duration);
                $start_odometer_data =  $this->cron_test_model->getstart_odometer($v_list->trip_id, $v_list->client_id, $v_list->vehicleid);
                $start_datetime = $start_odometer_data->create_datetime;
                if ($delay_status == 0 and $pl_duration != '') {
                    $datetime1 = new DateTime();
                    $datetime2 = new DateTime($start_datetime);
                    $interval = $datetime1->diff($datetime2);
                    $diff_hours = $interval->format('%h.%i');
                    $diff_hours  = floatval($diff_hours);
                    if ($diff_hours > $pl_duration) {
                        $sms_data = array(
                            "lat" => $latitude2,
                            "lng" => $longitude1,
                            "createdon" => date('Y-m-d H:i:s'),
                            "vehicle_id" => $v_list->vehicleid,
                            "client_id" => $v_list->client_id,
                            'type_id' => 5,
                            "all_status" => 59,
                            "show_status" => 1,
                            "sms_status" => 1,
                        );
                        $this->db->insert('sms_alert', $sms_data);
                        $data1 = array('delay_status' => 1);
                        $this->db->where('vehicleid', $v_list->vehicleid);
                        $this->db->where('trip_id', $v_list->trip_id);
                        $this->db->where('client_id', $v_list->client_id);
                        $this->db->update('zigma_plantrip', $data1);
                    }
                }
                if ($distance < $radius) {
                    $start_odo = $start_odometer_data->start_odometer;
                    $distance_value = $v_list->odometer - $start_odo;
                    $distance_value = ($distance_value > 0) ? $distance_value : 0;
                    $end_datetime = date('Y-m-d H:i:s');
                    $idle_data = $this->cron_test_model->smart_idleday($v_list->deviceimei, $start_datetime, $end_datetime);
                    $park_data = $this->cron_test_model->smart_parkday($v_list->deviceimei, $start_datetime, $end_datetime);
                    $idle_duration = $idle_data->idel_duration;
                    $hours = floor($idle_duration / 60);
                    $min = $idle_duration - ($hours * 60);
                    $min = floor((($min -   floor($min / 60) * 60)) / 6);
                    $second = $idle_duration % 60;
                    $idle_hrs = $hours . " Hours " . $min . " Minutes";

                    $park_duration = $park_data->parking_duration;
                    $hours = floor($park_duration / 60);
                    $min = $park_duration - ($hours * 60);
                    $min = floor((($min -   floor($min / 60) * 60)) / 6);
                    $second = $park_duration % 60;
                    $park_hrs = $hours . " Hours " . $min . " Minutes";

                    $data = array(
                        'end_odometer' => $v_list->odometer,
                        'distance' => $distance_value,
                        'e_lat' => $v_list->lat,
                        'e_lng' => $v_list->lng,
                        'end_location' => $v_list->latlon_address,
                        'manual_idle_dur' => $idle_hrs,
                        'parking_duration' => $park_hrs,
                        'flag' => 3,
                        'updated_datetime' => date('Y-m-d H:i:s')
                    );

                    $this->db->where('trip_id', $v_list->trip_id);
                    $this->db->where('client_id', $v_list->client_id);
                    $this->db->where('vehicle_id', $v_list->vehicleid);
                    $this->db->update('zigma_plantrip_report1', $data);
                    $data1 = array('status' => 3);
                    $this->db->where('vehicleid', $v_list->vehicleid);
                    $this->db->where('trip_id', $v_list->trip_id);
                    $this->db->where('client_id', $v_list->client_id);
                    $this->db->update('zigma_plantrip', $data1);

                    // Update Route Deviation Is Completed Then Change Default Status
                    $v_data = array('route_deviate_sms' => 0);
                    $this->db->where('deviceimei', $v_list->deviceimei);
                    $this->db->update('vehicletbl', $v_data);

                    $trip_idle_data = $this->cron_test_model->idle_report_list($start_datetime, $end_datetime, $v_list->deviceimei, 60, $start_geoid, $end_geoid);
                    $this->db->insert_batch('trip_plan_idle_report', $trip_idle_data);
                }
            }
        }
    }

    // public function trip_plan()
    // {
    //     $tripplan = DB::table('tripplan_reports')
    //         ->join('live_data AS l', 'l.vehicle_id', '=', 'tripplan_reports.vehicleid')
    //         ->where('tripplan_reports.status', '=', '1')
    //         ->select('tripplan_reports.*', 'l.lattitute as vehicle_lat', 'l.longitute as vehicle_lng', 'l.odometer', 'l.deviceimei',)
    //         ->get()->toArray();

    //     foreach ($tripplan as $trip) {

    //         if ($trip->status == 1) {

    //             $latitude1 = $trip->s_lat;
    //             $longitude1 = $trip->s_lat;
    //             $latitude2 = $trip->vehicle_lat;
    //             $longitude2 = $trip->vehicle_lng;
    //             $radius = 200;
    //             $distance = $this->geo_distance($latitude1, $longitude1, $latitude2, $longitude2);

    //             if ($distance < $radius) {
    //                 $data1 = array('geo_status' => 1);
    //                 $this->db->where('trip_id', $trip->trip_id);
    //                 $this->db->where('vehicleid', $trip->vehicleid);
    //                 $this->db->update('tripplan_reports', $data1);
    //             } elseif (($distance > $radius) && ($trip->geo_status == 1)) {
    //                 $data1 = array(
    //                     'trip_date' => $trip->created_date,
    //                     'deviceimei' => $trip->deviceimei,
    //                     'vehicle_name' => $trip->vehiclename,
    //                     'start_odometer' => $trip->odometer,
    //                     'flag' => 2,
    //                     'create_datetime' => date('Y-m-d H:i:s'),
    //                     'status' => 2,
    //                     'geo_status' => 0,
    //                 );
    //                 $this->db->where('trip_id', $trip->trip_id);
    //                 $this->db->where('vehicleid', $trip->vehicleid);
    //                 $this->db->update('zigma_plantrip', $data1);
    //             }
    //         }
    //     }
    // }

    // public function geo_distance($latitude1, $longitude1, $latitude2, $longitude2)
    // {
    //     $dis_query =  DB::select("SELECT find_distance('{$latitude1}', '{$longitude1}', '{$latitude2}','{$longitude2}') as distance;");
    //     $result = $dis_query;
    //     $wp_dis = $result[0]->distance;
    //     $v_dis = $wp_dis * 1000;
    //     return  $v_dis = round($v_dis);
    // }

    public function trip_plan()
    {
        $tripplan = DB::table('tripplan_reports')
            ->join('live_data AS l', 'l.vehicle_id', '=', 'tripplan_reports.vehicleid')
            ->whereIn('tripplan_reports.status', [1, 2])
            ->select('tripplan_reports.*', 'l.lattitute as vehicle_lat', 'l.longitute as vehicle_lng', 'l.odometer', 'l.deviceimei',)
            ->get()->toArray();
        $first_geo_status_arr = array();
        foreach ($tripplan as $trip) {
            if ($trip->status == 1) {
                $start_lat = $trip->s_lat;
                $start_lng = $trip->s_lat;
                $vehicle_lat = $trip->vehicle_lat;
                $vehicle_lng = $trip->vehicle_lng;
                $radius = 200;
                $distance_data = $this->calculateDistance($start_lat, $start_lng, $vehicle_lat, $vehicle_lng, $radius);
                if ($distance_data['location_status
                '] == 1) {
                    $first_geo_status_arr[] = array($trip->trip_id);
                } elseif (($distance_data['location_status'] == 2) && ($trip->geo_status == 1)) {
                    $processing_arr = array(
                        'trip_date' => $trip->created_date,
                        'deviceimei' => $trip->deviceimei,
                        'vehicle_name' => $trip->vehiclename,
                        'start_odometer' => $trip->odometer,
                        'flag' => 2,
                        'create_datetime' => date('Y-m-d H:i:s'),
                        'status' => 2,
                        'geo_status' => 0
                    );
                    DB::table('tripplan_reports')->whereIn('trip_id', $trip->trip_id)
                        ->update($processing_arr);
                } elseif ($trip->status == 2) {
                    $end_lat = $trip->e_lat;
                    $e_lng = $trip->e_lat;
                    $distance_data = $this->calculateDistance($end_lat, $e_lng, $vehicle_lat, $vehicle_lng, $radius);
                    if ($distance_data['location_status'] == 1) {
                        $end_trip_arr = array(
                            'end_odometer' => $trip->odometer,
                            'distance' => $distance_data['distance'],
                            'end_location' => $trip->latlon_address,
                            'manual_idle_dur' => '',
                            'parking_duration' => '',
                            'flag' => 3,
                            'updated_datetime' => date('Y-m-d H:i:s')
                        );
                        DB::table('tripplan_reports')->whereIn('trip_id', $trip->trip_id)
                            ->update($end_trip_arr);
                    }
                }
            }
        }
        if (count($first_geo_status_arr) > 0 && !empty($first_geo_status_arr)) {
            DB::table('tripplan_reports')->whereIn('trip_id', $first_geo_status_arr)
                ->update(['geo_status' => 1]);
        }
    }
    function calculateDistance($lat1, $lon1, $lat2, $lon2, $radius)
    {
        // Calculate Distance Formula
        $earthRadius = 6371; // Earth's radius in kilometers
        $latd1 = deg2rad($lat1);
        $lond1 = deg2rad($lon1);
        $latd2 = deg2rad($lat2);
        $lond2 = deg2rad($lon2);
        // Haversine formula
        $deltaLat = $latd2 - $latd1;
        $deltaLon = $lond2 - $lond1;
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($latd1) * cos($latd2) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        $distance = round($distance * 1000);
        if ($distance < $radius)
            $status =  1;
        else
            $status =  2;
        $distance_data = array(
            'distance' => $distance,
            'location_status' => $status
        );
        return $distance_data;
    }
}
