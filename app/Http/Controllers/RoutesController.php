<?php

namespace App\Http\Controllers;

use App\Models\Routes;
use App\Http\Requests\StoreRoutesRequest;
use App\Http\Requests\UpdateRoutesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('route.route');
    }


    public function getData(Request $request)
    {

        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 => 'id'
        );

        $totalDataRecord = Routes::count();

        $totalFilteredRecord = $totalDataRecord;

        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');

        $start = $request->input('start') + 1;

        if (empty($request->input('search.value'))) {
            $post_data = Routes::offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();
        } else {
            $search_text = $request->input('search.value');

            $post_data =  Routes::where('id', 'LIKE', "%{$search_text}%")
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();

            $totalFilteredRecord = Routes::where('id', 'LIKE', "%{$search_text}%")
                ->count();
        }
        // $count = 1;
        foreach ($post_data as $index => $data) {
            $serialNumber = $start + $index;
            $edit = '<a href="' . route('vehicle.edit', ['id' => $data->id]) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            $delete = '<a><i class="fa fa-trash " aria-hidden="true"></i></a>';

            $start_lat_lng = $data->route_start_lat.','. $data->route_start_lng ; 
            $end_lat_lng = $data->route_end_lat.','. $data->route_end_lng ; 
            
            $array_data[] = array(
                'S No' => $serialNumber,
                'routename' => $data->routename,
                'route_start_locationname' => $data->route_start_locationname,
                'route_end_locationname' => $data->route_end_locationname,
                'start_lat_lng' => $start_lat_lng,
                'end_lat_lng' => $end_lat_lng,
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
    public function route_create(Request $request)
    {
        return view('route.route_create');
    }


    public function route_storedddd(Request $request)
    {
        echo 'cagagag';
    }
    /**
     * Store a newly created resource in storage.
     */
    public function route_store(StoreRoutesRequest $request)
    {
        try {

            DB::beginTransaction();


            $startlatlang = explode(",", trim($request->input('startlatlang')));
            $endlatlang = explode(",", trim($request->input('endlatlang')));

            DB::table('routes')->insert([
                'routename' => $request->input('route_name'),
                'route_start_locationname' => $request->input('startlocation'),
                'route_end_locationname' => $request->input('endlocation'),
                'route_start_lat' => $startlatlang[0],
                'route_start_lng' => $startlatlang[1],
                'route_end_lat' => $endlatlang[0],
                'route_end_lng' => $endlatlang[1],
                'route_polyline' => $request->input('encoded'),
                'created_at' => date('Y-m-d H:i:s')
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
    public function show(Routes $routes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Routes $routes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoutesRequest $request, Routes $routes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Routes $routes)
    {
        //
    }
}
