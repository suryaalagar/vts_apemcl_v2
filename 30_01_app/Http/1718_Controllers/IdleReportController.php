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

        $idle_data = IdleReport::get();
        return view('report.idle_report',compact('idle_data'));
        // return view('report.idle_report');
    }

    // function get_data(Request $request)
    // {

    //     if ($request->ajax()) {
    //         dd("demo");
    //         $data = IdleReport::get();
    //         return view('report.idle_report', compact($data));
    //     }
    // }
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
