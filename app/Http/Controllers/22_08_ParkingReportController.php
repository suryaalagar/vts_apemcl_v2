<?php

namespace App\Http\Controllers;

use App\Models\ParkingReport;
use App\Http\Requests\StoreParkingReportRequest;
use App\Http\Requests\UpdateParkingReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parking_data = ParkingReport::get();
        return view('report.parking_report', compact('parking_data'));
        // return view('report.parking_report');
        // dd();
    }

    public function getData(Request $request)
    {

        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 => 'id'
        );

        $totalDataRecord = ParkingReport::count();

        $totalFilteredRecord = $totalDataRecord;

        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $post_data = ParkingReport::offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();
        } else {
            $search_text = $request->input('search.value');

            $post_data =  ParkingReport::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('device_no', 'LIKE', "%{$search_text}%")
                ->orWhere('start_location', 'LIKE', "%{$search_text}%")
                ->orWhere('end_location', 'LIKE', "%{$search_text}%")
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
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
            $count = 1;
            foreach ($post_data as $data) {
                $edit = '<button type="button" class="btn btn-success showModal"
                data-toggle="modal" data-target="#myModal"
                data-lat="' . $data->start_latitude . '" data-lng="' . $data->start_longitude . '">
                Map View
            </button>';
                // $delete = '<a><i class="fa fa-trash " aria-hidden="true"></i></a>';

                $array_data[] = array(
                    'S No' => $count++,
                    'vehicle_id' => $data->vehicle_id,
                    'device_imei' => $data->device_imei,
                    'start_datetime' => $data->start_datetime,
                    'end_datetime' => $data->end_datetime,
                    // 'duration' => $data->end_datetime - $data->start_datetime,
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
