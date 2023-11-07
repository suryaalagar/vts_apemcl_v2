<?php

namespace App\Http\Controllers;

use App\Models\PlayBackHistoryReport;
use App\Http\Requests\StorePlayBackHistoryReportRequest;
use App\Http\Requests\UpdatePlayBackHistoryReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayBackHistoryReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $playback_data = PlayBackHistoryReport::get();
        return view('report.playbackhistory', compact('playback_data'));
    }


    // public function get_apemcl_data(Request $request)
    // {
    //     $jsonData = json_decode($request->query('data'), true);
    //     if ($jsonData) {
    //         // Assuming each record in JSON data has 'key' and 'value' fields
    //         $batchData = [];
    //         foreach ($jsonData as $item) {
    //             $batchData[] = [
    //                 'deviceimei' => $item['deviceimei'],     // Change 'key_column' to the actual column name
    //                 'lattitute' => $item['lattitute'], // Change 'value_column' to the actual column name
    //                 'longitute' => $item['longitute'], // Change 'value_column' to the actual column name
    //                 'speed' => $item['speed'],
    //                 'server_time' => $item['server_time']

    //             ];
    //         }
    //         DB::table('new_location_history')->insert($batchdata);

    //         return response()->json(['message' => 'Batch data inserted successfully']);
    //     } else {
    //         return response()->json(['message' => 'No valid data to insert']);
    //     }
    // }

    public function get_apemcl_data(Request $request)
    {
        $url = "https://vts.trackingwings.com/API/AdminAPI/apemcl_data";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HEADER => true
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $jsonData = substr($response, $headerSize);
        $dataArray = json_decode($jsonData, true);

        if ($dataArray) {

            // foreach ($dataArray as $key => $item) {
               
           
                $batchData[] = array(
                    'deviceimei' => $dataArray['running_no'],     // Change 'key_column' to the actual column name
                    'lattitute' => $dataArray['lat_message'], // Change 'value_column' to the actual column name
                    'longitute' => $dataArray['lon_message'], // Change 'value_column' to the actual column name
                    'speed' => $dataArray['speed'],
                    'server_time' => $dataArray['current_datetime'],
                    'ignition' => $dataArray['acc_status'],
                    'angle'=>$dataArray['angle'],
                    'device_datetime'=>$dataArray['modified_date'],
                );
           
            DB::table('new_location_history')->insert($batchData);
        // }
            echo"Suceess";
        }else{
            echo"Fail";
        }
    }

    public function demo_test(Request $request)
    {
        $data[] = array(
           'current_time' => date("Y-m-d H:i:s")
        );
        DB::table('demo_test_cron')->insert($data);
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
