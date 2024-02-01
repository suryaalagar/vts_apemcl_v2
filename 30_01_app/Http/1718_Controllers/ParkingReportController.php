<?php

namespace App\Http\Controllers;

use App\Models\ParkingReport;
use App\Http\Requests\StoreParkingReportRequest;
use App\Http\Requests\UpdateParkingReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parking_data = ParkingReport::get();
        return view('report.parking_report', compact('parking_data'));
        // return view('report.parking_report');
        // dd();
    }

    public function getData(Request $request)
    {
        // dd($request->all());

        // $columns = ['id', 'vehiclename', 'start_location', 'end_location', 'start_time', 'end_time', 'duration', 'created_at'];
        // $query = DB::select($columns);
        // dd($query);
        // $columnIndex = $request->input('order.0.column');
        // $columnName = $columns[$columnIndex];
        // $columnDirection = $request->input('order.0.dir');
        // $query->orderBy($columnName, $columnDirection);
        // $data = $query->paginate(10);

        // return response()->json($data);

        // dd($request->get('order'));
        $draw                 =         $request->get('draw'); // Internal use
        $start                 =         $request->get("start"); // where to start next records for pagination
        $rowPerPage         =         $request->get("length"); // How many recods needed per page for pagination

        $orderArray         =         $request->get('order');
        $columnNameArray     =         $request->get('columns'); // It will give us columns array

        $searchArray         =         $request->get('search');
        $columnIndex         =         $orderArray[0]['column'];  // This will let us know,
        // which column index should be sorted 
        // 0 = id, 1 = name, 2 = email , 3 = created_at

        $columnName         =         $columnNameArray[$columnIndex]['data']; // Here we will get column name, 
        // Base on the index we get

        $columnSortOrder     =         $orderArray[0]['dir']; // This will get us order direction(ASC/DESC)
        $searchValue         =         $searchArray['value']; // This is search value 


        $parking_reports = ParkingReport::all();
        $total = $parking_reports->count();

        $totalFilter = ParkingReport::all();
        if (!empty($searchValue)) {
            $totalFilter = $totalFilter->where('name', 'like', '%' . $searchValue . '%');
            $totalFilter = $totalFilter->orWhere('email', 'like', '%' . $searchValue . '%');
        }
        $totalFilter = $totalFilter->count();


        $arrData = ParkingReport::all();
        $arrData = $arrData->skip($start)->take($rowPerPage);
        $arrData = $arrData->orderBy($columnName, $columnSortOrder);

        // if (!empty($searchValue)) {
        //     $arrData = $arrData->where('name', 'like', '%' . $searchValue . '%');
        //     $arrData = $arrData->orWhere('email', 'like', '%' . $searchValue . '%');
        // }

        $arrData = $arrData->get();
    //    print_r($arrData);die;
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $totalFilter,
            "data" => $arrData,
        );

        return response()->json($response);
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
    public function store(StoreParkingReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ParkingReport $parkingReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParkingReport $parkingReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParkingReportRequest $request, ParkingReport $parkingReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParkingReport $parkingReport)
    {
        //
    }
}
