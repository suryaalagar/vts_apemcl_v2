<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function send_Success($data){
        $data = array(
            "status_code" => "200",
            "data" => $data,
            "message" => "Suceess" 
        );
        return $data ;
    }

    public function send_Error($data){
        $data = array(
            "status_code" => "404",
            "data" => $data,
            "message" => "Failure" 
        );
        return $data;
    }
}
