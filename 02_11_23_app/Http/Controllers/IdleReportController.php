<?php

namespace App\Http\Controllers;

use App\Models\IdleReport;
use App\Http\Requests\StoreIdleReportRequest;
use App\Http\Requests\UpdateIdleReportRequest;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request;

class IdleReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $from_date = date('Y-m-d H:i:s', strtotime('00:00:00'));
        $to_date = date('Y-m-d H:i:s', strtotime('23:59:59'));
        return view('report.idle_report', compact('from_date', 'to_date'));
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

        $totalDataRecord = IdleReport::count();

        $totalFilteredRecord = $totalDataRecord;

        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $post_data = IdleReport::offset($start_val)
                ->whereBetween('start_datetime', [$fromdate, $todate])
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->select('vehicle_id', 'device_imei', 'start_datetime', 'end_datetime', 'start_latitude', 'start_longitude', DB::raw('TIMEDIFF(end_datetime, start_datetime) AS time_difference'))
                ->get();
        } else {
            $search_text = $request->input('search.value');

            $post_data =  IdleReport::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('device_no', 'LIKE', "%{$search_text}%")
                ->orWhere('start_location', 'LIKE', "%{$search_text}%")
                ->orWhere('end_location', 'LIKE', "%{$search_text}%")
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->select('vehicle_id', 'device_imei', 'start_datetime', 'end_datetime', 'start_latitude', 'start_longitude', DB::raw('TIMEDIFF(end_datetime, start_datetime) AS time_difference'))
                ->get();

            $totalFilteredRecord = IdleReport::where('id', 'LIKE', "%{$search_text}%")
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
            $count = 1;
            foreach ($post_data  as $index =>  $data) {
                $serialNumber = $start + $index;
                $park_address = "Loading...";
                if ($address == 1) {
                    $park_address = $this->get_address($data->start_latitude, $data->start_longitude);
                }
                $edit = '<button type="button" class="btn btn-success showModal"  onclick="parking_data(' . "$data->start_latitude" . "," . "$data->start_longitude" . ');" >Map View</button>';
                // $delete = '<a><i class="fa fa-trash " aria-hidden="true"></i></a>';

                $array_data[] = array(
                    'S No' => $serialNumber,
                    'vehicle_id' => $data->vehicle_id,
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
    public function store(StoreIdleReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(IdleReport $idleReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IdleReport $idleReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdleReportRequest $request, IdleReport $idleReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IdleReport $idleReport)
    {
        //
    }
}
