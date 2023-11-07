<?php

namespace App\Http\Controllers;

use App\Models\ParkingReport;
use App\Http\Requests\StoreParkingReportRequest;
use App\Http\Requests\UpdateParkingReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Service\AddressService;


class ParkingReportController extends Controller
{
    use AddressService;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $from_date = date('Y-m-d H:i:s', strtotime('00:00:00'));
        $to_date = date('Y-m-d H:i:s', strtotime('23:59:59'));
        $parking_data = ParkingReport::get();
        return view('report.parking_report', compact('parking_data', 'from_date', 'to_date'));
    }

    public function getData(Request $request)
    {
        $fromdate = date('Y-m-d H:i:s', strtotime($request->input('fromdate')));
        $todate = date('Y-m-d H:i:s', strtotime($request->input('todate')));
        $address =  $request->input('active');
        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 => 'id'
        );
        $start = $request->input('start') + 1;

        $totalDataRecord = ParkingReport::count();

        $totalFilteredRecord = $totalDataRecord;

        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $post_data = DB::table('parking_reports AS p')
                ->offset($start_val)
                ->whereBetween('p.start_datetime', [$fromdate, $todate])
                ->whereRaw('TIMEDIFF(p.end_datetime, p.start_datetime) > "00:00:00"')
                ->limit($limit_val)
                // ->orderBy($order_val, $dir_val)
                ->orderBy('p.id', 'asc')
                ->select('p.vehicle_id', 'v.vehicle_name', 'p.device_imei', 'p.end_datetime', 'p.start_datetime', 'p.start_latitude', 'p.start_longitude', DB::raw('TIMEDIFF(p.end_datetime, p.start_datetime) AS time_difference'))
                ->join('vehicles AS v', 'v.id', '=', 'p.vehicle_id')
                ->get();
        } else {
            $search_text = $request->input('search.value');

            $post_data =  ParkingReport::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('device_imei', 'LIKE', "%{$search_text}%")
                ->orWhere('start_datetime', 'LIKE', "%{$search_text}%")
                ->orWhere('start_datetime', 'LIKE', "%{$search_text}%")
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->select('vehicle_id', 'device_imei', 'start_datetime', 'end_datetime', 'start_latitude', 'start_longitude', DB::raw('TIMEDIFF(end_datetime, start_datetime) AS time_difference'))
                ->get();

            $totalFilteredRecord = ParkingReport::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('device_no', 'LIKE', "%{$search_text}%")
                ->orWhere('start_location', 'LIKE', "%{$search_text}%")
                ->orWhere('end_location', 'LIKE', "%{$search_text}%")
                ->count();
        }

        if (!empty($post_data)) {
            $draw_val = $request->input('draw');
            $get_json_data = array(
                "draw"            => intval($draw_val),
                "recordsTotal"    => intval($totalDataRecord),
                "recordsFiltered" => intval($totalFilteredRecord),
                "data"            => $post_data
            );
            foreach ($post_data  as $index =>  $data) {
                $serialNumber = $start + $index;
                $park_address = "Loading...";
                if ($address == 1) {
                    $park_address = $this->get_address($data->start_latitude, $data->start_longitude);
                }
                $edit = '<button type="button" class="btn btn-success showModal"  onclick="parking_data(' . "$data->start_latitude" . "," . "$data->start_longitude" . "," . ');" >Map View</button>';

                $array_data[] = array(
                    'S No' => $serialNumber,
                    'vehicle_name' => $data->vehicle_name,
                    'device_imei' => $data->device_imei,
                    'start_datetime' => $data->start_datetime,
                    'end_datetime' => $data->end_datetime,
                    'duration' => $data->time_difference,
                    'park_address' => $park_address,
                    'Action' => $edit
                );
            }
            if (!empty($array_data)) {
                $draw_val = $request->input('draw');
                $get_json_data = array(
                    "draw"            => intval($draw_val),
                    "recordsTotal"    => intval($totalDataRecord),
                    "recordsFiltered" => intval($totalFilteredRecord),
                    "data"            => $array_data
                );

                echo json_encode($get_json_data);
            }
        }
    }

    public function get_address_modal(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        // $url = "http://198.204.245.190/nominatim/reverse?format=jsonv2&lat=$Lattitute&lon=$Longitute";
        $url = "http://69.197.153.82:8080/reverse?format=jsonv2&lat=$latitude&lon=$longitude";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true); // decode the JSON response into a PHP associative array
        // $display_name = [];
        $display_name = $data['display_name'];
        return response()->json(['address' => $display_name]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParkingReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ParkingReport $parkingReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParkingReport $parkingReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParkingReportRequest $request, ParkingReport $parkingReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParkingReport $parkingReport)
    {
        //
    }
}
