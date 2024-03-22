<?php

namespace App\Http\Controllers;

use App\Models\LiveData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\TripplanReport;


class DashboardController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statusCounts = TripplanReport::groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        $planned_trips = $statusCounts[1] ?? 0;
        $process_trips = $statusCounts[2] ?? 0;
        $completed_trips = $statusCounts[3] ?? 0;
        return view('dashboard.index', compact('planned_trips', 'process_trips', 'completed_trips',));
    }

    public function get_apemcl_data(Request $request)
    {
        dd("hello");
    }

    public function all_vehicles(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $current_time = date("Y-m-d H:i:s");
        $query = LiveData::select(
            'vehicle_name',
            'deviceimei',
            'lattitute',
            'longitute',
            'ignition',
            'speed',
            'angle',
            DB::raw('TIMESTAMPDIFF(MINUTE, device_updatedtime, "' . $current_time . '") AS update_time')
        )->get();
        $result = [];
        foreach ($query as $key => $value) {
            $result[$key]['vehicle_name'] = $value->vehicle_name ?? "";
            $result[$key]['deviceimei'] = $value->deviceimei ?? "";
            $result[$key]['lattitute'] = $value->lattitute ?? 00.0000;
            $result[$key]['longitute'] = $value->longitute ?? 00.0000;
            $result[$key]['ignition'] = $value->ignition ?? 0;
            $result[$key]['speed'] = $value->speed ?? 0;
            $result[$key]['angle'] = $value->angle ?? 90;
            $result[$key]['update_time'] = $value->update_time ?? "";
        }
        return $result;
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
