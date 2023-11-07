<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Http\Requests\StorevehicleRequest;
use App\Http\Requests\UpdatevehicleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromQuery;

use App\Exports\ExportVehicle;


class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vehicle.vehicle');
    }

    public function getData(Request $request)
    {

        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 => 'id'
        );

        $totalDataRecord = Vehicle::count();

        $totalFilteredRecord = $totalDataRecord;

        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');

        $start = $request->input('start') + 1;

        if (empty($request->input('search.value'))) {
            $post_data = Vehicle::offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();
        } else {
            $search_text = $request->input('search.value');

            $post_data =  Vehicle::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('device_no', 'LIKE', "%{$search_text}%")
                ->orWhere('start_location', 'LIKE', "%{$search_text}%")
                ->orWhere('end_location', 'LIKE', "%{$search_text}%")
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();

            $totalFilteredRecord = Vehicle::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('device_no', 'LIKE', "%{$search_text}%")
                ->orWhere('start_location', 'LIKE', "%{$search_text}%")
                ->orWhere('end_location', 'LIKE', "%{$search_text}%")
                ->count();
        }
       
        foreach ($post_data as $index => $data) {
            $serialNumber = $start + $index;
            $edit = '<a href="' . route('vehicle.edit', ['id' => $data->id]) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            $delete = '<a><i class="fa fa-trash " aria-hidden="true"></i></a>';

            $array_data[] = array(
                'S No' => $serialNumber,
                'vehicle_name' => $data->vehicle_name,
                'device_imei' => $data->device_imei,
                'sim_mob_no' => $data->sim_mob_no,
                'Action' => $edit . ' ' . $delete
            );
        }
        if (!empty($array_data)) {
            $draw_val = $request->input('draw');
            $get_json_data = array(
                "draw"            => intval($draw_val),
                "recordsTotal"    => intval($totalDataRecord),
                "recordsFiltered" => intval($totalFilteredRecord),
                "data"            => $array_data
            );

            echo json_encode($get_json_data);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('vehicle.vehicle_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorevehicleRequest $request)
    {

        // // $validator = Validator::make($request->all(), [
        // //     'sim_mob_no' => 'required|unique:vehicles,sim_mob_no|integer',
        // //     'device_imei' => 'required|unique:vehicles,device_imei|integer'
        // // ]);


        // // if ($validator->fails()) {

        // //     return response([
        // //         'message' => "validaton_error"
        // //     ]);
        // }

        try {

            DB::beginTransaction();
            $Vehicle = new Vehicle();
            $Vehicle->vehicle_name = $request->input('vehicle_name');
            $Vehicle->device_imei = $request->input('device_imei');
            $Vehicle->sim_mob_no = $request->input('sim_mob_no');
            $Vehicle->vehicle_type_id = $request->input('vehicle_type_id');
            $Vehicle->save();
            $last_id = $Vehicle->id;

            DB::table('live_data')->insert([
                'vehicle_id' => $last_id, // Replace with your actual column names and values
                'vehicle_name' => $request->input('vehicle_name'),
                'deviceimei' => $request->input('device_imei'),
                'vehicle_status' => 1,
            ]);
            DB::commit();

            return response(['message' => "Success"]);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => "Failure"]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $vehicle =  Vehicle::find($request->id);
        return view('vehicle.vehicle_edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatevehicleRequest $request, vehicle $vehicle)
    {
        // dd($request);
        $id = $request->input('id');
        $validator = Validator::make($request->all(), [
            'sim_mob_no' => 'required|integer|unique:vehicles,sim_mob_no,' . $id,
            'device_imei' => 'required|integer|unique:vehicles,device_imei,' . $id
        ]);

        if ($validator->fails()) {

            return response([
                'message' => "validaton_error"
            ]);
        }

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::find($request->id);
            $vehicle->vehicle_name = $request->vehicle_name;
            $vehicle->device_imei = $request->device_imei;
            $vehicle->sim_mob_no = $request->sim_mob_no;
            $vehicle->vehicle_type_id = $request->vehicle_type_id;
            $vehicle->save();

            DB::table('live_data')
            ->where('vehicle_id', $request->id)
            ->update([
                'vehicle_name' => $request->vehicle_name,
                'deviceimei' =>  $request->device_imei
            ]);

            DB::commit();
            return response(['message' => "Success"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response(['message' => "Failure"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(vehicle $vehicle)
    {
    }

    // public function sim_import(Request $request)
    // {
    //     $file_path = $request->input('file_path');
    //     if (!$file_path) {
    //         return $this->sendError("No File Path Provided");
    //     }

    //     $validator = Validator::make($request->all(), ['file_path' => 'required']);

    //     if ($validator->fails()) {
    //         return $this->sendError("Invalid File Format");
    //     }

    //     try {
    //         $path = $file_path;
    //         $data = array_map('str_getcsv', file($path));

    //         DB::beginTransaction();

    //         foreach ($data as $row) {
    //             $rowValidator = Validator::make($row, [
    //                 0 => 'required', // network_id
    //                 1 => 'required|unique:sims,sim_imei_no', // sim_imei_no (unique in 'sims' table)
    //                 2 => 'required|unique:sims,sim_mob_no', // sim_mob_no (unique in 'sims' table)
    //                 3 => 'required', // valid_from
    //                 4 => 'required', // valid_to
    //                 5 => 'required', // purchase_date
    //                 6 => 'required' // created_by
    //             ]);

    //             if ($rowValidator->fails()) {
    //                 DB::rollBack();
    //                 return $this->sendError($rowValidator->errors());
    //             }

    //             Sim::create([
    //                 'network_id' => $row[0],
    //                 'sim_imei_no' => $row[1],
    //                 'sim_mob_no' => $row[2],
    //                 'valid_from' => $row[3],
    //                 'valid_to' => $row[4],
    //                 'purchase_date' => $row[5],
    //                 'created_by' => $row[6]
    //             ]);
    //         }

    //         DB::commit();

    //         return $this->sendSuccess('Sim Imported Successfully');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
    //     }
    // }
    public function vehicle_export()
    {
        return Excel::download(new ExportVehicle, 'vehicles.xlsx');
    }
}
