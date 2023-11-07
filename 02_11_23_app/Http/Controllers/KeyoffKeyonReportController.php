<?php

namespace App\Http\Controllers;

use App\Models\KeyoffKeyonReport;
use App\Http\Requests\StoreKeyoffKeyonReportRequest;
use App\Http\Requests\UpdateKeyoffKeyonReportRequest;
use Illuminate\Http\Request;

class KeyoffKeyonReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $keyonkeyoff_data = KeyoffKeyonReport::get();
        // return view('report.keyonkeyoff_report',compact('keyonkeyoff_data'));
        return view('report.keyonkeyoff_report');
    }

    public function getData(Request $request)
    {

        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 => 'id'
        );

        $totalDataRecord = KeyoffKeyonReport::count();

        $totalFilteredRecord = $totalDataRecord;

        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $post_data = KeyoffKeyonReport::offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();
        } else {
            $search_text = $request->input('search.value');

            $post_data =  KeyoffKeyonReport::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('device_no', 'LIKE', "%{$search_text}%")
                ->orWhere('start_location', 'LIKE', "%{$search_text}%")
                ->orWhere('end_location', 'LIKE', "%{$search_text}%")
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();

            $totalFilteredRecord = KeyoffKeyonReport::
                where('id', 'LIKE', "%{$search_text}%")
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

            echo json_encode($get_json_data);
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
    public function store(StoreKeyoffKeyonReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(KeyoffKeyonReport $keyoffKeyonReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KeyoffKeyonReport $keyoffKeyonReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKeyoffKeyonReportRequest $request, KeyoffKeyonReport $keyoffKeyonReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KeyoffKeyonReport $keyoffKeyonReport)
    {
        //
    }
}
