<?php

namespace App\Http\Controllers;

use App\Models\PlayBackHistoryReport;
use App\Http\Requests\StorePlayBackHistoryReportRequest;
use App\Http\Requests\UpdatePlayBackHistoryReportRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PlayBackHistoryReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $playback_data = PlayBackHistoryReport::get();
        $from_date = date('Y-m-d H:i:s', strtotime('00:00:00'));
        $to_date = date('Y-m-d H:i:s', strtotime('23:59:59'));
        $vehicle = DB::table('vehicles')->select('device_imei', 'vehicle_name')->where('status', '=', '1')->get();
        return view('report.playbackhistory', compact('playback_data', 'from_date', 'to_date', 'vehicle'));
    }

    public function get_history(Request $request)
    {
        $vehicle = date($request->input('vehicle'));
        $fromdate = date('Y-m-d H:i:s', strtotime($request->input('from_date')));
        $todate = date('Y-m-d H:i:s', strtotime($request->input('to_date')));

        $query = DB::table('play_back_histories AS p')->whereBetween('p.device_datetime', [$fromdate, $todate])
        ->join('vehicles AS v', 'v.device_imei', '=', 'p.device_imei')
        ->where('p.device_imei','=',"$vehicle")
        ->select('p.*','v.vehicle_name')
        ->get()->toArray();

        return response()->json($query);
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
    public function store(StorePlayBackHistoryReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PlayBackHistoryReport $playBackHistoryReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlayBackHistoryReport $playBackHistoryReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlayBackHistoryReportRequest $request, PlayBackHistoryReport $playBackHistoryReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlayBackHistoryReport $playBackHistoryReport)
    {
        //
    }
}
