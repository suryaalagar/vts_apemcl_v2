<?php

namespace App\Service;

trait AddressService {

    public function get_address($Lattitute, $Longitute)
	{
		// $url = "http://198.204.245.190/nominatim/reverse?format=jsonv2&lat=$Lattitute&lon=$Longitute";
		$url = "http://69.197.153.82:8080/reverse?format=jsonv2&lat=$Lattitute&lon=$Longitute";
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
		$response = curl_exec($curl);
		curl_close($curl);
		$data = json_decode($response, true); // decode the JSON response into a PHP associative array
		$address1 = $data["display_name"];
		// $details = $address['road'] . ", " . $address['city'] . ", " . $address['state'] . ", " . $address['country'];
		// //$array  = array('status' => 'ok', 'address' =>$details);
		// $array = array('status' => 'ok', 'address' => $data["display_name"]);

		return $address1;
	}


}
