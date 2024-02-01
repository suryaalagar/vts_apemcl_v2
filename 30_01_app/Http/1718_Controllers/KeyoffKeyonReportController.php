<?php

namespace App\Http\Controllers;

use App\Models\KeyoffKeyonReport;
use App\Http\Requests\StoreKeyoffKeyonReportRequest;
use App\Http\Requests\UpdateKeyoffKeyonReportRequest;

class KeyoffKeyonReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keyonkeyoff_data = KeyoffKeyonReport::get();
        return view('report.keyonkeyoff_report',compact('keyonkeyoff_data'));
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
