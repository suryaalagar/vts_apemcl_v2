<?php

namespace App\Service;

trait AddressService
{

    public function get_address($Lattitute, $Longitute)
    {
        $url = "http://69.197.153.82:8080/reverse?format=jsonv2&lat=$Lattitute&lon=$Longitute";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true); // decode the JSON response into a PHP associative array
        if (isset($data["display_name"])) {
            $address1 = $data["display_name"];
        } else {
            $this->get_address();
        }

        return $address1;
    }

    function trip_plan_encoded($points)
    {

        $encodedString = '';
        $previousLat = 0;
        $previousLng = 0;

        foreach ($points as $point) {

            $lat = $point->latitude; // Latitude is the first element of the array.
            $lng = $point->longitude; // Longitude is the second element of the array.

            $late5 = round($lat * 1e5);
            $lnge5 = round($lng * 1e5);

            $dLat = $late5 - $previousLat;
            $dLng = $lnge5 - $previousLng;

            $previousLat = $late5;
            $previousLng = $lnge5;

            $encodedString .= $this->encodeSignedNumber($dLat) . $this->encodeSignedNumber($dLng);
        }

        return $encodedString;
    }

    public function encodeSignedNumber($num)
    {
        $sgn_num = $num << 1;
        if ($num < 0) {
            $sgn_num = ~($sgn_num);
        }
        return $this->encodeNumber($sgn_num);
    }
    public function encodeNumber($num)
    {
        $encodeString = '';
        while ($num >= 0x20) {
            $nextValue = (0x20 | ($num & 0x1f)) + 63;
            $encodeString .= chr($nextValue);
            $num >>= 5;
        }
        $finalValue = $num + 63;
        $encodeString .= chr($finalValue);
        return $encodeString;
    }
}
