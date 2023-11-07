<?php

namespace App\Http\Controllers;

use App\Models\PlayBackHistoryReport;
use App\Http\Requests\StorePlayBackHistoryReportRequest;
use App\Http\Requests\UpdatePlayBackHistoryReportRequest;

class PlayBackHistoryReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $playback_data = PlayBackHistoryReport::get();
        return view('report.playbackhistory',compact('playback_data'));
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
