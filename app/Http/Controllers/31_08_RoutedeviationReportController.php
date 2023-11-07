<?php

namespace App\Http\Controllers;

use App\Models\RoutedeviationReport;
use App\Models\DemoPolyline;
use App\Http\Requests\StoreRoutedeviationReportRequest;
use App\Http\Requests\UpdateRoutedeviationReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// use App\Http\Service\AddressService;
use App\Service\AddressService;
// use App\Service\AddressService\get_address;

class RoutedeviationReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AddressService;

    public function index()
    {

        // dd($data);
        // $routedeviation_data = RoutedeviationReport::get();
        // $polyline_data = DemoPolyline::first();
        // return view('report.routedeviation_report',compact('routedeviation_data','polyline_data'));
        return view('report.routedeviation_report');
    }

    public function getData(Request $request)
    {



        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 => 'id'
        );

        $totalDataRecord = RoutedeviationReport::count();

        $totalFilteredRecord = $totalDataRecord;

        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');

        $start = $request->input('start') + 1;

        if (empty($request->input('search.value'))) {
            $post_data = DB::table('routedeviation_reports')
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->select('id', 'route_name', 'vehicle_imei', 'vehicle_name', 'route_deviate_outtime', 'route_deviate_intime', 'route_out_lat', 'route_out_lng', 'route_in_lat', 'route_in_lng', DB::raw('TIMEDIFF(route_deviate_intime, route_deviate_outtime) AS time_difference'))
                ->get();
        } else {
            $search_text = $request->input('search.value');

            $post_data =  RoutedeviationReport::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('vehicle_imei', 'LIKE', "%{$search_text}%")
                ->orWhere('vehicle_name', 'LIKE', "%{$search_text}%")
                ->orWhere('route_name', 'LIKE', "%{$search_text}%")
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();

            $totalFilteredRecord = RoutedeviationReport::where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('vehicle_imei', 'LIKE', "%{$search_text}%")
                ->orWhere('vehicle_name', 'LIKE', "%{$search_text}%")
                ->orWhere('route_name', 'LIKE', "%{$search_text}%")
                ->count();
        }

        if (!empty($post_data)) {
            // $draw_val = $request->input('draw');
            // $get_json_data = array(
            //     "draw"            => intval($draw_val),
            //     "recordsTotal"    => intval($totalDataRecord),
            //     "recordsFiltered" => intval($totalFilteredRecord),
            //     "data"            => $post_data
            // );
            foreach ($post_data  as $index =>  $data) {

                $serialNumber = $start + $index;
                // $address = $this->AddressService($data->route_out_lat, $data->route_out_lng);
                $route_out_address = $this->get_address($data->route_out_lat, $data->route_out_lng);
                $route_in_address = $this->get_address($data->route_in_lat, $data->route_in_lng);
                // $route_out_address = "";
                // $route_in_address = "";
                //     $edit = '<button type="button" class="btn btn-success showModal"
                //     data-toggle="modal" data-target="#myModal"
                //     data-lat="' . $data->start_latitude . '" data-lng="' . $data->start_longitude . '">
                //     Map View
                // </button>';
                $start_time = Carbon::parse($data->route_deviate_outtime)->timestamp;
                $end_time = Carbon::parse($data->route_deviate_intime)->timestamp;
                $route_name = $data->route_name;
                $route_id = $data->id;
                $edit = '<button type="button" class="btn btn-success showModal"  onclick="route_deviation_data(' . "$start_time" . "," . "$end_time" . "," . "'$route_name'" . "," . "'$route_id'" . ');" >Map View</button>';
                // $delete = '<a><i class="fa fa-trash " aria-hidden="true"></i></a>';

                $array_data[] = array(
                    'S No' => $serialNumber,
                    'vehicle_name' => $data->vehicle_name,
                    'route_name' => $data->route_name,
                    'vehicle_imei' => $data->vehicle_imei,
                    'route_deviate_outtime' => $data->route_deviate_outtime,
                    'route_deviate_intime' => $data->route_deviate_intime,
                    'route_out_address' => $route_out_address,
                    'route_in_address' => $route_in_address,
                    'time_difference' => $data->time_difference,
                    'Action' => $edit
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

    public function playdata(Request $request)
    {
        $start_time = $request->start_time; // Replace this with your timestamp
        $end_time = $request->end_time;
        $route_name = $request->route_name;
        $dateTime = Carbon::createFromTimestamp($start_time);
        $dateTime1 = Carbon::createFromTimestamp($end_time);
        $formattedDateTime = $dateTime->format('Y-m-d H:i:s');
        $formattedDateTime1 = $dateTime1->format('Y-m-d H:i:s');
        $route_id =  $request->route_id;
        $get_latlng = DB::table('routedeviation_reports')
            ->select('route_out_lat', 'route_out_lng', 'route_in_lat', 'route_in_lng')
            ->where('id', '=', "$route_id")->first();
        // dd($get_latlng);
        // $get_latlng =
        $route_out_address = $this->get_address($get_latlng->route_out_lat, $get_latlng->route_out_lng);
        $route_in_address = $this->get_address($get_latlng->route_in_lat, $get_latlng->route_in_lng);
        $query['location'] = array('route_out_address' => $route_out_address, 'route_in_address' => $route_in_address);
        $query['playback'] = DB::table('play_back_histories')
            ->select('latitude', 'longitude')
            ->whereBetween('device_datetime', [$formattedDateTime, $formattedDateTime1])->get();
        $query['route_polyline'] = DB::table('trip_polylines')
            ->select('polyline', 'poc_number', 'route_name')
            ->where('route_name', '=', "$route_name")->get();
        return $query;
    }
}
