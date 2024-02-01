<?php

namespace App\Http\Controllers;

use App\Models\CronJob;
use App\Http\Requests\StoreCronJobRequest;
use App\Http\Requests\UpdateCronJobRequest;

class CronJobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function add_tripplan(){
        
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
    public function store(StoreCronJobRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CronJob $cronJob)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CronJob $cronJob)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCronJobRequest $request, CronJob $cronJob)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CronJob $cronJob)
    {
        //
    }
}
