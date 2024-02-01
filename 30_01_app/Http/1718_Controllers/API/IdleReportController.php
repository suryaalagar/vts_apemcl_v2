<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\IdleReport;

class IdleReportController extends BaseController
{
    public function index()
    {
        $idle_data = IdleReport::get();
        if ($idle_data->isEmpty()) {
            return  $this->send_Error("No Data Found");
        }
        return $this->send_Success($idle_data);
        // return view('report.idle_report',compact('idle_data'));
        // return view('report.idle_report');
    }
}
