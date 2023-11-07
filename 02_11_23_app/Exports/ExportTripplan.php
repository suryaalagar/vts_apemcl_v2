<?php

namespace App\Exports;

use App\Models\TripplanReport;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\FromQuery;
// use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
// use Illuminate\Contracts\View\FromView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ExportTripplan implements FromView
{
    private $fromdate;
    private $todate;

    public function __construct($fromdate, $todate)
    {
        $this->fromdate = $fromdate;
        $this->todate = $todate;
    }

    public function view(): \Illuminate\Contracts\View\View
    {

        $from_date = $this->fromdate;
        $to_date = $this->todate;
        $query = TripplanReport::whereBetween('trip_date', [$from_date, $to_date]);
        $result = $query->orderby('trip_id', 'asc')->get();
        return view('exports.tripplan_exports', [
            'result' => $result,
        ]);
    }
}
