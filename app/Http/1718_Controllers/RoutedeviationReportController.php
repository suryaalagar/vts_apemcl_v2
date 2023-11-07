<?php

namespace App\Http\Controllers;

use App\Models\RoutedeviationReport;
use App\Models\DemoPolyline;
use App\Http\Requests\StoreRoutedeviationReportRequest;
use App\Http\Requests\UpdateRoutedeviationReportRequest;


class RoutedeviationReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routedeviation_data = RoutedeviationReport::get();
        $polyline_data = DemoPolyline::first();
        return view('report.routedeviation_report',compact('routedeviation_data','polyline_data'));
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
    public function store(StoreRoutedeviationReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RoutedeviationReport $routedeviationReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoutedeviationReport $routedeviationReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoutedeviationReportRequest $request, RoutedeviationReport $routedeviationReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoutedeviationReport $routedeviationReport)
    {
        //
    }
}
