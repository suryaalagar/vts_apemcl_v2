<?php
error_reporting(0);
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Api extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api_model');
		$this->load->model('genericreport_model');
		$this->load->model('Smartreport_model');
		$this->load->model('Executivereport_model');
		$this->load->model('Dashboardmodel');
		$this->load->library('Authorization_Token');
	}

	public function user_post()
	{
		$array  = array('status' => 'ok', 'data' => 1);
		$this->response($array);
	}

	public function login_post()
	{
		$username = $this->input->post('username');
		$password = md5($this->input->post('password'));
		$push_code = $this->input->post('push_code');
		$api_key = $this->input->post('api_key');
		$checkuser = $this->api_model->checkuser($username, $password);
		// $check_api_key = $this->api_model->verify_apikey($api_key, $checkuser->dealer_id);
		// if (!empty($check_api_key)) {
		if ($checkuser) {
			$token_data['userid'] = $checkuser->userid;
			$token_data['username'] = $checkuser->username;
			$token_data['client_id'] = $checkuser->client_id;
			$token_data['dealer_id'] = $checkuser->dealer_id;
			$token_data['roleid'] = $checkuser->roleid;
			$token_data['subdealer_id'] = $checkuser->subdealer_id;
			$token_data['timezone_minutes'] = $checkuser->timezone_minutes ? $checkuser->timezone_minutes : 0;

			$tokenData = $this->authorization_token->generateToken($token_data);
			$ip_address = $this->input->ip_address();
			$push_data = array(
				'client_id' => $checkuser->client_id,
				'userid' => $checkuser->userid,
				'push_code' => $push_code,
				'ip_address' => $ip_address
			);
			$user_count = $this->db->query("SELECT count(*) as user_count FROM user_pushcode WHERE client_id=$checkuser->client_id");
			$count = $user_count->num_rows();
			if ($count > 5) {
				$this->db->query("DELETE FROM user_pushcode ORDER BY push_id ASC LIMIT 1");
				$sql = $this->db->insert_string('user_pushcode', $push_data) . ' ON DUPLICATE KEY UPDATE client_id=LAST_INSERT_ID(client_id)';
				$this->db->query($sql);
			} else {
				$sql = $this->db->insert_string('user_pushcode', $push_data) . ' ON DUPLICATE KEY UPDATE client_id=LAST_INSERT_ID(client_id)';
				$this->db->query($sql);
			}

			$array = array();
			$array['status'] = 1;
			$array['refresh_token'] = 'SWT ' . $tokenData;
			$array['data'] = $checkuser;
		} else {
			$array  = array('status' => 0, 'message' => 'Username and Password Not Excist');
		}
		// } else {
		// 	$data['status'] = 0;
		// 	$data['message'] = 'Not Authorised to Login This APP';
		// 	$this->response($data, REST_Controller::HTTP_OK);
		// }

		$this->response($array);
	}

	public function verify_post()
	{
		$headers = $this->input->request_headers();
		$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

		$this->response($decodedToken);
	}

	public function vehiclecount_get()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {

			$roleid = $result['data']->roleid;
			$userid = $result['data']->userid;
			$client_id = $result['data']->client_id;
			if ($roleid == "6") {
				$data['status'] = 1;
				$data['allvehicle'] = $this->api_model->total_count($userid, $client_id, $roleid);
				$this->response($data, REST_Controller::HTTP_OK);
			} else {
				$data['status'] = 1;
				$data['allvehicle'] = array($this->api_model->total_count($userid, $client_id, $roleid));
				$this->response($data, REST_Controller::HTTP_OK);
			}
		} else {
			$this->response($result);
		}
	}


	public function vehicledetails_get()
	{
		$headers = $this->input->request_headers();
		$deviceimei = $this->input->get('imei');
		$valid_status = 1;
		if ($deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'deviceimei is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$ct = date('Y-m-d');
				$start_date = $ct . ' 00:00:00';
				$end_date = $ct . ' 23:59:59';

				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$timezone_minutes = $result['data']->timezone_minutes;
				$local_timezone = $timezone_minutes == 0 ? 'CURRENT_TIMESTAMP' : 'UTC_TIMESTAMP';

				$data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei, $timezone_minutes, $local_timezone);
				$device_type = $data['vehicle_details']->device_type;

				$ct_client_id = $data['vehicle_details']->client_id;
				$today_km_data = $this->api_model->calculate_distance($deviceimei, $start_date, $end_date, $client_id);
				$percentage = $this->api_model->percentage_detail($client_id, $deviceimei);

				$batteryvolt = ($percentage) ? 'No data' : $data['vehicle_details']->batteryvolt;

				$percentage = ($percentage) ? $percentage : 'No data';
				$today_km = ($today_km_data) ? $today_km_data->distance_km : 0;

				$today_km = ($today_km_data) ? $today_km_data->distance_km : 0;
				// Address
				$lat = $data['vehicle_details']->lat;
				$lng = $data['vehicle_details']->lng;
				$format = "json";
				$url = "http://69.197.153.82/nominatim/reverse.php?lat=$lat&lon=$lng&format=$format";
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
				$response = curl_exec($curl);
				curl_close($curl);

				$address_data = json_decode($response, true); // decode the JSON response into a PHP associative array

				// Address
				$data['vehicle_details'] = array(array(
					"client_id" => $data['vehicle_details']->client_id,
					"vehicle_id" => $data['vehicle_details']->vehicle_id,
					"alarm_set" => $data['vehicle_details']->alarm_set,
					"updatedon" => $data['vehicle_details']->updatedon,
					"update_time" => $data['vehicle_details']->update_time,
					"odometer" => $data['vehicle_details']->odometer,
					"speed" => $data['vehicle_details']->speed,
					"acc_on" => $data['vehicle_details']->acc_on,
					"ac_flag" => $data['vehicle_details']->ac_flag,
					"lat" => $data['vehicle_details']->lat,
					"lng" => $data['vehicle_details']->lng,
					"angle" => $data['vehicle_details']->angle,
					"latlon_address" => $address_data['display_name'],
					"today_km" => $today_km,
					"trip_kilometer" => $data['vehicle_details']->today_km,
					"batteryvolt" => $batteryvolt,
					"fuel_ltr" => $data['vehicle_details']->fuel_ltr,
					"driver_name" => 'N/A',
					"last_ign_off" => $data['vehicle_details']->last_ign_off,
					"last_ign_on" => $data['vehicle_details']->last_ign_on,
					"mileage" => $data['vehicle_details']->mileage,
					"distancetoEmpty" => 'N/A',
					"secondary_engine" => 'N/A',
					"battery_precentage" => $percentage,
					"hourmeter" => 'h:min',
					"rpm" => round($data['vehicle_details']->rpm_data),
					//"rpm"=>$data['vehicle_details']->rpm_data,
					"temperature" => $data['vehicle_details']->temperature,
					"safe_parking" => $data['vehicle_details']->safe_parking,
					"humidity" => 'N/A',
					"drum" => 'N/A',
					"bucket" => 'N/A',
					"gps" => $data['vehicle_details']->gps,
					"gsm" => $data['vehicle_details']->gsm,
					"altitude" => $data['vehicle_details']->altitude,
					"sattlite" => 'N/A'

				));



				if ($client_id == $ct_client_id) {
					//$data = array_merge($data['vehicle_details'],$data['vehicle_details1']);
					$this->response($data, REST_Controller::HTTP_OK);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1, REST_Controller::HTTP_OK);
				}
			} else {
				$this->response($result);
			}
		}
	}
	public function demo_vehicledetails_get()
	{
		$headers = $this->input->request_headers();
		$deviceimei = $this->input->get('imei');
		$valid_status = 1;
		if ($deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'deviceimei is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$ct = date('Y-m-d');
				$start_date = $ct . ' 00:00:00';
				$end_date = $ct . ' 23:59:59';

				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$timezone_minutes = $result['data']->timezone_minutes;
				$local_timezone = $timezone_minutes == 0 ? 'CURRENT_TIMESTAMP' : 'UTC_TIMESTAMP';

				//$data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				$data['vehicle_details'] = $this->api_model->demo_vehicledetails($deviceimei, $timezone_minutes, $local_timezone);
				$device_type = $data['vehicle_details']->device_type;

				$ct_client_id = $data['vehicle_details']->client_id;
				$today_km_data = $this->api_model->calculate_distance($deviceimei, $start_date, $end_date, $client_id);
				$percentage = $this->api_model->percentage_detail($client_id, $deviceimei);

				$batteryvolt = ($percentage) ? 'No data' : $data['vehicle_details']->batteryvolt;

				$percentage = ($percentage) ? $percentage : 'No data';
				$today_km = ($today_km_data) ? $today_km_data->distance_km : 0;

				$today_km = ($today_km_data) ? $today_km_data->distance_km : 0;
				// Address
				$lat = $data['vehicle_details']->lat;
				$lng = $data['vehicle_details']->lng;
				$format = "json";
				$url = "http://69.197.153.82/nominatim/reverse.php?lat=$lat&lon=$lng&format=$format";
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
				$response = curl_exec($curl);
				curl_close($curl);

				$address_data = json_decode($response, true); // decode the JSON response into a PHP associative array

				// Address
				$data['vehicle_details'] = array(array(
					"client_id" => $data['vehicle_details']->client_id,
					"vehicle_id" => $data['vehicle_details']->vehicle_id,
					"alarm_set" => $data['vehicle_details']->alarm_set,
					"updatedon" => $data['vehicle_details']->updatedon,
					"update_time" => $data['vehicle_details']->update_time,
					"odometer" => $data['vehicle_details']->odometer,
					"speed" => $data['vehicle_details']->speed,
					"acc_on" => $data['vehicle_details']->acc_on,
					"ac_flag" => $data['vehicle_details']->ac_flag,
					"lat" => $data['vehicle_details']->lat,
					"lng" => $data['vehicle_details']->lng,
					"angle" => $data['vehicle_details']->angle,
					"latlon_address" => $address_data['display_name'],
					"today_km" => $today_km,
					"trip_kilometer" => $data['vehicle_details']->today_km,
					"batteryvolt" => $batteryvolt,
					"fuel_ltr" => $data['vehicle_details']->fuel_ltr,
					"driver_name" => 'N/A',
					"last_ign_off" => $data['vehicle_details']->last_ign_off,
					"last_ign_on" => $data['vehicle_details']->last_ign_on,
					"mileage" => $data['vehicle_details']->mileage,
					"distancetoEmpty" => 'N/A',
					"secondary_engine" => 'N/A',
					"battery_precentage" => $percentage,
					"hourmeter" => 'h:min',
					"rpm" => round($data['vehicle_details']->rpm_data),
					//"rpm"=>$data['vehicle_details']->rpm_data,
					"temperature" => $data['vehicle_details']->temperature,
					"safe_parking" => $data['vehicle_details']->safe_parking,
					"humidity" => 'N/A',
					"drum" => 'N/A',
					"bucket" => 'N/A',
					"gps" => $data['vehicle_details']->gps,
					"gsm" => $data['vehicle_details']->gsm,
					"altitude" => $data['vehicle_details']->altitude,
					"sattlite" => 'N/A'

				));



				if ($client_id == $ct_client_id) {
					//$data = array_merge($data['vehicle_details'],$data['vehicle_details1']);
					$this->response($data, REST_Controller::HTTP_OK);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1, REST_Controller::HTTP_OK);
				}
			} else {
				$this->response($result);
			}
		}
	}

	// public function vehicledetails_get()
	// {  
	// 	$headers = $this->input->request_headers(); 
	// 	$deviceimei = $this->input->get('imei');
	// 		$valid_status =1;
	// 		if($deviceimei=='')
	// 	{
	// 		$valid_status = 0;
	// 		$data['status'] = 0;
	// 		$data['message'] ='deviceimei is Empty';
	// 		$this->response($data, REST_Controller::HTTP_OK);

	// 	}

	// 	if($valid_status)
	// 	{

	// 	$result = $this->authorization_token->validateToken($headers['Authorization']);

	// 	if($result['status']==1)
	// 	{
	// 			$data['status'] = 1;
	// 			$client_id= $result['data']->client_id;
	// 			$data['vehicle_details'] = array($this->api_model->vehicledetails($deviceimei));
	// 			if($client_id==$data['vehicle_details'][0]->client_id)
	// 			{
	// 				$this->response($data, REST_Controller::HTTP_OK);
	// 			}
	// 			else
	// 			{

	// 				$data1['status'] = 0;
	// 				$data1['message'] ='Please send Current User Token';
	// 				$this->response($data1, REST_Controller::HTTP_OK);
	// 			}

	// 	}
	// 	else
	// 	{
	// 		$this->response($result); 
	// 	}

	// 	}

	// }

	public function vehiclelist_get()
	{
		$headers = $this->input->request_headers();
		$status = $this->input->get('status');
		$valid_status = 1;
		if ($status == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'status is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);

			$start_date = $ct . ' 00:00:00';
			$end_date = $ct . ' 23:59:59';

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$roleid = $result['data']->roleid;
				$timezone_minutes = $result['data']->timezone_minutes;
				$local_timezone = $timezone_minutes == 0 ? 'CURRENT_TIMESTAMP' : 'UTC_TIMESTAMP';
				$data['allvehicle'] = $this->api_model->vehiclelist($client_id, $status, $roleid, $timezone_minutes, $local_timezone);
				// $result_data = array();
				// foreach($vehicle_list as $key=>$vehicle_data){
				// 	// Getting Address  Using Lat ,Lng - Starts Here
				// 	$lat = $vehicle_data->lat;
				// 	$lng = $vehicle_data->lng;
				// 	$format = "json";
				// 	$url = "http://69.197.153.82/nominatim/reverse.php?lat=$lat&lon=$lng&format=$format";

				// 	$curl = curl_init();
				// 	curl_setopt($curl, CURLOPT_URL, $url);
				// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				// 	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
				// 	$response = curl_exec($curl);
				// 	curl_close($curl);

				// 	$address_data = json_decode($response, true); 
				// 	$address = $address_data['display_name'];
				// 	// Getting Address  Using Lat ,Lng - Ends Here

				// 	unset($vehicle_data->address);
				// 	$vehicle_data->address = $address;
				// 	$result_data[] = $vehicle_data;
				// }

				//$data['allvehicle'] = $result_data;

				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}
	public function demo_vehiclelist_get()
	{
		$headers = $this->input->request_headers();
		//print_r($headers
		$status = $this->input->get('status');
		$valid_status = 1;
		if ($status == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'status is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);

			$start_date = $ct . ' 00:00:00';
			$end_date = $ct . ' 23:59:59';

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$roleid = $result['data']->roleid;
				$timezone_minutes = $result['data']->timezone_minutes;
				$local_timezone = $timezone_minutes = 0 ? 'CURRENT_TIMESTAMP' : 'UTC_TIMESTAMP';

				$vehicle_list = $this->api_model->demo_vehiclelist($client_id, $status, $roleid, $timezone_minutes, $local_timezone);

				$result_data = array();
				foreach ($vehicle_list as $key => $vehicle_data) {
					// Getting Address  Using Lat ,Lng - Starts Here
					$lat = $vehicle_data->lat;
					$lng = $vehicle_data->lng;
					$format = "json";
					$url = "http://69.197.153.82/nominatim/reverse.php?lat=$lat&lon=$lng&format=$format";

					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
					$response = curl_exec($curl);
					curl_close($curl);

					$address_data = json_decode($response, true);
					$address = $address_data['display_name'];
					// Getting Address  Using Lat ,Lng - Ends Here

					unset($vehicle_data->address);
					$vehicle_data->address = $address;
					$result_data[] = $vehicle_data;
				}

				$data['allvehicle'] = $result_data;
				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}
	public function genric_parking_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$time = $this->input->post('time');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['parking_report'] = $this->genericreport_model->parking_report_list($from_date, $to_date, $deviceimei, $time);

					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function genric_idle_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$time = $this->input->post('time');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['idle_report'] = $this->genericreport_model->idle_report_list($from_date, $to_date, $deviceimei, $time);

					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}


	public function genric_trip_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$time = $this->input->post('time');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				// $data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['ign_reports'] = $this->genericreport_model->trip_report_list($from_date, $to_date, $deviceimei, $time);

					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function demo_playback_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['vehicletype'] = $this->api_model->vehicletype_data($deviceimei);
					$data['over_speed_limit'] = '50';
					$data['playback_data'] = $this->api_model->playback_data_old($deviceimei, $from_date, $to_date, $client_id);
					$data['query'] = $this->db->last_query();

					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}
	public function playback_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['vehicletype'] = $this->api_model->vehicletype_data($deviceimei);
					$data['over_speed_limit'] = '50';
					$data['playback_data'] = $this->api_model->playback_data_old($deviceimei, $from_date, $to_date, $client_id);


					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function old_smartreport_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {

					$fromtime = strtotime($from_date);
					$totime = strtotime($to_date);
					$from_date = date('Y-m-d', $fromtime);
					$to_date = date('Y-m-d', $totime);
					$fromd = $from_date;
					$tod = $to_date;
					$date1 = new DateTime($fromd);
					$date2 = new DateTime($tod);
					$interval = $date1->diff($date2);
					$diff_day =  $interval->days;
					$last_day = $diff_day;



					$vehicle_detail = $this->db->query("SELECT vehiclename,device_type,client_id FROM vehicletbl WHERE deviceimei=$deviceimei");
					$vehicle_info = $vehicle_detail->row();
					$vehiclename = $vehicle_info->vehiclename;
					$device_type = $vehicle_info->device_type;
					$client_id = $vehicle_info->client_id;

					$dt = $from_date;
					$day_data = array();

					$gfrom_date = date('Y-m-d H:i:s', $fromtime);
					$gfrom_date1 = $from_date . ' 00:00:00';
					for ($i = 0; $i < ($diff_day + 1); $i++) {
						//echo $i.'<br>'.$last_day;exit;
						if ($i == 0 && $last_day == $i) {

							$gfrom_date = date('Y-m-d H:i:s', $fromtime);
							$gto_date = date('Y-m-d H:i:s', $totime);

							$yesterday_distance[] = $this->api_model->smart_distanceday_API($deviceimei, $gfrom_date, $gto_date, $device_type, $client_id);
							$yesterday_park[] = $this->api_model->smart_parkday_API($deviceimei, $gfrom_date, $gto_date);
							$yesterday_idle[] = $this->api_model->smart_idleday_API($deviceimei, $gfrom_date, $gto_date);
							$yesterday_ign[] = $this->api_model->smart_ignday_API($deviceimei, $gfrom_date, $gto_date);
							$yesterday_ac[] = $this->api_model->smart_acday_API($deviceimei, $gfrom_date, $gto_date);
							$yesterday_fill[] = $this->api_model->smart_fuelfill_API($deviceimei, $gfrom_date, $gto_date);
							$yesterday_dip[] = $this->api_model->smart_fueldip_API($deviceimei, $gfrom_date, $gto_date);
							$yesterday_consumed[] = $this->api_model->smart_fuelconsumed_API($deviceimei, $gfrom_date, $gto_date);

							$dd = $dt;
						} elseif ($i == 0 || $last_day == $i) {

							if ($i == 0) {

								$gfrom_date = date('Y-m-d H:i:s', $fromtime);
								$gfrom_date1 = strtotime($gfrom_date);
								$gfrom_date1 = date('Y-m-d', $gfrom_date1);
								$gto_date = $gfrom_date1 . ' 23:59:59';

								$yesterday_distance[] = $this->api_model->smart_distanceday_API($deviceimei, $gfrom_date, $gto_date, $device_type, $client_id);
								$yesterday_park = $this->api_model->smart_parkday_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_idle[] = $this->api_model->smart_idleday_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_ign[] = $this->api_model->smart_ignday_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_ac[] = $this->api_model->smart_acday_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_fill[] = $this->api_model->smart_fuelfill_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_dip[] = $this->api_model->smart_fueldip_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_consumed[] = $this->api_model->smart_fuelconsumed_API($deviceimei, $gfrom_date, $gto_date);

								$dd = $dt;
							} elseif ($i == $last_day) {

								$gto_date = date('Y-m-d H:i:s', $totime);
								$gfrom_date = date('Y-m-d', $totime);
								$gfrom_date = $gfrom_date . ' 00:00:00';


								$yesterday_distance[] = $this->api_model->smart_distanceday_API($deviceimei, $gfrom_date, $gto_date, $device_type, $client_id);
								$yesterday_park[] = $this->api_model->smart_parkday_API($deviceimei, $gfrom_date, $gto_date);
								//print_r($yesterday_park);exit;
								$yesterday_idle[] = $this->api_model->smart_idleday_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_ign[] = $this->api_model->smart_ignday_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_ac[] = $this->api_model->smart_acday_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_fill[] = $this->api_model->smart_fuelfill_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_dip[] = $this->api_model->smart_fueldip_API($deviceimei, $gfrom_date, $gto_date);
								$yesterday_consumed[] = $this->api_model->smart_fuelconsumed_API($deviceimei, $gfrom_date, $gto_date);
							}
						} else {
							$dd = date('Y-m-d', strtotime("+1 days", strtotime($dt)));
							$all_day[] = $dd;
							$dt = $dd;
						}
					}

					//  print_r($all_day);exit;


					$all_count = count($all_day);
					$start_date = $all_day[0];
					$end_date = $all_day[$all_count - 1];
					$yesterday_distance[] = $this->Smartreport_model->consolidate_distanceday($deviceimei, $start_date, $end_date);
					$yesterday_park[] = $this->Smartreport_model->consolidate_parkday($deviceimei, $start_date, $end_date);
					$yesterday_idle[] = $this->Smartreport_model->consolidate_idleday($deviceimei, $start_date, $end_date);
					$yesterday_ign[] = $this->Smartreport_model->consolidate_ignday($deviceimei, $start_date, $end_date);
					$yesterday_ac[] = $this->Smartreport_model->consolidate_acday($deviceimei, $start_date, $end_date);
					$yesterday_fill[] = $this->Smartreport_model->consolidate_fuelfill($deviceimei, $start_date, $end_date);
					$yesterday_dip[] = $this->Smartreport_model->consolidate_fueldip($deviceimei, $start_date, $end_date);
					$yesterday_consumed[] = $this->Smartreport_model->consolidate_fuelconsumed($deviceimei, $start_date, $end_date);



					$total_parking = 0;
					$park_count = count($yesterday_park) - 1;

					foreach ($yesterday_park as $plist) {

						$total_parking += $plist->parking_duration;
						$park_count += $plist->totalcount;
					}
					$idle_count = count($yesterday_idle) - 1;
					foreach ($yesterday_idle as $idlist) {

						$total_idle += $idlist->idel_duration;
						$idle_count += $idlist->totalcount;
					}
					$running_count = count($yesterday_ign) - 1;
					foreach ($yesterday_ign as $iglist) {

						$total_running += $iglist->moving_duration;
						$running_count += $iglist->totalcount;
					}

					$ac_count = count($yesterday_ac) - 1;
					//	print_r($yesterday_ac);exit;
					foreach ($yesterday_ac as $iglist) {

						$total_ac += $iglist->moving_duration;
						$ac_count += $iglist->totalcount;
					}


					$consume_count = count($yesterday_consumed) - 1;
					foreach ($yesterday_consumed as $fclist) {

						$total_fuel_consume += $fclist->fuel_consumed_litre;

						$totalmilege += $fclist->fuel_milege;
						$consume_count += $fclist->totalcount;
					}
					$avg_fuel_consume = $total_fuel_consume / $consume_count;


					foreach ($yesterday_fill as $fllist) {

						$total_fuel_fill += $fllist->fuel_fill_litre;
					}

					foreach ($yesterday_dip as $fdlist) {

						$total_fuel_dip += $fdlist->fuel_dip_litre;
					}
					if ($device_type == 17) {


						$curl = curl_init();
						curl_setopt_array($curl, array(
							CURLOPT_URL => 'https://sproutwings.asymbix.net/auth/login?jwt=1',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS => '{"login":"sproutwings_wl","password":"7Qx4x6uIJT"}',
							CURLOPT_HTTPHEADER => array(
								'Content-Type: application/json'
							),
						));
						curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

						$jwt_token = curl_exec($curl);
						$jwt_token = json_decode($jwt_token);

						$jwt = 'JWT ' . $jwt_token->jwt;
						//echo $jwt_token->jwt;exit;
						curl_close($curl);

						$dd = $from_date;
						$startdate = $dd . " 00:00:00";
						$startdate = strtotime($startdate);
						'<br>';
						$enddate = $dd . " 23:59:59";
						$enddate = strtotime($enddate);
						// echo '<br>';
						$url = "https://sproutwings.asymbix.net/ls/api/v1/reports/statistics?versionNumber=1&vehicleID=$vehicle_data[0]&timeBegin=$fromtime&timeEnd=$totime&dataGroups=[mw,fuel]&vehicles=[$vehicle_data[0]]";
						//echo $url;exit;

						$curl = curl_init();
						//print_r($curl);
						curl_setopt_array($curl, array(
							CURLOPT_URL => $url,
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'GET',
							CURLOPT_HTTPHEADER => array(
								'Authorization:' . $jwt
							),
						));
						//Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
						//setting them to false.
						curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

						$response = curl_exec($curl);
						$response = json_decode($response);
						//		print_r($response->data->totalMw);

						$normal_rpm = $response->data->totalMw->totalWorkedOnNormalRPM;
						$idle_rpm = $response->data->totalMw->totalWorkedOnIdlingRPM;
						$under_load = $response->data->totalMw->totalWorkedOnExcessRPM;
						$milege = $response->data->totalMw->totalMileage;
					}

					if ($device_type == 17) {
						$total_kilometer = $milege;
						$avg_kilometer = $total_kilometer / ($diff_day + 1);
					} else {
						$total_kilometer = 0;
						$dis_count = count($yesterday_distance) - 1;
						foreach ($yesterday_distance as $dlist) {
							$total_kilometer += $dlist->distance_km;
							$dis_count += $dlist->totalcount;
						}
						$avg_kilometer = $total_kilometer / $dis_count;
					}


					$park_duration = $total_parking; //Parking Duration
					$hours = floor($park_duration / 60);
					$min = $park_duration - ($hours * 60);
					$min = floor((($min -   floor($min / 60) * 60)) / 6);
					$second = $park_duration % 60;
					$tot_park = $hours . ":" . $min . ":" . $second;
					$parts = explode(':', $tot_park);
					$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
					$park_period = $secs / ($park_count * 864) . '%';


					$idle_duration = $total_idle;       // Idle Duration
					$hours = floor($idle_duration / 60);
					$min = $idle_duration - ($hours * 60);
					$min = floor((($min -   floor($min / 60) * 60)) / 6);
					$second = $idle_duration % 60;
					$tot_idle = $hours . ":" . $min . ":" . $second;
					$tot_idle1 = $hours . "." . $min;
					$parts = explode(':', $tot_idle);
					$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
					$idle_period = $secs / ($idle_count * 864) . '%';

					$running_duration = $total_running; //Running Duration
					$hours = floor($running_duration / 60);
					$min = $running_duration - ($hours * 60);
					$min = floor((($min -   floor($min / 60) * 60)) / 6);
					$second = $running_duration % 60;
					$tot_move = $hours . ":" . $min . ":" . $second;
					$tot_move1 = $hours . "." . $min;
					$parts = explode(':', $tot_move);
					$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
					$running_period = $secs / ($running_count * 864) . '%';

					$ac_duration = $total_ac; //AC Duration
					$hours = floor($ac_duration / 60);
					$min = $ac_duration - ($hours * 60);
					$min = floor((($min -   floor($min / 60) * 60)) / 6);
					$second = $ac_duration % 60;
					$tot_ac = $hours . ":" . $min . ":" . $second;
					$tot_ac1 = $hours . "." . $min;
					$parts = explode(':', $tot_ac);
					$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
					$ac_period = $secs / ($ac_count * 864) . '%';


					$normal_duration = $normal_rpm; //Normal RPM Duration
					$hours = floor($normal_duration / 3600);
					$min = ($normal_duration / 60) % 60;
					$second = $normal_duration % 60;
					$tot_normalrpm = $hours . ":" . $min . ":" . $second;
					$tot_normalrpm1 = $hours . "." . $min;
					$parts = explode(':', $tot_normalrpm);
					$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
					$normalrpm_period = $secs / ($normalrpm_count * 864) . '%';

					$load_duration = $under_load; //Load RPM Duration
					$hours = floor($load_duration / 3600);
					$min = ($load_duration / 60) % 60;
					$second = $load_duration % 60;
					$tot_loadrpm = $hours . ":" . $min . ":" . $second;
					$tot_loadrpm1 = $hours . "." . $min;
					$parts = explode(':', $tot_loadrpm);
					$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
					$loadrpm_period = $secs / ($loadrpm_count * 864) . '%';

					$idle_duration = $idle_rpm; //Load RPM Duration
					$hours = floor($idle_duration / 3600);
					$min = ($idle_duration / 60) % 60;
					$second = $idle_duration % 60;
					$tot_idlerpm = $hours . ":" . $min . ":" . $second;
					$tot_idlerpm1 = $hours . "." . $min;
					$parts = explode(':', $tot_idlerpm);
					$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];

					$totalrpm_duration = $normal_duration + $load_duration + $idle_duration; //Total RPM Duration
					$hours = floor($totalrpm_duration / 3600);
					$min = ($totalrpm_duration / 60) % 60;
					$min1 = round($min / 60, 1) * 10;
					$second = $totalrpm_duration % 60;
					$tot_rpm = $hours . ":" . $min . ":" . $second;
					$tot_hrpm =  $hours + round($min / 60, 1);


					$avg_running_duration = ($total_running / $running_count); //Average Running Duration
					$hours = floor($avg_running_duration / 60);
					$min = $avg_running_duration - ($hours * 60);
					$min = floor((($min -   floor($min / 60) * 60)) / 6);
					$second = $avg_running_duration % 60;
					$tot_move_avg = $hours . ":" . $min . ":" . $second;

					$avg_moving_fl_cunsume = ($total_fuel_consume / $tot_move1) / $consume_count;
					$moving_fl_cunsume = ($total_fuel_consume / $tot_move1);
					$not_moving_fl_cunsume = ($total_fuel_consume / $tot_idle1);



					$data['smart_report'] = array(
						'totalKilometer' => round((int)$total_kilometer, 1),
						'avgDailykm' => round((int)$avg_kilometer, 1),
						'runningTimeHMS' => $tot_move,
						'runningTime' => round((int)$running_period, 1),
						'avgDailyRunning' => $tot_move_avg,
						'actime' => $tot_ac,
						'acTimeReport' => round((int)$ac_period, 1),
						'vehicleOprIdle' => $tot_idle,
						'vehicleOprIdleEngineOpr' => round((int)$idle_period, 1),
						'vehicleOff' => $tot_park,
						'vehicleOffReport' => round((int)$park_period, 1),
						'totalEnginerpm' => $tot_rpm,
						'engOprNormalRpm' => $tot_normalrpm,
						'engOprMaximumRpm' => $tot_loadrpm,
						'engOprIdle' => $tot_idlerpm,
						'totalActualFuelCon' => $total_fuel_consume,
						'avgDailyFuelCon' => round((int)$avg_fuel_consume, 1),
						'refuelingVol' => $total_fuel_fill,
						'drainingVolume' => $total_fuel_dip,
						'avgActualMil' => $totalmilege,
						'avgActualMilWhenVehicleMoving' => round((int)$avg_moving_fl_cunsume, 1),
						'totalActualFuelConwhenVehicleMoving' => round((int)$moving_fl_cunsume, 1),
						'totalActualFuelConWhenVehicleNotMov' => round((int)$not_moving_fl_cunsume, 1),

						'totActualFuelConTimeEngOpr' => 0,
						'ActualAvgFuelConperHour' => 0,
						'avgDailyActualFuelConperHour' => 0,
						'totActualConTimeEngOperInMotion' => 0,
						'totActualConTimeEngOperInNormalRPM' => 0,
						'totActualConTimeEngOperInMaxRPM' => 0,
						'totActualConTimeEngOperunderLoad' => 0,
						'bucketMovTime' => 0,
						'bucketMovTimereportPeriod' => 0,
						'bucketAvgDailyMov' => 0,
						'bucketMovIdle' => 0,
						'bucketMovIdleEngOpr' => 0,
						'drumMovTimeload' => 0,
						'drumMovTimeReportPeriod' => 0,
						'AvgDailyDrumMov' => 0,
						'DrumMovtimewithoutLoad' => 0,
						'AvgDailymovwithoutLoad' => 0,
						'DrumnonMovTime' => 0,
						'DrumnonMovReport' => 0,
						'avgTemparature' => 0,

						'DrumMovmenttimereportPeriod' => 0,
						'highTemparature' => 0,
						'lowTemparature' => 0,
						'AvgTemVehicleIdle' => 0,
						'HighTemVehicleIdle' => 0,
						'LowTemVehicleIdle' => 0,
						'AvgTemVehicleRunning' => 0,
						'highTemparatureVehicleRunning' => 0,
						'lowTemVehicleRunning' => 0,
						'highTemVehicleoff' => 0,
						'lowTemVehicleoff' => 0,
						'avgTemparature2' => 0,
						'highTemparature2' => 0,
						'lowTemparature2' => 0,
						'AvgTemVehicleIdle2' => 0,
						'HighTemVehicleIdle2' => 0,
						'LowTemVehicleIdle2' => 0,
						'AvgTemVehicleRunning2' => 0,
						'highTemVehicleRunning2' => 0,
						'lowTemVehicleRunning2' => 0,

						'highTemVehicleoff2' => 0,
						'lowTemVehicleoff2' => 0,
						'totalOdometer' => 0,
						'engineHours' => 0,
						'FuelCon' => 0,
						'avgEngineLoad' => 0,
						'avgCoolTemp' => 0
					);


					$this->response($data);
				}
			} else {

				$data1['status'] = 0;
				$data1['message'] = 'Please send Current User Token';
				$this->response($data1);
			}
		} else {
			$this->response($result);
		}
	}
	public function smartreport_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');

		 $valid_status = 1;
		// if ($from_date == '' || $to_date == '' || $deviceimei == '') {
		// 	$valid_status = 0;
		// 	$data['status'] = 0;
		// 	$data['message'] = 'Require Fields Are empty';
		// 	$this->response($data);
		// }

		 //if ($valid_status) {

		// 	$result = $this->authorization_token->validateToken($headers['Authorization']);

		// 	if ($result['status'] == 1) {
		// 		$data['status'] = 1;
		// 		$client_id = $result['data']->client_id;
		// 		$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
		// 		if ($client_id == $vehicle_data['vehicle_details']->client_id) {

		// 			$fromtime = strtotime($from_date);
		// 			$totime = strtotime($to_date);
		// 			$from_date = date('Y-m-d', $fromtime);
		// 			$to_date = date('Y-m-d', $totime);
		// 			$fromd = $from_date;
		// 			$tod = $to_date;
		// 			$date1 = new DateTime($fromd);
		// 			$date2 = new DateTime($tod);
		// 			$interval = $date1->diff($date2);
		// 			$diff_day =  $interval->days;
		// 			$last_day = $diff_day;



		// 			$vehicle_detail = $this->db->query("SELECT vehiclename,device_type,client_id FROM vehicletbl WHERE deviceimei=$deviceimei");
		// 			$vehicle_info = $vehicle_detail->row();
		// 			$vehiclename = $vehicle_info->vehiclename;
		// 			$device_type = $vehicle_info->device_type;
		// 			$client_id = $vehicle_info->client_id;

		// 			$dt = $from_date;
		// 			$day_data = array();

		// 			$gfrom_date = date('Y-m-d H:i:s', $fromtime);
		// 			$gfrom_date1 = $from_date . ' 00:00:00';
		// 			for ($i = 0; $i < ($diff_day + 1); $i++) {
		// 				//echo $i.'<br>'.$last_day;exit;
		// 				if ($i == 0 && $last_day == $i) {

		// 					$gfrom_date = date('Y-m-d H:i:s', $fromtime);
		// 					$gto_date = date('Y-m-d H:i:s', $totime);

		// 					$yesterday_distance[] = $this->api_model->smart_distanceday_API($deviceimei, $gfrom_date, $gto_date, $device_type, $client_id);
		// 					$yesterday_park[] = $this->api_model->smart_parkday_API($deviceimei, $gfrom_date, $gto_date);
		// 					$yesterday_idle[] = $this->api_model->smart_idleday_API($deviceimei, $gfrom_date, $gto_date);
		// 					$yesterday_ign[] = $this->api_model->smart_ignday_API($deviceimei, $gfrom_date, $gto_date);
		// 					$yesterday_ac[] = $this->api_model->smart_acday_API($deviceimei, $gfrom_date, $gto_date);
		// 					$yesterday_fill[] = $this->api_model->smart_fuelfill_API($deviceimei, $gfrom_date, $gto_date);
		// 					$yesterday_dip[] = $this->api_model->smart_fueldip_API($deviceimei, $gfrom_date, $gto_date);
		// 					$yesterday_consumed[] = $this->api_model->smart_fuelconsumed_API($deviceimei, $gfrom_date, $gto_date);

		// 					$dd = $dt;
		// 				} elseif ($i == 0 || $last_day == $i) {

		// 					if ($i == 0) {

		// 						$gfrom_date = date('Y-m-d H:i:s', $fromtime);
		// 						$gfrom_date1 = strtotime($gfrom_date);
		// 						$gfrom_date1 = date('Y-m-d', $gfrom_date1);
		// 						$gto_date = $gfrom_date1 . ' 23:59:59';

		// 						$yesterday_distance[] = $this->api_model->smart_distanceday_API($deviceimei, $gfrom_date, $gto_date, $device_type, $client_id);
		// 						$yesterday_park = $this->api_model->smart_parkday_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_idle[] = $this->api_model->smart_idleday_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_ign[] = $this->api_model->smart_ignday_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_ac[] = $this->api_model->smart_acday_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_fill[] = $this->api_model->smart_fuelfill_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_dip[] = $this->api_model->smart_fueldip_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_consumed[] = $this->api_model->smart_fuelconsumed_API($deviceimei, $gfrom_date, $gto_date);

		// 						$dd = $dt;
		// 					} elseif ($i == $last_day) {

		// 						$gto_date = date('Y-m-d H:i:s', $totime);
		// 						$gfrom_date = date('Y-m-d', $totime);
		// 						$gfrom_date = $gfrom_date . ' 00:00:00';


		// 						$yesterday_distance[] = $this->api_model->smart_distanceday_API($deviceimei, $gfrom_date, $gto_date, $device_type, $client_id);
		// 						$yesterday_park[] = $this->api_model->smart_parkday_API($deviceimei, $gfrom_date, $gto_date);
		// 						//print_r($yesterday_park);exit;
		// 						$yesterday_idle[] = $this->api_model->smart_idleday_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_ign[] = $this->api_model->smart_ignday_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_ac[] = $this->api_model->smart_acday_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_fill[] = $this->api_model->smart_fuelfill_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_dip[] = $this->api_model->smart_fueldip_API($deviceimei, $gfrom_date, $gto_date);
		// 						$yesterday_consumed[] = $this->api_model->smart_fuelconsumed_API($deviceimei, $gfrom_date, $gto_date);
		// 					}
		// 				} else {
		// 					$dd = date('Y-m-d', strtotime("+1 days", strtotime($dt)));
		// 					$all_day[] = $dd;
		// 					$dt = $dd;
		// 				}
		// 			}

		// 			//  print_r($all_day);exit;


		// 			$all_count = count($all_day);
		// 			$start_date = $all_day[0];
		// 			$end_date = $all_day[$all_count - 1];
		// 			$yesterday_distance[] = $this->Smartreport_model->consolidate_distanceday($deviceimei, $start_date, $end_date);
		// 			$yesterday_park[] = $this->Smartreport_model->consolidate_parkday($deviceimei, $start_date, $end_date);
		// 			$yesterday_idle[] = $this->Smartreport_model->consolidate_idleday($deviceimei, $start_date, $end_date);
		// 			$yesterday_ign[] = $this->Smartreport_model->consolidate_ignday($deviceimei, $start_date, $end_date);
		// 			$yesterday_ac[] = $this->Smartreport_model->consolidate_acday($deviceimei, $start_date, $end_date);
		// 			$yesterday_fill[] = $this->Smartreport_model->consolidate_fuelfill($deviceimei, $start_date, $end_date);
		// 			$yesterday_dip[] = $this->Smartreport_model->consolidate_fueldip($deviceimei, $start_date, $end_date);
		// 			$yesterday_consumed[] = $this->Smartreport_model->consolidate_fuelconsumed($deviceimei, $start_date, $end_date);



		// 			$total_parking = 0;
		// 			$park_count = count($yesterday_park) - 1;

		// 			foreach ($yesterday_park as $plist) {

		// 				$total_parking += $plist->parking_duration;
		// 				$park_count += $plist->totalcount;
		// 			}
		// 			$idle_count = count($yesterday_idle) - 1;
		// 			foreach ($yesterday_idle as $idlist) {

		// 				$total_idle += $idlist->idel_duration;
		// 				$idle_count += $idlist->totalcount;
		// 			}
		// 			$running_count = count($yesterday_ign) - 1;
		// 			foreach ($yesterday_ign as $iglist) {

		// 				$total_running += $iglist->moving_duration;
		// 				$running_count += $iglist->totalcount;
		// 			}

		// 			$ac_count = count($yesterday_ac) - 1;
		// 			//	print_r($yesterday_ac);exit;
		// 			foreach ($yesterday_ac as $iglist) {

		// 				$total_ac += $iglist->moving_duration;
		// 				$ac_count += $iglist->totalcount;
		// 			}


		// 			$consume_count = count($yesterday_consumed) - 1;
		// 			foreach ($yesterday_consumed as $fclist) {

		// 				$total_fuel_consume += $fclist->fuel_consumed_litre;

		// 				$totalmilege += $fclist->fuel_milege;
		// 				$consume_count += $fclist->totalcount;
		// 			}
		// 			$avg_fuel_consume = $total_fuel_consume / $consume_count;


		// 			foreach ($yesterday_fill as $fllist) {

		// 				$total_fuel_fill += $fllist->fuel_fill_litre;
		// 			}

		// 			foreach ($yesterday_dip as $fdlist) {

		// 				$total_fuel_dip += $fdlist->fuel_dip_litre;
		// 			}
		// 			if ($device_type == 17) {


		// 				$curl = curl_init();
		// 				curl_setopt_array($curl, array(
		// 					CURLOPT_URL => 'https://sproutwings.asymbix.net/auth/login?jwt=1',
		// 					CURLOPT_RETURNTRANSFER => true,
		// 					CURLOPT_ENCODING => '',
		// 					CURLOPT_MAXREDIRS => 10,
		// 					CURLOPT_TIMEOUT => 0,
		// 					CURLOPT_FOLLOWLOCATION => true,
		// 					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		// 					CURLOPT_CUSTOMREQUEST => 'POST',
		// 					CURLOPT_POSTFIELDS => '{"login":"sproutwings_wl","password":"7Qx4x6uIJT"}',
		// 					CURLOPT_HTTPHEADER => array(
		// 						'Content-Type: application/json'
		// 					),
		// 				));
		// 				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		// 				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		// 				$jwt_token = curl_exec($curl);
		// 				$jwt_token = json_decode($jwt_token);

		// 				$jwt = 'JWT ' . $jwt_token->jwt;
		// 				//echo $jwt_token->jwt;exit;
		// 				curl_close($curl);

		// 				$dd = $from_date;
		// 				$startdate = $dd . " 00:00:00";
		// 				$startdate = strtotime($startdate);
		// 				'<br>';
		// 				$enddate = $dd . " 23:59:59";
		// 				$enddate = strtotime($enddate);
		// 				// echo '<br>';
		// 				$url = "https://sproutwings.asymbix.net/ls/api/v1/reports/statistics?versionNumber=1&vehicleID=$vehicle_data[0]&timeBegin=$fromtime&timeEnd=$totime&dataGroups=[mw,fuel]&vehicles=[$vehicle_data[0]]";
		// 				//echo $url;exit;

		// 				$curl = curl_init();
		// 				//print_r($curl);
		// 				curl_setopt_array($curl, array(
		// 					CURLOPT_URL => $url,
		// 					CURLOPT_RETURNTRANSFER => true,
		// 					CURLOPT_ENCODING => '',
		// 					CURLOPT_MAXREDIRS => 10,
		// 					CURLOPT_TIMEOUT => 0,
		// 					CURLOPT_FOLLOWLOCATION => true,
		// 					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		// 					CURLOPT_CUSTOMREQUEST => 'GET',
		// 					CURLOPT_HTTPHEADER => array(
		// 						'Authorization:' . $jwt
		// 					),
		// 				));
		// 				//Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
		// 				//setting them to false.
		// 				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		// 				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		// 				$response = curl_exec($curl);
		// 				$response = json_decode($response);
		// 				//		print_r($response->data->totalMw);

		// 				$normal_rpm = $response->data->totalMw->totalWorkedOnNormalRPM;
		// 				$idle_rpm = $response->data->totalMw->totalWorkedOnIdlingRPM;
		// 				$under_load = $response->data->totalMw->totalWorkedOnExcessRPM;
		// 				$milege = $response->data->totalMw->totalMileage;
		// 			}

		// 			if ($device_type == 17) {
		// 				$total_kilometer = $milege;
		// 				$avg_kilometer = $total_kilometer / ($diff_day + 1);
		// 			} else {
		// 				$total_kilometer = 0;
		// 				$dis_count = count($yesterday_distance) - 1;
		// 				foreach ($yesterday_distance as $dlist) {
		// 					$total_kilometer += $dlist->distance_km;
		// 					$dis_count += $dlist->totalcount;
		// 				}
		// 				$avg_kilometer = $total_kilometer / $dis_count;
		// 			}


		// 			$park_duration = $total_parking; //Parking Duration
		// 			$hours = floor($park_duration / 60);
		// 			$min = $park_duration - ($hours * 60);
		// 			$min = floor((($min -   floor($min / 60) * 60)) / 6);
		// 			$second = $park_duration % 60;
		// 			$tot_park = $hours . ":" . $min . ":" . $second;
		// 			$parts = explode(':', $tot_park);
		// 			$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 			$park_period = $secs / ($park_count * 864) . '%';


		// 			$idle_duration = $total_idle;       // Idle Duration
		// 			$hours = floor($idle_duration / 60);
		// 			$min = $idle_duration - ($hours * 60);
		// 			$min = floor((($min -   floor($min / 60) * 60)) / 6);
		// 			$second = $idle_duration % 60;
		// 			$tot_idle = $hours . ":" . $min . ":" . $second;
		// 			$tot_idle1 = $hours . "." . $min;
		// 			$parts = explode(':', $tot_idle);
		// 			$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 			$idle_period = $secs / ($idle_count * 864) . '%';

		// 			$running_duration = $total_running; //Running Duration
		// 			$hours = floor($running_duration / 60);
		// 			$min = $running_duration - ($hours * 60);
		// 			$min = floor((($min -   floor($min / 60) * 60)) / 6);
		// 			$second = $running_duration % 60;
		// 			$tot_move = $hours . ":" . $min . ":" . $second;
		// 			$tot_move1 = $hours . "." . $min;
		// 			$parts = explode(':', $tot_move);
		// 			$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 			$running_period = $secs / ($running_count * 864) . '%';

		// 			$ac_duration = $total_ac; //AC Duration
		// 			$hours = floor($ac_duration / 60);
		// 			$min = $ac_duration - ($hours * 60);
		// 			$min = floor((($min -   floor($min / 60) * 60)) / 6);
		// 			$second = $ac_duration % 60;
		// 			$tot_ac = $hours . ":" . $min . ":" . $second;
		// 			$tot_ac1 = $hours . "." . $min;
		// 			$parts = explode(':', $tot_ac);
		// 			$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 			$ac_period = $secs / ($ac_count * 864) . '%';


		// 			$normal_duration = $normal_rpm; //Normal RPM Duration
		// 			$hours = floor($normal_duration / 3600);
		// 			$min = ($normal_duration / 60) % 60;
		// 			$second = $normal_duration % 60;
		// 			$tot_normalrpm = $hours . ":" . $min . ":" . $second;
		// 			$tot_normalrpm1 = $hours . "." . $min;
		// 			$parts = explode(':', $tot_normalrpm);
		// 			$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 			$normalrpm_period = $secs / ($normalrpm_count * 864) . '%';

		// 			$load_duration = $under_load; //Load RPM Duration
		// 			$hours = floor($load_duration / 3600);
		// 			$min = ($load_duration / 60) % 60;
		// 			$second = $load_duration % 60;
		// 			$tot_loadrpm = $hours . ":" . $min . ":" . $second;
		// 			$tot_loadrpm1 = $hours . "." . $min;
		// 			$parts = explode(':', $tot_loadrpm);
		// 			$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 			$loadrpm_period = $secs / ($loadrpm_count * 864) . '%';

		// 			$idle_duration = $idle_rpm; //Load RPM Duration
		// 			$hours = floor($idle_duration / 3600);
		// 			$min = ($idle_duration / 60) % 60;
		// 			$second = $idle_duration % 60;
		// 			$tot_idlerpm = $hours . ":" . $min . ":" . $second;
		// 			$tot_idlerpm1 = $hours . "." . $min;
		// 			$parts = explode(':', $tot_idlerpm);
		// 			$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];

		// 			$totalrpm_duration = $normal_duration + $load_duration + $idle_duration; //Total RPM Duration
		// 			$hours = floor($totalrpm_duration / 3600);
		// 			$min = ($totalrpm_duration / 60) % 60;
		// 			$min1 = round($min / 60, 1) * 10;
		// 			$second = $totalrpm_duration % 60;
		// 			$tot_rpm = $hours . ":" . $min . ":" . $second;
		// 			$tot_hrpm =  $hours + round($min / 60, 1);


		// 			$avg_running_duration = ($total_running / $running_count); //Average Running Duration
		// 			$hours = floor($avg_running_duration / 60);
		// 			$min = $avg_running_duration - ($hours * 60);
		// 			$min = floor((($min -   floor($min / 60) * 60)) / 6);
		// 			$second = $avg_running_duration % 60;
		// 			$tot_move_avg = $hours . ":" . $min . ":" . $second;

		// 			$avg_moving_fl_cunsume = ($total_fuel_consume / $tot_move1) / $consume_count;
		// 			$moving_fl_cunsume = ($total_fuel_consume / $tot_move1);
		// 			$not_moving_fl_cunsume = ($total_fuel_consume / $tot_idle1);



		// 			$data['smart_report'] = array(
		// 				'totalKilometer' => round((int)$total_kilometer, 1),
		// 				'avgDailykm' => round((int)$avg_kilometer, 1),
		// 				'runningTimeHMS' => $tot_move,
		// 				'runningTime' => round((int)$running_period, 1),
		// 				'avgDailyRunning' => $tot_move_avg,
		// 				'actime' => $tot_ac,
		// 				'acTimeReport' => round((int)$ac_period, 1),
		// 				'vehicleOprIdle' => $tot_idle,
		// 				'vehicleOprIdleEngineOpr' => round((int)$idle_period, 1),
		// 				'vehicleOff' => $tot_park,
		// 				'vehicleOffReport' => round((int)$park_period, 1),
		// 				'totalEnginerpm' => $tot_rpm,
		// 				'engOprNormalRpm' => $tot_normalrpm,
		// 				'engOprMaximumRpm' => $tot_loadrpm,
		// 				'engOprIdle' => $tot_idlerpm,
		// 				'totalActualFuelCon' => $total_fuel_consume,
		// 				'avgDailyFuelCon' => round((int)$avg_fuel_consume, 1),
		// 				'refuelingVol' => $total_fuel_fill,
		// 				'drainingVolume' => $total_fuel_dip,
		// 				'avgActualMil' => $totalmilege,
		// 				'avgActualMilWhenVehicleMoving' => round((int)$avg_moving_fl_cunsume, 1),
		// 				'totalActualFuelConwhenVehicleMoving' => round((int)$moving_fl_cunsume, 1),
		// 				'totalActualFuelConWhenVehicleNotMov' => round((int)$not_moving_fl_cunsume, 1),

		// 				'totActualFuelConTimeEngOpr' => 0,
		// 				'ActualAvgFuelConperHour' => 0,
		// 				'avgDailyActualFuelConperHour' => 0,
		// 				'totActualConTimeEngOperInMotion' => 0,
		// 				'totActualConTimeEngOperInNormalRPM' => 0,
		// 				'totActualConTimeEngOperInMaxRPM' => 0,
		// 				'totActualConTimeEngOperunderLoad' => 0,
		// 				'bucketMovTime' => 0,
		// 				'bucketMovTimereportPeriod' => 0,
		// 				'bucketAvgDailyMov' => 0,
		// 				'bucketMovIdle' => 0,
		// 				'bucketMovIdleEngOpr' => 0,
		// 				'drumMovTimeload' => 0,
		// 				'drumMovTimeReportPeriod' => 0,
		// 				'AvgDailyDrumMov' => 0,
		// 				'DrumMovtimewithoutLoad' => 0,
		// 				'AvgDailymovwithoutLoad' => 0,
		// 				'DrumnonMovTime' => 0,
		// 				'DrumnonMovReport' => 0,
		// 				'avgTemparature' => 0,

		// 				'DrumMovmenttimereportPeriod' => 0,
		// 				'highTemparature' => 0,
		// 				'lowTemparature' => 0,
		// 				'AvgTemVehicleIdle' => 0,
		// 				'HighTemVehicleIdle' => 0,
		// 				'LowTemVehicleIdle' => 0,
		// 				'AvgTemVehicleRunning' => 0,
		// 				'highTemparatureVehicleRunning' => 0,
		// 				'lowTemVehicleRunning' => 0,
		// 				'highTemVehicleoff' => 0,
		// 				'lowTemVehicleoff' => 0,
		// 				'avgTemparature2' => 0,
		// 				'highTemparature2' => 0,
		// 				'lowTemparature2' => 0,
		// 				'AvgTemVehicleIdle2' => 0,
		// 				'HighTemVehicleIdle2' => 0,
		// 				'LowTemVehicleIdle2' => 0,
		// 				'AvgTemVehicleRunning2' => 0,
		// 				'highTemVehicleRunning2' => 0,
		// 				'lowTemVehicleRunning2' => 0,

		// 				'highTemVehicleoff2' => 0,
		// 				'lowTemVehicleoff2' => 0,
		// 				'totalOdometer' => 0,
		// 				'engineHours' => 0,
		// 				'FuelCon' => 0,
		// 				'avgEngineLoad' => 0,
		// 				'avgCoolTemp' => 0
		// 			);


		// 			$this->response($data);
		// 		}
		// 	} else {

		// 		$data1['status'] = 0;
		// 		$data1['message'] = 'Please send Current User Token';
		// 		$this->response($data1);
		// 	}
		// } else {
		// 	$this->response($result);
		// }
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		$this->response($result);
	}

	public function smartreportchk_get()
	{
		$headers = $this->input->request_headers();
		$userid = $this->input->get('userid');
		$valid_status = 1;
		if ($userid == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'userid is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$userid = $result['data']->userid;
				$data['checkbox_list'] = array($this->api_model->smartreportchk_details($userid));


				if ($userid == $data['checkbox_list'][0]->user_id) {

					$this->response($data, REST_Controller::HTTP_OK);
				} else {
					$data1['user_id'] = $userid;
					$this->db->insert('smart_report_chk', $data1);

					$data['checkbox_list'] = array($this->api_model->smartreportchk_details($userid));

					// $data1['status'] = 0;
					// $data1['message'] ='Please send Current User Token';
					$this->response($data, REST_Controller::HTTP_OK);
				}
			} else {
				$this->response($result);
			}
		}
	}



	public function smartreportchkupdate_post()
	{
		$headers = $this->input->request_headers();
		$userid = $this->input->get('userid');
		$valid_status = 1;
		if ($userid == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'userid is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {

				$smart_data = $this->api_model->smartreportchk_details($userid);
				$smartid = $smart_data->id;
				$user_id = $smart_data->user_id;
				$client_id = $smart_data->client_id;


				$data = array(
					'totalKilometer' => $this->input->post('totalKilometer'),
					'avgDailykm' => $this->input->post('avgDailykm'),
					'runningTimeHMS' => $this->input->post('runningTimeHMS'),
					'runningTime' => $this->input->post('runningTime'),
					'avgDailyRunning' => $this->input->post('avgDailyRunning'),
					'actime' => $this->input->post('actime'),
					'acTimeReport' => $this->input->post('acTimeReport'),
					'vehicleOprIdle' => $this->input->post('vehicleOprIdle'),
					'vehicleOprIdleEngineOpr' => $this->input->post('vehicleOprIdleEngineOpr'),
					'vehicleOff' => $this->input->post('vehicleOff'),
					'vehicleOffReport' => $this->input->post('vehicleOffReport'),
					'totalEnginerpm' => $this->input->post('totalEnginerpm'),
					'engOprNormalRpm' => $this->input->post('engOprNormalRpm'),
					'engOprMaximumRpm' => $this->input->post('engOprMaximumRpm'),
					'engOprIdle' => $this->input->post('engOprIdle'),
					'totalActualFuelCon' => $this->input->post('totalActualFuelCon'),
					'avgDailyFuelCon' => $this->input->post('avgDailyFuelCon'),
					'refuelingVol' => $this->input->post('refuelingVol'),
					'drainingVolume' => $this->input->post('drainingVolume'),
					'avgActualMil' => $this->input->post('avgActualMil'),
					'avgActualMilWhenVehicleMoving' => $this->input->post('avgActualMilWhenVehicleMoving'),
					'totalActualFuelConwhenVehicleMoving' => $this->input->post('totalActualFuelConwhenVehicleMoving'),
					'totalActualFuelConWhenVehicleNotMov' => $this->input->post('totalActualFuelConWhenVehicleNotMov'),
					'totActualFuelConTimeEngOpr' => $this->input->post('totActualFuelConTimeEngOpr'),
					'ActualAvgFuelConperHour' => $this->input->post('ActualAvgFuelConperHour'),
					'avgDailyActualFuelConperHour' => $this->input->post('avgDailyActualFuelConperHour'),
					'totActualConTimeEngOperInMotion' => $this->input->post('totActualConTimeEngOperInMotion'),
					'totActualConTimeEngOperInNormalRPM' => $this->input->post('totActualConTimeEngOperInNormalRPM'),
					'totActualConTimeEngOperInMaxRPM' => $this->input->post('totActualConTimeEngOperInMaxRPM'),
					'totActualConTimeEngOperunderLoad' => $this->input->post('totActualConTimeEngOperunderLoad'),
					'bucketMovTime' => $this->input->post('bucketMovTime'),
					'bucketMovTimereportPeriod' => $this->input->post('bucketMovTimereportPeriod'),
					'bucketAvgDailyMov' => $this->input->post('bucketAvgDailyMov'),
					'bucketMovIdle' => $this->input->post('bucketMovIdle'),
					'bucketMovIdleEngOpr' => $this->input->post('bucketMovIdleEngOpr'),
					'drumMovTimeload' => $this->input->post('drumMovTimeload'),
					'drumMovTimeReportPeriod' => $this->input->post('drumMovTimeReportPeriod'),
					'AvgDailyDrumMov' => $this->input->post('AvgDailyDrumMov'),
					'DrumMovtimewithoutLoad' => $this->input->post('DrumMovtimewithoutLoad'),
					'DrumMovmenttimereportPeriod' => $this->input->post('DrumMovmenttimereportPeriod'),
					'AvgDailymovwithoutLoad' => $this->input->post('AvgDailymovwithoutLoad'),
					'DrumnonMovTime' => $this->input->post('DrumnonMovTime'),
					'DrumnonMovReport' => $this->input->post('DrumnonMovReport'),
					'avgTemparature' => $this->input->post('avgTemparature'),
					'highTemparature' => $this->input->post('highTemparature'),
					'lowTemparature' => $this->input->post('lowTemparature'),
					'AvgTemVehicleIdle' => $this->input->post('AvgTemVehicleIdle'),
					'HighTemVehicleIdle' => $this->input->post('HighTemVehicleIdle'),
					'LowTemVehicleIdle' => $this->input->post('LowTemVehicleIdle'),
					'AvgTemVehicleRunning' => $this->input->post('AvgTemVehicleRunning'),
					'highTemparatureVehicleRunning' => $this->input->post('highTemparatureVehicleRunning'),
					'lowTemVehicleRunning' => $this->input->post('lowTemVehicleRunning'),
					'highTemVehicleoff' => $this->input->post('highTemVehicleoff'),
					'lowTemVehicleoff' => $this->input->post('lowTemVehicleoff'),
					'avgTemparature2' => $this->input->post('avgTemparature2'),
					'highTemparature2' => $this->input->post('highTemparature2'),
					'lowTemparature2' => $this->input->post('lowTemparature2'),
					'AvgTemVehicleIdle2' => $this->input->post('AvgTemVehicleIdle2'),
					'HighTemVehicleIdle2' => $this->input->post('HighTemVehicleIdle2'),
					'LowTemVehicleIdle2' => $this->input->post('LowTemVehicleIdle2'),
					'AvgTemVehicleRunning2' => $this->input->post('AvgTemVehicleRunning2'),
					'highTemVehicleRunning2' => $this->input->post('highTemVehicleRunning2'),
					'lowTemVehicleRunning2' => $this->input->post('lowTemVehicleRunning2'),
					'highTemVehicleoff2' => $this->input->post('highTemVehicleoff2'),
					'lowTemVehicleoff2' => $this->input->post('lowTemVehicleoff2'),
					'totalOdometer' => $this->input->post('totalOdometer'),
					'engineHours' => $this->input->post('engineHours'),
					'FuelCon' => $this->input->post('FuelCon'),
					'avgEngineLoad' => $this->input->post('avgEngineLoad'),
					'avgCoolTemp' => $this->input->post('avgCoolTemp')
				);


				if ($smartid == '') {
					$data['user_id'] = $userid;
					$this->db->insert('smart_report_chk', $data);
					$data1['status'] = 1;
					$data1['message'] = 'Data Insert Successfully';
					$this->response($data1, REST_Controller::HTTP_OK);
				} else {

					$this->db->where('id', $smartid);
					$this->db->where('user_id', $userid);
					// $this->db->where('client_id', $client_id);
					$this->db->update('smart_report_chk', $data);

					$data1['status'] = 1;
					$data1['message'] = 'Data Update Successfully';
					$this->response($data1, REST_Controller::HTTP_OK);
				}
			} else {
				$this->response($result);
			}
		}
	}




	public function old_executivereport_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {

					$fromtime = strtotime($from_date);
					$totime = strtotime($to_date);
					$from_date = date('Y-m-d', $fromtime);
					$to_date = date('Y-m-d', $totime);
					$fromd = $from_date;
					$tod = $to_date;
					$date1 = new DateTime($fromd);
					$date2 = new DateTime($tod);
					$interval = $date1->diff($date2);

					$diff_day =  $interval->days;

					$vehicle_detail = $this->db->query("SELECT vehiclename,device_type,client_id FROM vehicletbl WHERE deviceimei='" . $deviceimei . "'");
					$vehicle_info = $vehicle_detail->row();
					$vehiclename = $vehicle_info->vehiclename;
					$device_type = $vehicle_info->device_type;
					$client_id = $vehicle_info->client_id;


					$dt = $from_date;

					$day_data = array();

					for ($i = 0; $i < ($diff_day + 1); $i++) {
						if ($i == 0) {
							$dd = $dt;
						} else {
							$dd = date('Y-m-d', strtotime("+1 days", strtotime($dt)));
						}


						$yesterday_distance = $this->api_model->consolidate_distanceday($deviceimei, $dd, $device_type, $client_id);
						$yesterday_park = $this->api_model->consolidate_parkday($deviceimei, $dd, $client_id);
						$yesterday_idle = $this->api_model->consolidate_idleday($deviceimei, $dd, $client_id);
						$yesterday_ign = $this->api_model->consolidate_ignday($deviceimei, $dd, $client_id);
						$yesterday_ac = $this->api_model->consolidate_acday($deviceimei, $dd, $client_id);
						$yesterday_fill = $this->api_model->consolidate_fuelfill($deviceimei, $dd, $client_id);
						$yesterday_dip = $this->api_model->consolidate_fueldip($deviceimei, $dd, $client_id);
						$yesterday_consumed = $this->api_model->consolidate_fuelconsumed($deviceimei, $dd, $client_id);

						$all_rpm = $this->api_model->consolidate_allrpmday($deviceimei, $dd, $client_id);

						$trip_duration = $yesterday_ign; //Running Duration
						$hours = floor($trip_duration / 60);
						$min = $trip_duration - ($hours * 60);
						$second = $trip_duration % 60;
						$tot_trip = $hours . ":" . $min . ":" . $second;
						$tot_trip1 = $hours . "." . $min;
						$parts = explode(':', $tot_trip);
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$trip_period = $secs / 864 . '%';

						$park_duration = $yesterday_park; //Parking Duration
						$hours = floor($park_duration / 60);
						$min = $park_duration - ($hours * 60);
						$second = $park_duration % 60;
						$tot_park = $hours . ":" . $min . ":" . $second;
						$parts = explode(':', $tot_park);
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$park_period = $secs / 864 . '%';

						$running_duration = $yesterday_ign - $yesterday_idle; //Running Duration
						$hours = floor($running_duration / 60);
						$min = $running_duration - ($hours * 60);
						$second = $running_duration % 60;
						$tot_move = $hours . ":" . $min . ":" . $second;
						$tot_move1 = $hours . "." . $min;
						$parts = explode(':', $tot_move);
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$running_period = $secs / 864 . '%';

						$idle_duration = $yesterday_idle;       // Idle Duration
						$hours = floor($idle_duration / 60);
						$min = $idle_duration - ($hours * 60);
						$second = $idle_duration % 60;
						$tot_idle = $hours . ":" . $min . ":" . $second;
						$parts = explode(':', $tot_idle);
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$idle_period = $secs / 864 . '%';

						$ac_duration = $yesterday_ac;       // Ac Duration
						$hours = floor($ac_duration / 60);
						$min = $ac_duration - ($hours * 60);
						$second = $ac_duration % 60;
						$tot_ac = $hours . ":" . $min . ":" . $second;
						$parts = explode(':', $tot_ac);
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$ac_period = $secs / 864 . '%';

						$normal_rpm_duration = $all_rpm->normal_rpm; //Normal RPM Duration
						$hours = floor($normal_rpm_duration / 3600);
						$min = ($normal_rpm_duration / 60) % 60;
						$second = $normal_rpm_duration % 60;
						$tot_normalrpm = $hours . ":" . $min . ":" . $second;
						$parts = explode(':', $tot_normalrpm);
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$normalrpm_period = $secs / 864 . '%';


						$load_rpm_duration = $all_rpm->under_load; //Load RPM Duration
						$hours = floor($load_rpm_duration / 3600);
						$min = ($load_rpm_duration / 60) % 60;
						$second = $load_rpm_duration % 60;
						$tot_loadrpm = $hours . ":" . $min . ":" . $second;
						$parts = explode(':', $tot_loadrpm);
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$loadrpm_period = $secs / 864 . '%';


						$idlerpm_duration = $all_rpm->idle_rpm; //IDLE RPM Duration
						$hours = floor($idlerpm_duration / 3600);
						$min = ($idlerpm_duration / 60) % 60;
						$second = $idlerpm_duration % 60;
						$tot_idlerpm = $hours . ":" . $min . ":" . $second;
						$parts = explode(':', $tot_idlerpm);
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$idlerpm_period = $secs / 864 . '%';


						$total_rpm_duration = $normal_rpm_duration + $load_rpm_duration + $idlerpm_duration; //Total RPM Duration

						$hours = floor($total_rpm_duration / 3600);
						$min = ($total_rpm_duration / 60) % 60;
						$hmin = $min / 10;
						$mins = '0.' . $min;
						$hoursdata = $mins * 100 / 60;
						$min1 = ($hmin) * (100 / 60);
						//  $tot_hrpm =  $hours + round($min / 60, 1);
						$second = $total_rpm_duration % 60;
						$tot_rpm1 = $hours . "." . $min;
						$tot_hrpm =  round($tot_rpm1 - $hoursdata, 1);
						$tot_rpm = $hours . ":" . $min . ":" . $second;
						$parts = explode(':', $tot_rpm);
						$tot_rpm = $hours . "hh:" . $min . "mm:" . $second . "ss";
						$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
						$tot_rpm_period = $secs / 864 . '%';

						$fuel_millege = round(($yesterday_consumed->fuel_millege), 2);

						$ct = date('Y-m-d');
						if ($dd == $ct) {


							$fuel_consume = $yesterday_fill->start_fuel + $yesterday_fill->fuel_fill_litre - $yesterday_fill->end_fuel;
						} else {
							$fuel_consume = $yesterday_consumed->fuel_consumed_litre;
						}



						$day_data[$dd] = array(
							'mileagekm' => round($yesterday_distance->distance_km, 1),
							'startodometer' => $yesterday_distance->start_odometer,
							'endodometer' => $yesterday_distance->end_odometer,
							'startenginehrmeter' => 0,
							'endenginehrmeter' => 0,
							'overspeedmileagekm' => 0,
							'avgspeedrunning' => 0,
							'maxspeedkm' => 0,
							'triptime' => $tot_trip,
							'triptime_percentagerpt' => round($trip_period),
							'runningtime' => $tot_move,
							'runningtime_percentagerpt' => round($running_period, 1),
							'idletime_hhmmss' => $tot_idle,
							'idletime_percentagerpt' => round($idle_period, 1),
							'parkingtime_hhmmss' => $tot_park,
							'parkingtime_percentage' => round($park_period, 1),
							'actime_hhmmss' => $tot_ac,
							'actime_percentage' => round($ac_period, 1),
							'rpm_opr_time_hhmmss' => $tot_rpm,
							'rpm_opr_time_percentage' => round($tot_rpm_period, 1),
							'rpm_opt_normal_hhmmss' => $tot_normalrpm,
							'rpm_opt_normal_percentage' => round($normalrpm_period, 1),
							'rpm_opt_max_hhmmss' => $tot_loadrpm,
							'rpm_opt_max_percentage' => round($loadrpm_period, 1),
							'rpm_opr_hhmmss' => $tot_idlerpm,
							'rpm_engine_opr_percentage' => round($idlerpm_period, 1),
							'fuelconsumption_engine_hour' => 0,
							'fuel_start_vol' => $yesterday_fill->start_fuel,
							'fuel_final_vol' => $yesterday_fill->end_fuel,
							'fuel_refueling_vol' => $yesterday_fill->fuel_fill_litre,
							'fuel_draining_vol' => $yesterday_dip->fuel_dip_litre,
							'fuel_actual_fuel_cons' => $fuel_consume,
							'fuel_mileage_km' => $fuel_millege,

							'fuel_mileage_vehicle_running_km' => 0,
							'fuel_fuelconsumption_vehicle_running' => 0,
							'fuel_fuelconsumption_vehicle_idle' => 0,

							'bucket_move_time_hhmmss' => 0,
							'bucket_move_time_percentage' => 0,
							'bucket_idle_time_hhmmss' => 0,
							'bucket_idle_time_percentage' => 0,
							'drumnonmvt_time_with_hhmmss' => 0,
							'drumnonmvt_time_with_percentage' => 0,
							'drumnonmvt_time_withoutload_hhmmss' => 0,
							'drumnonmvt_time_withoutload_per' => 0,
							'drumnonmvt_time_hhmmss' => 0,
							'drumnonmvt_time_percentage' => 0,
							'reading_can_odomtr_start' => 0,
							'reading_can_odomtr_end' => 0,
							'fuelconsumptionmeter' => 0,
							'nbsp_sec' => 0,
							'enginehours_hhmmss' => 0,
							'fuelconsumption1' => 0
						);
						$dt = $dd;

						// print_r($day_data);exit;
					}

					$consolidate_data['status'] = 1;
					$consolidate_data['executive_report'] = $day_data;
					$this->response($consolidate_data);
				}
			} else {

				$data1['status'] = 0;
				$data1['message'] = 'Please send Current User Token';
				$this->response($data1);
			}
		} else {
			$this->response($result);
		}
	}

	public function executivereport_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');

		// TEST CODE
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		// TEST CODE
		// $valid_status = 1;
		// if ($from_date == '' || $to_date == '' || $deviceimei == '') {
		// 	$valid_status = 0;
		// 	$data['status'] = 0;
		// 	$data['message'] = 'Require Fields Are empty';
		// 	$this->response($data);
		// }

		// if ($valid_status) {

		// 	$result = $this->authorization_token->validateToken($headers['Authorization']);

		// 	if ($result['status'] == 1) {
		// 		$data['status'] = 1;
		// 		$client_id = $result['data']->client_id;
		// 		$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
		// 		if ($client_id == $vehicle_data['vehicle_details']->client_id) {

		// 			$fromtime = strtotime($from_date);
		// 			$totime = strtotime($to_date);
		// 			$from_date = date('Y-m-d', $fromtime);
		// 			$to_date = date('Y-m-d', $totime);
		// 			$fromd = $from_date;
		// 			$tod = $to_date;
		// 			$date1 = new DateTime($fromd);
		// 			$date2 = new DateTime($tod);
		// 			$interval = $date1->diff($date2);

		// 			$diff_day =  $interval->days;

		// 			$vehicle_detail = $this->db->query("SELECT vehiclename,device_type,client_id FROM vehicletbl WHERE deviceimei='" . $deviceimei . "'");
		// 			$vehicle_info = $vehicle_detail->row();
		// 			$vehiclename = $vehicle_info->vehiclename;
		// 			$device_type = $vehicle_info->device_type;
		// 			$client_id = $vehicle_info->client_id;


		// 			$dt = $from_date;

		// 			$day_data = array();

		// 			for ($i = 0; $i < ($diff_day + 1); $i++) {
		// 				if ($i == 0) {
		// 					$dd = $dt;
		// 				} else {
		// 					$dd = date('Y-m-d', strtotime("+1 days", strtotime($dt)));
		// 				}


		// 				$yesterday_distance = $this->api_model->consolidate_distanceday($deviceimei, $dd, $device_type, $client_id);
		// 				$yesterday_park = $this->api_model->consolidate_parkday($deviceimei, $dd, $client_id);
		// 				$yesterday_idle = $this->api_model->consolidate_idleday($deviceimei, $dd, $client_id);
		// 				$yesterday_ign = $this->api_model->consolidate_ignday($deviceimei, $dd, $client_id);
		// 				$yesterday_ac = $this->api_model->consolidate_acday($deviceimei, $dd, $client_id);
		// 				$yesterday_fill = $this->api_model->consolidate_fuelfill($deviceimei, $dd, $client_id);
		// 				$yesterday_dip = $this->api_model->consolidate_fueldip($deviceimei, $dd, $client_id);
		// 				$yesterday_consumed = $this->api_model->consolidate_fuelconsumed($deviceimei, $dd, $client_id);

		// 				$all_rpm = $this->api_model->consolidate_allrpmday($deviceimei, $dd, $client_id);

		// 				$trip_duration = $yesterday_ign; //Running Duration
		// 				$hours = floor($trip_duration / 60);
		// 				$min = $trip_duration - ($hours * 60);
		// 				$second = $trip_duration % 60;
		// 				$tot_trip = $hours . ":" . $min . ":" . $second;
		// 				$tot_trip1 = $hours . "." . $min;
		// 				$parts = explode(':', $tot_trip);
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$trip_period = $secs / 864 . '%';

		// 				$park_duration = $yesterday_park; //Parking Duration
		// 				$hours = floor($park_duration / 60);
		// 				$min = $park_duration - ($hours * 60);
		// 				$second = $park_duration % 60;
		// 				$tot_park = $hours . ":" . $min . ":" . $second;
		// 				$parts = explode(':', $tot_park);
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$park_period = $secs / 864 . '%';

		// 				$running_duration = $yesterday_ign - $yesterday_idle; //Running Duration
		// 				$hours = floor($running_duration / 60);
		// 				$min = $running_duration - ($hours * 60);
		// 				$second = $running_duration % 60;
		// 				$tot_move = $hours . ":" . $min . ":" . $second;
		// 				$tot_move1 = $hours . "." . $min;
		// 				$parts = explode(':', $tot_move);
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$running_period = $secs / 864 . '%';

		// 				$idle_duration = $yesterday_idle;       // Idle Duration
		// 				$hours = floor($idle_duration / 60);
		// 				$min = $idle_duration - ($hours * 60);
		// 				$second = $idle_duration % 60;
		// 				$tot_idle = $hours . ":" . $min . ":" . $second;
		// 				$parts = explode(':', $tot_idle);
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$idle_period = $secs / 864 . '%';

		// 				$ac_duration = $yesterday_ac;       // Ac Duration
		// 				$hours = floor($ac_duration / 60);
		// 				$min = $ac_duration - ($hours * 60);
		// 				$second = $ac_duration % 60;
		// 				$tot_ac = $hours . ":" . $min . ":" . $second;
		// 				$parts = explode(':', $tot_ac);
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$ac_period = $secs / 864 . '%';

		// 				$normal_rpm_duration = $all_rpm->normal_rpm; //Normal RPM Duration
		// 				$hours = floor($normal_rpm_duration / 3600);
		// 				$min = ($normal_rpm_duration / 60) % 60;
		// 				$second = $normal_rpm_duration % 60;
		// 				$tot_normalrpm = $hours . ":" . $min . ":" . $second;
		// 				$parts = explode(':', $tot_normalrpm);
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$normalrpm_period = $secs / 864 . '%';


		// 				$load_rpm_duration = $all_rpm->under_load; //Load RPM Duration
		// 				$hours = floor($load_rpm_duration / 3600);
		// 				$min = ($load_rpm_duration / 60) % 60;
		// 				$second = $load_rpm_duration % 60;
		// 				$tot_loadrpm = $hours . ":" . $min . ":" . $second;
		// 				$parts = explode(':', $tot_loadrpm);
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$loadrpm_period = $secs / 864 . '%';


		// 				$idlerpm_duration = $all_rpm->idle_rpm; //IDLE RPM Duration
		// 				$hours = floor($idlerpm_duration / 3600);
		// 				$min = ($idlerpm_duration / 60) % 60;
		// 				$second = $idlerpm_duration % 60;
		// 				$tot_idlerpm = $hours . ":" . $min . ":" . $second;
		// 				$parts = explode(':', $tot_idlerpm);
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$idlerpm_period = $secs / 864 . '%';


		// 				$total_rpm_duration = $normal_rpm_duration + $load_rpm_duration + $idlerpm_duration; //Total RPM Duration

		// 				$hours = floor($total_rpm_duration / 3600);
		// 				$min = ($total_rpm_duration / 60) % 60;
		// 				$hmin = $min / 10;
		// 				$mins = '0.' . $min;
		// 				$hoursdata = $mins * 100 / 60;
		// 				$min1 = ($hmin) * (100 / 60);
		// 				//  $tot_hrpm =  $hours + round($min / 60, 1);
		// 				$second = $total_rpm_duration % 60;
		// 				$tot_rpm1 = $hours . "." . $min;
		// 				$tot_hrpm =  round($tot_rpm1 - $hoursdata, 1);
		// 				$tot_rpm = $hours . ":" . $min . ":" . $second;
		// 				$parts = explode(':', $tot_rpm);
		// 				$tot_rpm = $hours . "hh:" . $min . "mm:" . $second . "ss";
		// 				$secs = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
		// 				$tot_rpm_period = $secs / 864 . '%';

		// 				$fuel_millege = round(($yesterday_consumed->fuel_millege), 2);

		// 				$ct = date('Y-m-d');
		// 				if ($dd == $ct) {


		// 					$fuel_consume = $yesterday_fill->start_fuel + $yesterday_fill->fuel_fill_litre - $yesterday_fill->end_fuel;
		// 				} else {
		// 					$fuel_consume = $yesterday_consumed->fuel_consumed_litre;
		// 				}



		// 				$day_data[$dd] = array(
		// 					'mileagekm' => round($yesterday_distance->distance_km, 1),
		// 					'startodometer' => $yesterday_distance->start_odometer,
		// 					'endodometer' => $yesterday_distance->end_odometer,
		// 					'startenginehrmeter' => 0,
		// 					'endenginehrmeter' => 0,
		// 					'overspeedmileagekm' => 0,
		// 					'avgspeedrunning' => 0,
		// 					'maxspeedkm' => 0,
		// 					'triptime' => $tot_trip,
		// 					'triptime_percentagerpt' => round($trip_period),
		// 					'runningtime' => $tot_move,
		// 					'runningtime_percentagerpt' => round($running_period, 1),
		// 					'idletime_hhmmss' => $tot_idle,
		// 					'idletime_percentagerpt' => round($idle_period, 1),
		// 					'parkingtime_hhmmss' => $tot_park,
		// 					'parkingtime_percentage' => round($park_period, 1),
		// 					'actime_hhmmss' => $tot_ac,
		// 					'actime_percentage' => round($ac_period, 1),
		// 					'rpm_opr_time_hhmmss' => $tot_rpm,
		// 					'rpm_opr_time_percentage' => round($tot_rpm_period, 1),
		// 					'rpm_opt_normal_hhmmss' => $tot_normalrpm,
		// 					'rpm_opt_normal_percentage' => round($normalrpm_period, 1),
		// 					'rpm_opt_max_hhmmss' => $tot_loadrpm,
		// 					'rpm_opt_max_percentage' => round($loadrpm_period, 1),
		// 					'rpm_opr_hhmmss' => $tot_idlerpm,
		// 					'rpm_engine_opr_percentage' => round($idlerpm_period, 1),
		// 					'fuelconsumption_engine_hour' => 0,
		// 					'fuel_start_vol' => $yesterday_fill->start_fuel,
		// 					'fuel_final_vol' => $yesterday_fill->end_fuel,
		// 					'fuel_refueling_vol' => $yesterday_fill->fuel_fill_litre,
		// 					'fuel_draining_vol' => $yesterday_dip->fuel_dip_litre,
		// 					'fuel_actual_fuel_cons' => $fuel_consume,
		// 					'fuel_mileage_km' => $fuel_millege,

		// 					'fuel_mileage_vehicle_running_km' => 0,
		// 					'fuel_fuelconsumption_vehicle_running' => 0,
		// 					'fuel_fuelconsumption_vehicle_idle' => 0,

		// 					'bucket_move_time_hhmmss' => 0,
		// 					'bucket_move_time_percentage' => 0,
		// 					'bucket_idle_time_hhmmss' => 0,
		// 					'bucket_idle_time_percentage' => 0,
		// 					'drumnonmvt_time_with_hhmmss' => 0,
		// 					'drumnonmvt_time_with_percentage' => 0,
		// 					'drumnonmvt_time_withoutload_hhmmss' => 0,
		// 					'drumnonmvt_time_withoutload_per' => 0,
		// 					'drumnonmvt_time_hhmmss' => 0,
		// 					'drumnonmvt_time_percentage' => 0,
		// 					'reading_can_odomtr_start' => 0,
		// 					'reading_can_odomtr_end' => 0,
		// 					'fuelconsumptionmeter' => 0,
		// 					'nbsp_sec' => 0,
		// 					'enginehours_hhmmss' => 0,
		// 					'fuelconsumption1' => 0
		// 				);
		// 				$dt = $dd;

		// 				// print_r($day_data);exit;
		// 			}

		// 			$consolidate_data['status'] = 1;
		// 			$consolidate_data['executive_report'] = $day_data;
		// 			$this->response($consolidate_data);
		// 		}
		// 	} else {

		// 		$data1['status'] = 0;
		// 		$data1['message'] = 'Please send Current User Token';
		// 		$this->response($data1);
		// 	}
		// } else {
		// 	$this->response($result);
		// }
		$this->response($result);
	}
	public function exereportchk_get()
	{
		$headers = $this->input->request_headers();
		$userid = $this->input->get('userid');
		$valid_status = 1;
		if ($userid == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'userid is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$userid = $result['data']->userid;
				$data['checkbox_list'] = array($this->api_model->exereportchk_details($userid));

				if ($userid == $data['checkbox_list'][0]->user_id) {

					$this->response($data, REST_Controller::HTTP_OK);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1, REST_Controller::HTTP_OK);
				}
			} else {
				$this->response($result);
			}
		}
	}


	public function exereportchkupdate_post()
	{
		$headers = $this->input->request_headers();
		$userid = $this->input->get('userid');
		$valid_status = 1;
		if ($userid == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'userid is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {

				if ($this->input->post('mileagekm') == 1) {
					$mileagekm = 1;
				} else {
					$mileagekm = 0;
				}
				if ($this->input->post('startodometer') == 1) {
					$startodometer = 1;
				} else {
					$startodometer = 0;
				}
				if ($this->input->post('endodometer') == 1) {
					$endodometer = 1;
				} else {
					$endodometer = 0;
				}
				if ($this->input->post('startenginehrmeter') == 1) {
					$startenginehrmeter = 1;
				} else {
					$startenginehrmeter = 0;
				}
				if ($this->input->post('endenginehrmeter') == 1) {
					$endenginehrmeter = 1;
				} else {
					$endenginehrmeter = 0;
				}
				if ($this->input->post('overspeedmileagekm') == 1) {
					$overspeedmileagekm = 1;
				} else {
					$overspeedmileagekm = 0;
				}
				if ($this->input->post('avgspeedrunning') == 1) {
					$avgspeedrunning = 1;
				} else {
					$avgspeedrunning = 0;
				}
				if ($this->input->post('maxspeedkm') == 1) {
					$maxspeedkm = 1;
				} else {
					$maxspeedkm = 0;
				}
				if ($this->input->post('runningtime') == 1) {
					$runningtime = 1;
				} else {
					$runningtime = 0;
				}
				if ($this->input->post('runningtime_percentagerpt') == 1) {
					$runningtime_percentagerpt = 1;
				} else {
					$runningtime_percentagerpt = 0;
				}
				if ($this->input->post('idletime_hhmmss') == 1) {
					$idletime_hhmmss = 1;
				} else {
					$idletime_hhmmss = 0;
				}
				if ($this->input->post('idletime_percentagerpt') == 1) {
					$idletime_percentagerpt = 1;
				} else {
					$idletime_percentagerpt = 0;
				}
				if ($this->input->post('parkingtime_hhmmss') == 1) {
					$parkingtime_hhmmss = 1;
				} else {
					$parkingtime_hhmmss = 0;
				}
				if ($this->input->post('parkingtime_percentage') == 1) {
					$parkingtime_percentage = 1;
				} else {
					$parkingtime_percentage = 0;
				}
				if ($this->input->post('actime_hhmmss') == 1) {
					$actime_hhmmss = 1;
				} else {
					$actime_hhmmss = 0;
				}
				if ($this->input->post('actime_percentage') == 1) {
					$actime_percentage = 1;
				} else {
					$actime_percentage = 0;
				}
				if ($this->input->post('rpm_opr_time_hhmmss') == 1) {
					$rpm_opr_time_hhmmss = 1;
				} else {
					$rpm_opr_time_hhmmss = 0;
				}
				if ($this->input->post('rpm_opr_time_percentage') == 1) {
					$rpm_opr_time_percentage = 1;
				} else {
					$rpm_opr_time_percentage = 0;
				}
				if ($this->input->post('rpm_opt_normal_hhmmss') == 1) {
					$rpm_opt_normal_hhmmss = 1;
				} else {
					$rpm_opt_normal_hhmmss = 0;
				}
				if ($this->input->post('rpm_opt_normal_percentage') == 1) {
					$rpm_opt_normal_percentage = 1;
				} else {
					$rpm_opt_normal_percentage = 0;
				}
				if ($this->input->post('rpm_opt_max_hhmmss') == 1) {
					$rpm_opt_max_hhmmss = 1;
				} else {
					$rpm_opt_max_hhmmss = 0;
				}
				if ($this->input->post('rpm_opt_max_percentage') == 1) {
					$rpm_opt_max_percentage = 1;
				} else {
					$rpm_opt_max_percentage = 0;
				}
				if ($this->input->post('rpm_opr_hhmmss') == 1) {
					$rpm_opr_hhmmss = 1;
				} else {
					$rpm_opr_hhmmss = 0;
				}
				if ($this->input->post('rpm_engine_opr_percentage') == 1) {
					$rpm_engine_opr_percentage = 1;
				} else {
					$rpm_engine_opr_percentage = 0;
				}
				if ($this->input->post('fuel_start_vol') == 1) {
					$fuel_start_vol = 1;
				} else {
					$fuel_start_vol = 0;
				}
				if ($this->input->post('fuel_final_vol') == 1) {
					$fuel_final_vol = 1;
				} else {
					$fuel_final_vol = 0;
				}
				if ($this->input->post('fuel_actual_fuel_cons') == 1) {
					$fuel_actual_fuel_cons = 1;
				} else {
					$fuel_actual_fuel_cons = 0;
				}
				if ($this->input->post('fuel_refueling_vol') == 1) {
					$fuel_refueling_vol = 1;
				} else {
					$fuel_refueling_vol = 0;
				}
				if ($this->input->post('fuel_draining_vol') == 1) {
					$fuel_draining_vol = 1;
				} else {
					$fuel_draining_vol = 0;
				}
				if ($this->input->post('fuel_mileage_km') == 1) {
					$fuel_mileage_km = 1;
				} else {
					$fuel_mileage_km = 0;
				}
				if ($this->input->post('fuel_mileage_vehicle_running_km') == 1) {
					$fuel_mileage_vehicle_running_km = 1;
				} else {
					$fuel_mileage_vehicle_running_km = 0;
				}
				if ($this->input->post('fuel_fuelconsumption_vehicle_running') == 1) {
					$fuel_fuelconsumption_vehicle_running = 1;
				} else {
					$fuel_fuelconsumption_vehicle_running = 0;
				}
				if ($this->input->post('fuel_fuelconsumption_vehicle_idle') == 1) {
					$fuel_fuelconsumption_vehicle_idle = 1;
				} else {
					$fuel_fuelconsumption_vehicle_idle = 0;
				}
				if ($this->input->post('fuelconsumption_engine_hour') == 1) {
					$fuelconsumption_engine_hour = 1;
				} else {
					$fuelconsumption_engine_hour = 0;
				}
				if ($this->input->post('bucket_move_time_hhmmss') == 1) {
					$bucket_move_time_hhmmss = 1;
				} else {
					$bucket_move_time_hhmmss = 0;
				}
				if ($this->input->post('bucket_move_time_percentage') == 1) {
					$bucket_move_time_percentage = 1;
				} else {
					$bucket_move_time_percentage = 0;
				}
				if ($this->input->post('bucket_idle_time_hhmmss') == 1) {
					$bucket_idle_time_hhmmss = 1;
				} else {
					$bucket_idle_time_hhmmss = 0;
				}
				if ($this->input->post('bucket_idle_time_percentage') == 1) {
					$bucket_idle_time_percentage = 1;
				} else {
					$bucket_idle_time_percentage = 0;
				}
				if ($this->input->post('drumnonmvt_time_with_hhmmss') == 1) {
					$drumnonmvt_time_with_hhmmss = 1;
				} else {
					$drumnonmvt_time_with_hhmmss = 0;
				}
				if ($this->input->post('drumnonmvt_time_with_percentage') == 1) {
					$drumnonmvt_time_with_percentage = 1;
				} else {
					$drumnonmvt_time_with_percentage = 0;
				}
				if ($this->input->post('drumnonmvt_time_withoutload_hhmmss') == 1) {
					$drumnonmvt_time_withoutload_hhmmss = 1;
				} else {
					$drumnonmvt_time_withoutload_hhmmss = 0;
				}
				if ($this->input->post('drumnonmvt_time_withoutload_per') == 1) {
					$drumnonmvt_time_withoutload_per = 1;
				} else {
					$drumnonmvt_time_withoutload_per = 0;
				}
				if ($this->input->post('drumnonmvt_time_hhmmss') == 1) {
					$drumnonmvt_time_hhmmss = 1;
				} else {
					$drumnonmvt_time_hhmmss = 0;
				}
				if ($this->input->post('drumnonmvt_time_percentage') == 1) {
					$drumnonmvt_time_percentage = 1;
				} else {
					$drumnonmvt_time_percentage = 0;
				}
				if ($this->input->post('reading_can_odomtr_start') == 1) {
					$reading_can_odomtr_start = 1;
				} else {
					$reading_can_odomtr_start = 0;
				}
				if ($this->input->post('reading_can_odomtr_end') == 1) {
					$reading_can_odomtr_end = 1;
				} else {
					$reading_can_odomtr_end = 0;
				}
				if ($this->input->post('fuelconsumptionmeter') == 1) {
					$fuelconsumptionmeter = 1;
				} else {
					$fuelconsumptionmeter = 0;
				}
				if ($this->input->post('nbsp_sec') == 1) {
					$nbsp_sec = 1;
				} else {
					$nbsp_sec = 0;
				}
				if ($this->input->post('enginehours_hhmmss') == 1) {
					$enginehours_hhmmss = 1;
				} else {
					$enginehours_hhmmss = 0;
				}
				if ($this->input->post('fuelconsumption1') == 1) {
					$fuelconsumption1 = 1;
				} else {
					$fuelconsumption1 = 0;
				}

				if ($userid) {
					$finedata = array(
						'mileagekm' => $mileagekm,
						'startodometer' => $startodometer,
						'endodometer' => $endodometer,
						'startenginehrmeter' => $startenginehrmeter,
						'endenginehrmeter' => $endenginehrmeter,
						'overspeedmileagekm' => $overspeedmileagekm,
						'avgspeedrunning' => $avgspeedrunning,
						'maxspeedkm' => $maxspeedkm,
						'runningtime' => $runningtime,
						'runningtime_percentagerpt' => $runningtime_percentagerpt,
						'idletime_hhmmss' => $idletime_hhmmss,
						'idletime_percentagerpt' => $idletime_percentagerpt,
						'parkingtime_hhmmss' => $parkingtime_hhmmss,
						'parkingtime_percentage' => $parkingtime_percentage,
						'actime_hhmmss' => $actime_hhmmss,
						'actime_percentage' => $actime_percentage,
						'rpm_opr_time_hhmmss' => $rpm_opr_time_hhmmss,
						'rpm_opr_time_percentage' => $rpm_opr_time_percentage,
						'rpm_opt_normal_hhmmss' => $rpm_opt_normal_hhmmss,
						'rpm_opt_normal_percentage' => $rpm_opt_normal_percentage,
						'rpm_opt_max_hhmmss' => $rpm_opt_max_hhmmss,
						'rpm_opt_max_percentage' => $rpm_opt_max_percentage,
						'rpm_opr_hhmmss' => $rpm_opr_hhmmss,
						'rpm_engine_opr_percentage' => $rpm_engine_opr_percentage,
						'fuel_start_vol' => $fuel_start_vol,
						'fuel_final_vol' => $fuel_final_vol,
						'fuel_actual_fuel_cons' => $fuel_actual_fuel_cons,
						'fuel_refueling_vol' => $fuel_refueling_vol,
						'fuel_draining_vol' => $fuel_draining_vol,
						'fuel_mileage_km' => $fuel_mileage_km,
						'fuel_mileage_vehicle_running_km' => $fuel_mileage_vehicle_running_km,
						'fuel_fuelconsumption_vehicle_running' => $fuel_fuelconsumption_vehicle_running,
						'fuel_fuelconsumption_vehicle_idle' => $fuel_fuelconsumption_vehicle_idle,
						'fuelconsumption_engine_hour' => $fuelconsumption_engine_hour,
						'bucket_move_time_hhmmss' => $bucket_move_time_hhmmss,
						'bucket_move_time_percentage' => $bucket_move_time_percentage,
						'bucket_idle_time_hhmmss' => $bucket_idle_time_hhmmss,
						'bucket_idle_time_percentage' => $bucket_idle_time_percentage,
						'drumnonmvt_time_with_hhmmss' => $drumnonmvt_time_with_hhmmss,
						'drumnonmvt_time_with_percentage' => $drumnonmvt_time_with_percentage,
						'drumnonmvt_time_withoutload_hhmmss' => $drumnonmvt_time_withoutload_hhmmss,
						'drumnonmvt_time_withoutload_per' => $drumnonmvt_time_withoutload_per,
						'drumnonmvt_time_hhmmss' => $drumnonmvt_time_hhmmss,
						'drumnonmvt_time_percentage' => $drumnonmvt_time_percentage,
						'reading_can_odomtr_start' => $reading_can_odomtr_start,
						'reading_can_odomtr_end' => $reading_can_odomtr_end,
						'fuelconsumptionmeter' => $fuelconsumptionmeter,
						'nbsp_sec' => $nbsp_sec,
						'enginehours_hhmmss' => $enginehours_hhmmss,
						'fuelconsumption1' => $fuelconsumption1,
					);



					$this->db->where('user_id', $userid);
					$this->db->update('executive_report_chk', $finedata);

					$data['status'] = 1;
					$data['message'] = 'Data Update Successfully';
					$this->response($data, REST_Controller::HTTP_OK);
				} else {
					$this->response($result);
				}
			}
		}
	}


	public function analysis_post()
	{

		$headers = $this->input->request_headers();
		$deviceimei = $this->input->post('deviceimei');
		$fromdate = $this->input->post('fromdate');
		$todate = $this->input->post('todate');
		$valid_status = 1;
		if ($fromdate == '' || $todate == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Required Fields are Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$data['speed_distance_data'] = $this->api_model->speed_distance_data($fromdate, $todate, $deviceimei, $client_id);
				// $raw_fuelvalue = $this->api_model->Fuel_report_list($fromdate,$todate,$deviceimei);
				// $smooth_fuelvalue = $this->api_model->Fuel_smooth_data($fromdate,$todate,$deviceimei);
				// $rpm_values = $this->api_model->engine_rpm_data($fromdate,$todate,$deviceimei);

				$this->response($data, REST_Controller::HTTP_OK);
			} else {
				$this->response($result);
			}
		}
	}

	public function rawfuelvalue_post()
	{

		$headers = $this->input->request_headers();
		$deviceimei = $this->input->post('deviceimei');
		$fromdate = $this->input->post('fromdate');
		$todate = $this->input->post('todate');
		$valid_status = 1;
		if ($fromdate == '' || $todate == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Required Fields are Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$data['raw_fuelvalue'] = $this->api_model->Fuel_report_list($fromdate, $todate, $deviceimei);
				// $smooth_fuelvalue = $this->api_model->Fuel_smooth_data($fromdate,$todate,$deviceimei);
				// $rpm_values = $this->api_model->engine_rpm_data($fromdate,$todate,$deviceimei);

				$this->response($data, REST_Controller::HTTP_OK);
			} else {
				$this->response($result);
			}
		}
	}

	public function smoothfuelvalue_post()
	{

		$headers = $this->input->request_headers();
		$deviceimei = $this->input->post('deviceimei');
		$fromdate = $this->input->post('fromdate');
		$todate = $this->input->post('todate');
		$valid_status = 1;
		if ($fromdate == '' || $todate == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Required Fields are Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;

				$data['smooth_fuelvalue'] = $this->api_model->Fuel_smooth_data($fromdate, $todate, $deviceimei);
				// $rpm_values = $this->api_model->engine_rpm_data($fromdate,$todate,$deviceimei);

				$this->response($data, REST_Controller::HTTP_OK);
			} else {
				$this->response($result);
			}
		}
	}


	public function rpmvalue_post()
	{
		$headers = $this->input->request_headers();
		$deviceimei = $this->input->post('deviceimei');
		$fromdate = $this->input->post('fromdate');
		$todate = $this->input->post('todate');
		$valid_status = 1;
		if ($fromdate == '' || $todate == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Required Fields are Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;

				// $data['smooth_fuelvalue'] = $this->api_model->Fuel_smooth_data($fromdate,$todate,$deviceimei);
				$data['rpm_values'] = $this->api_model->engine_rpm_data($fromdate, $todate, $deviceimei);

				$this->response($data, REST_Controller::HTTP_OK);
			} else {
				$this->response($result);
			}
		}
	}


	public function temperature_value_post()
	{

		$headers = $this->input->request_headers();
		$deviceimei = $this->input->post('deviceimei');
		$fromdate = $this->input->post('fromdate');
		$todate = $this->input->post('todate');
		$valid_status = 1;
		if ($fromdate == '' || $todate == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Required Fields are Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;

				$data['temperature_value'] = $this->api_model->temperature_value($fromdate, $todate, $deviceimei);

				$this->response($data, REST_Controller::HTTP_OK);
			} else {
				$this->response($result);
			}
		}
	}


	public function fuelvehicle_get()
	{
		$headers = $this->input->request_headers();
		$valid_status = 1;
		if ($headers == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'headers is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;
				$roleid = $result['data']->roleid;
				$data['Fuelvehicles'] = $this->api_model->fuel_vehicle($client_id, $userid, $roleid);

				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}



	public function genric_fuelfill_dip_report_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$type_id = $this->input->post('type_id');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				// $data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['fuel'] = $this->api_model->fuelfill_dipreport($from_date, $to_date, $deviceimei, $type_id);

					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function genric_fuel_milegereport_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				// $data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {


					$fuel_data = $this->genericreport_model->fuel_data_distance($from_date, $to_date, $deviceimei);

					//print_r($fuel_data);exit;
					$query_filldip = $this->db->query("SELECT * FROM (SELECT SUM(difference_fuel) as filldiff FROM fuel_fill_dip_report WHERE running_no = '" . $deviceimei . "'  AND difference_fuel>0 AND (created_on >= '" . $from_date . "' AND created_on <= '" . $to_date . "') AND type_id ='2') A,(SELECT SUM(difference_fuel) as dipdiff FROM fuel_fill_dip_report WHERE running_no = '" . $vehicle . "' AND difference_fuel<0 AND (created_on >= '" . $from_date . "' AND created_on <= '" . $to_date . "') AND type_id ='1') B");
					//  echo "SELECT * FROM (SELECT SUM(difference_fuel) as filldiff FROM fuel_fill_dip_report WHERE running_no = '".$vehicle."'  AND difference_fuel>0 AND (created_on >= '".$from_date."' AND created_on <= '".$to_date."') AND type_id ='2') A,(SELECT SUM(difference_fuel) as dipdiff FROM fuel_fill_dip_report WHERE running_no = '".$vehicle."' AND difference_fuel<0 AND (created_on >= '".$from_date."' AND created_on <= '".$to_date."') AND type_id ='1') B";exit;
					$filldip = $query_filldip->row();

					$fill_ltr = $filldip->filldiff;

					$dip_ltr = $filldip->dipdiff;

					$fuel_ltr = $fill_ltr + $dip_ltr;


					$query1 = $this->db->query("SELECT deviceimei as v_running_no,vehiclename,device_config_type from vehicletbl where deviceimei = '" . $deviceimei . "'");
					$vehicle_number = $query1->row();
					$vehicle_register_number = $vehicle_number->vehiclename;

					$data['consumed_fuel'] = null;
					$data['distance'] = null;
					$data['start_date'] = null;
					$data['end_date'] = null;

					if ($fuel_data) {

						$call_distance = null;
						$set_dis = 0;

						$call_fuel = null;
						$set_fuel = 0;
						$i = 0;

						$f_length = count($fuel_data) - 1;

						$n = count($fuel_data);

						$end_meter = $fuel_data[0]->odometer;
						$start_meter = $fuel_data[$n - 1]->odometer;

						$end_fuel = $fuel_data[0]->litres;
						$start_fuel = $fuel_data[$n - 1]->litres;
						$distance = $end_meter - $start_meter;


						if ($fuel_ltr < 0) {
							$fl = 0;
						} else {
							$fl = $fuel_ltr;
						}

						//echo $start_fuel." +". $fl."-".$end_fuel; exit();
						$cf = $start_fuel + $fl - $end_fuel;

						if ($cf < 0) {
							$cunsumed_fl = -1 * $cf;
						} else {
							$cunsumed_fl = $cf;
						}

						if ($cunsumed_fl != 0) {
							$mileage = $distance / $cunsumed_fl;
						} else {
							$mileage = 0;
						}

						$data['start_date'] = $from_date;
						$data['end_date'] = $to_date;
						$data['fill_fuel'] = round($fill_ltr, 2);
						$data['consumed_fuel'] = $cunsumed_fl;
						$data['start_odo'] = $start_meter;
						$data['end_odo'] = $end_meter;
						$data['distance'] = round($distance, 2);
						$data['mileage'] = round($mileage, 2);
						$data['vehicle'] = $vehicle_register_number;


						$this->response($data);
					} else {
						$data['start_date'] = $from_date;
						$data['end_date'] = $to_date;
						$data['fill_fuel'] = '';
						$data['consumed_fuel'] = '';
						$data['start_odo'] = '';
						$data['end_odo'] = '';
						$data['distance'] = '';
						$data['mileage'] = '';
						$data['vehicle'] = '';


						$this->response($data);
					}
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}


	public function geolocation_get()
	{
		$headers = $this->input->request_headers();
		$valid_status = 1;
		if ($headers == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'headers is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;
				$roleid = $result['data']->roleid;
				$data['geolocation'] = $this->api_model->geolocation($client_id, $userid, $roleid);

				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}



	public function geofence_report_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$geolocation_id = $this->input->post('geolocation_id');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				// $data['status'] = 1;
				$client_id = $result['data']->client_id;
				$user_id = $result['data']->userid;
				$vehicle_data = $this->api_model->imeidetails($deviceimei);
				$vehicle = ($vehicle_data == '') ? 'all' : $vehicle_data->vehicleid;
				$geolocation_id = ($geolocation_id == 0) ? '' : $geolocation_id;

				$data['geofence_report'] = $this->api_model->geofence_report($from_date, $to_date, $vehicle, $geolocation_id, $client_id, $user_id);

				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}


	public function hublocation_get()
	{
		$headers = $this->input->request_headers();
		$valid_status = 1;
		if ($headers == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'headers is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;

				$data['hublocation'] = $this->api_model->hublocation($client_id);

				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}


	public function hubpoint_report_post()
	{
		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				// $data['status'] = 1;
				$client_id = $result['data']->client_id;
				$user_id = $result['data']->userid;
				$vehicle_data = $this->api_model->imeidetails($deviceimei);
				$vehicle = $vehicle_data->vehicleid;

				$data['hup_report'] = $this->api_model->hubpoint_report($from_date, $to_date, $vehicle, $client_id);

				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}

	public function acvehiclelist_post()
	{
		$headers = $this->input->request_headers();
		$valid_status = 1;
		if ($headers == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'headers is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;

				$data['acvehiclelist'] = $this->api_model->acvehiclelist($client_id);
				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}


	public function genric_ac_report_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$time = $this->input->post('time');
		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['ac_reportlist'] = $this->api_model->ac_report_list($from_date, $to_date, $deviceimei, $time);
					$this->response($data);
				} else {
					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function vehicle_consolidateold_post()
	{

		$headers = $this->input->request_headers();
		$deviceimei = $this->input->post('deviceimei');
		$daytype = $this->input->post('daytype');

		if ($daytype == 1) // weeks
		{
			$deviceimei = $this->input->post('deviceimei');
			$from_date = date('Y-m-d', strtotime('-7 Day'));
			$to_date = date('Y-m-d');
		} elseif ($daytype == 2) // Months
		{
			$deviceimei = $this->input->post('deviceimei');
			$from_date = date('Y-m-d', strtotime('-30 Day'));
			$to_date = date('Y-m-d');
		} else {
			$deviceimei = $this->input->post('deviceimei');
			$from_date = $this->input->post('from_date');
			$to_date = $this->input->post('to_date');
		}
		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);
			//print_r($result['data']); die;
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;
				$role = $result['data']->roleid;

				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				//print_r($vehicle_data['vehicle_details']); die;

				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$consolidate_data = $this->api_model->consolidatedata_json($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

					$data['distance'] = $consolidate_data->distance;
					$data['milege'] = $consolidate_data->fuel_milege;
					$data['fuel_fill_litre'] = $consolidate_data->fuel_fill_litre;
					$data['fuel_dip_litre'] = $consolidate_data->fuel_dip_litre;
					$data['fuel_consumed_litre'] = $consolidate_data->fuel_consumed_litre;
					$data['moving_duration'] = $this->converthmi($consolidate_data->moving_duration);
					$data['idle_duration'] = $this->converthmi($consolidate_data->idle_duration);
					$data['parking_duration'] = $this->converthmi($consolidate_data->parking_duration);
					$data['ac_duration'] = $this->converthmi($consolidate_data->ac_duration);
					$data['totalrpm'] = $this->converthmi_rpm($consolidate_data->totalrpm);
					$this->response($data);
				} elseif ($deviceimei == 0) {
					$consolidate_data = $this->api_model->consolidatedata_json($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

					$data['distance'] = $consolidate_data->distance;
					$data['milege'] = $consolidate_data->fuel_milege;
					$data['fuel_fill_litre'] = $consolidate_data->fuel_fill_litre;
					$data['fuel_dip_litre'] = $consolidate_data->fuel_dip_litre;
					$data['fuel_consumed_litre'] = $consolidate_data->fuel_consumed_litre;
					$data['moving_duration'] = $this->converthmi($consolidate_data->moving_duration);
					$data['idle_duration'] = $this->converthmi($consolidate_data->idle_duration);
					$data['parking_duration'] = $this->converthmi($consolidate_data->parking_duration);
					$data['ac_duration'] = $this->converthmi($consolidate_data->ac_duration);
					$data['totalrpm'] = $this->converthmi_rpm($consolidate_data->totalrpm);
					$this->response($data);
				} else {
					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}
	public function vehicle_consolidate_post()
	{

		$headers = $this->input->request_headers();
		// $deviceimei =$this->input->post('deviceimei');
		$deviceimei = 0;
		$daytype = 1;
		$from_date = date('Y-m-d', strtotime('-7 Day'));
		$to_date = date('Y-m-d');

		$valid_status = 1;
		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);
			//print_r($result['data']); die;
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;
				$role = $result['data']->roleid;

				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				$consolidate_data = $this->api_model->consolidatedata_json($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

				$distance_chart = $this->api_model->consolidate_distance_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$moving_chart = $this->api_model->consolidate_moving_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$idle_chart = $this->api_model->consolidate_idle_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$parking_chart = $this->api_model->consolidate_park_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$ac_chart = $this->api_model->consolidate_ac_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$fuelfill_chart = $this->api_model->consolidate_fuelfill_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$fueldip_chart = $this->api_model->consolidate_fueldip_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$fuelconsume_chart = $this->api_model->consolidate_fuelconsume_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$fuelmilege_chart = $this->api_model->consolidate_fuelmilege_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
				$rpm_chart = $this->api_model->consolidate_rpm_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

				$moving_chart = ($moving_chart == NULL) ? array() : $moving_chart;
				$idle_chart = ($idle_chart == NULL) ? array() : $idle_chart;
				$parking_chart = ($parking_chart == NULL) ? array() : $parking_chart;
				$ac_chart = ($ac_chart == NULL || $ac_chart == 0) ? array() : $ac_chart;
				$fuelfill_chart = ($fuelfill_chart == NULL) ? array() : $fuelfill_chart;
				$fuelconsume_chart = ($fuelconsume_chart == NULL) ? array() : $fuelconsume_chart;
				$fuelmilege_chart = ($fuelmilege_chart == NULL) ? array() : $fuelmilege_chart;
				$fueldip_chart = ($fueldip_chart == NULL) ? array() : $fueldip_chart;
				$rpm_chart = ($rpm_chart == NULL) ? array() : $rpm_chart;

				// $moving_dur = $this->converthmi($consolidate_data->moving_duration);
				// $idle_dur =$this->converthmi($consolidate_data->idle_duration);
				// $parking_dur = $this->converthmi($consolidate_data->parking_duration);
				// $ac_dur = $this->converthmi($consolidate_data->ac_duration);	
				// $rpm_dur = $this->converthmi_rpm($consolidate_data->totalrpm);

				$moving_dur = ($consolidate_data->moving_duration * 60);
				$idle_dur = ($consolidate_data->idle_duration * 60);
				$parking_dur = ($consolidate_data->parking_duration * 60);
				$ac_dur = ($consolidate_data->ac_duration * 60);
				$rpm_dur = ($consolidate_data->totalrpm * 60);

				$data['distance_data'] = array(
					'distance' => round($consolidate_data->distance),
					'distance_chart' => $distance_chart
				);

				$data['moving_data'] = array(
					'moving_duration' => $moving_dur,
					'moving_chart' => $moving_chart
				);

				$data['idle_data'] = array(
					'idle_duration' => $idle_dur,
					'idle_chart' => $idle_chart
				);

				$data['parking_data'] = array(
					'parking_duration' => $parking_dur,
					'parking_chart' => $parking_chart
				);

				$data['ac_data'] = array(
					'ac_duration' => $ac_dur,
					'ac_chart' => $ac_chart
				);


				$data['fuelfill_data'] = array(
					'fuelfill_litres' => round($consolidate_data->fuel_fill_litre),
					'fuellfill_chart' => $fuelfill_chart
				);

				$data['fueldip_data'] = array(
					'fueldip_litres' => round($consolidate_data->fuel_dip_litre),
					'fuelldip_chart' => $fueldip_chart
				);

				$data['fuelconsume_data'] = array(
					'fuelconsume_litres' => round($consolidate_data->fuel_consumed_litre),
					'fuelconsume_chart' => $fuelconsume_chart
				);

				$data['fuelmilege_data'] = array(
					'fuel_milege' => round($consolidate_data->distance / $consolidate_data->fuel_consumed_litre),
					'fuelmilege_chart' => $fuelmilege_chart
				);

				$data['totalrpm_data'] = array(
					'rpm_duration' => $rpm_dur,
					'rpm_chart' => $rpm_chart
				);



				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}
	public function converthmi($value)
	{
		//     $duration=$value;//Parking Duration
		//     $hours = floor($duration / 60);
		//     $min = $duration - ($hours * 60); 
		//     $second = $duration % 60;
		//   return  $tot_value=$hours.":".$min.":".$second;
		$ss = $value * 60;
		$m = floor(($ss % 3600) / 60);
		$h = floor(($ss % 86400) / 3600);
		$d = floor(($ss % 2592000) / 86400);
		// $M = floor($ss/2592000);

		return "$d days, $h hours:$m minutes";
	}

	public function converthmi_rpm($value)
	{
		$ss = $value;
		$m = floor(($ss % 3600) / 60);
		$h = floor(($ss % 86400) / 3600);
		$d = floor(($ss % 2592000) / 86400);
		// $M = floor($ss/2592000);

		return "$d days, $h hours:$m minutes";
		// $d = floor(($ss%2592000)/86400);
		// $load_rpm_duration=$value;//Load RPM Duration
		// $hours = floor($load_rpm_duration / 3600);
		//  $min = ($load_rpm_duration / 60) % 60;
		//  $second = $load_rpm_duration % 60;
		//  return  $tot_loadrpm=$hours.":".$min;
	}



	public function consolidate_singlebox_post()
	{

		$headers = $this->input->request_headers();
		$deviceimei = $this->input->post('deviceimei');
		$daytype = $this->input->post('daytype');
		$reporttype = $this->input->post('reporttype');

		$reporttype =  explode(",", $reporttype);
		// print_r($reporttype);
		// exit;
		if ($daytype == 1) // weeks
		{
			$deviceimei = $this->input->post('deviceimei');
			$from_date = date('Y-m-d', strtotime('-7 Day'));
			$to_date = date('Y-m-d');
		} elseif ($daytype == 2) // Months
		{
			$deviceimei = $this->input->post('deviceimei');
			$from_date = date('Y-m-d', strtotime('-30 Day'));
			$to_date = date('Y-m-d');
		} else {
			$deviceimei = $this->input->post('deviceimei');
			$from_date = $this->input->post('from_date');
			$to_date = $this->input->post('to_date');
		}
		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);
			//print_r($result['data']); die;
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;
				$role = $result['data']->roleid;

				$consolidate_data = $this->api_model->consolidatedata_json($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

				if (in_array(1, $reporttype)) {
					$distance_chart = $this->api_model->consolidate_distance_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
					$data['distance_data'] = array(
						'distance' => round($consolidate_data->distance),
						'distance_chart' => $distance_chart
					);
				}
				if (in_array(2, $reporttype)) {
					$moving_chart = $this->api_model->consolidate_moving_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
					$moving_dur = $consolidate_data->moving_duration;

					$data['moving_data'] = array(
						'moving_duration' => $moving_dur,
						'moving_chart' => $moving_chart
					);
				}
				if (in_array(3, $reporttype)) {
					$idle_chart = $this->api_model->consolidate_idle_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
					$idle_dur = $consolidate_data->idle_duration;
					$data['idle_data'] = array(
						'idle_duration' => $idle_dur,
						'idle_chart' => $idle_chart
					);
				}
				if (in_array(4, $reporttype)) {
					$parking_chart = $this->api_model->consolidate_park_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
					$parking_dur = $consolidate_data->parking_duration;
					$data['parking_data'] = array(
						'parking_duration' => $parking_dur,
						'parking_chart' => $parking_chart
					);
				}
				if (in_array(5, $reporttype)) {
					$ac_chart = $this->api_model->consolidate_ac_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
					$ac_chart = ($ac_chart == NULL || $ac_chart == 0) ? array() : $ac_chart;

					$ac_dur = round($consolidate_data->ac_duration);
					$data['ac_data'] = array(
						'ac_duration' => $ac_dur,
						'ac_chart' => $ac_chart
					);
				}
				if (in_array(6, $reporttype)) {
					$fuelfill_chart = $this->api_model->consolidate_fuelfill_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

					$fuelfill_chart = ($fuelfill_chart == NULL || $fuelfill_chart == 0) ? array() : $fuelfill_chart;
					$data['fuelfill_data'] = array(
						'fuelfill_litres' => round($consolidate_data->fuel_fill_litre),
						'fuellfill_chart' => $fuelfill_chart
					);
				}
				if (in_array(7, $reporttype)) {
					$fueldip_chart = $this->api_model->consolidate_fueldip_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);
					$fueldip_chart = ($fueldip_chart == NULL || $fueldip_chart == 0) ? array() : $fueldip_chart;

					$data['fueldip_data'] = array(
						'fueldip_litres' => round($consolidate_data->fuel_dip_litre),
						'fuelldip_chart' => $fueldip_chart
					);
				}
				if (in_array(8, $reporttype)) {

					$fuelconsume_chart = $this->api_model->consolidate_fuelconsume_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

					$fuelconsume_chart = ($fuelconsume_chart == NULL || $fuelconsume_chart == 0) ? array() : $fuelconsume_chart;
					$data['fuelconsume_data'] = array(
						'fuelconsume_litres' => round($consolidate_data->fuel_consumed_litre),
						'fuelconsume_chart' => $fuelconsume_chart
					);
				}
				if (in_array(9, $reporttype)) {

					$fuelmilege_chart = $this->api_model->consolidate_fuelmilege_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

					$fuelmilege_chart = ($fuelmilege_chart == NULL || $fuelmilege_chart == 0) ? array() : $fuelmilege_chart;
					$data['fuelmilege_data'] = array(
						'fuel_milege' => round($consolidate_data->distance / $consolidate_data->fuel_consumed_litre),
						'fuelmilege_chart' => $fuelmilege_chart
					);
				}
				if (in_array(10, $reporttype)) {

					$rpm_chart = $this->api_model->consolidate_rpm_chart($deviceimei, $from_date, $to_date, $client_id, $userid, $role);

					$rpm_chart = ($rpm_chart == NULL || $rpm_chart == 0) ? array() : $rpm_chart;
					$rpm_dur = round($consolidate_data->totalrpm);
					$data['totalrpm_data'] = array(
						'rpm_duration' => $rpm_dur,
						'rpm_chart' => $rpm_chart
					);
				}

				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}


	public function genric_drum_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$time = $this->input->post('time');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['drum_report'] = $this->api_model->drum_report_list($from_date, $to_date, $deviceimei, $time);
					$this->response($data);
				} else {
					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function genric_bucket_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$time = $this->input->post('time');
		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['bucket_report'] = $this->api_model->bucket_report_list($from_date, $to_date, $deviceimei, $time);

					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}



	public function immoblizer_post()
	{

		$headers = $this->input->request_headers();
		$password = $this->input->post('password');
		$newp = md5($password);
		$deviceimei = $this->input->post('deviceimei');
		$digit_output = $this->input->post('digit_output');
		$status = ($digit_output == 1) ? 1 : 0;
		$address = $this->input->post('address');

		$valid_status = 1;
		if ($deviceimei == '' || $password == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$user_id = $result['data']->userid;
				$dealer_id = $result['data']->dealer_id;
				$subdealer_id = $result['data']->subdealer_id;
				$data['user_details'] = $this->api_model->user_details($user_id, $newp);
				if ($user_id == $data['user_details']->userid) {
					$data1 = array(
						'client_id' => $client_id,
						'user_id' => $user_id,
						'dealer_id' => $dealer_id,
						'subdealer_id' => $subdealer_id,
						'vehicle_id' => $deviceimei,
						'address' => $address,
						'created_by' => $user_id,
						'status' => $status
					);

					$this->db->insert('immoblizer_data', $data1);

					$data2['status'] = 1;
					$data2['message'] = 'Insert Successfully';
					$this->response($data2);
				} else {
					$data1['status'] = 0;
					$data1['message'] = 'Password is mismatch';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function alerttypes_get()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$data['alerttypes'] = $this->api_model->alerttypes();

			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function alertreport_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$alert_id = $this->input->post('alert_id');
		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '' || $alert_id == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['alert_report'] = $this->api_model->alert_report($from_date, $to_date, $deviceimei, $alert_id);

					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}


	public function alert_settings_get()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		//print_r($result);exit;
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['alert_settings'] = $this->api_model->alert_settings($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function update_alertsettings_post()
	{

		$headers = $this->input->request_headers();
		$update_data = $this->input->post();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$update = $this->api_model->update_settings($update_data, $client_id);
			if ($update) {
				$data['message'] = 'Update Successfully';
				$this->response($data);
			} else {
				$data['message'] = 'Data Not Update......';
				$this->response($data);
			}
		} else {
			$this->response($result);
		}
	}

	public function vehicle_settings_get()
	{

		$headers = $this->input->request_headers();
		$deviceimei = $this->input->get('deviceimei');

		$result = $this->authorization_token->validateToken($headers['Authorization']);
		//print_r($result);exit;
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['vehicle_settings'] = $this->api_model->vehicle_settings($deviceimei);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function update_vehiclesettings_post()
	{

		$headers = $this->input->request_headers();
		$update_data = $this->input->post();
		$deviceimei = $update_data['deviceimei'];
		unset($update_data['deviceimei']);
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$update = $this->api_model->update_vehiclesettings($update_data, $deviceimei);
			if ($update) {
				$data['message'] = 'Update Successfully';
				$this->response($data);
			} else {
				$data['message'] = 'Data Not Update......';
				$this->response($data);
			}
		} else {
			$this->response($result);
		}
	}


	public function rpmvehicle_get()
	{
		$headers = $this->input->request_headers();
		$valid_status = 1;
		if ($headers == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'headers is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;
				$roleid = $result['data']->roleid;
				$data['rpm_vehicles'] = $this->api_model->rpm_vehicle($client_id, $userid, $roleid);
				$this->response($data);
			} else {
				$this->response($result);
			}
		}
	}


	public function genric_enginerpm_post()
	{

		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$time = $this->input->post('time');

		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {
			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {
				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {

					$normal_data = $this->genericreport_model->normal_rpm($from_date, $to_date, $deviceimei, $time);
					$load_data = $this->genericreport_model->load_rpm($from_date, $to_date, $deviceimei, $time);
					$overload_data = $this->genericreport_model->overload_rpm($from_date, $to_date, $deviceimei, $time);

					$merge_array = array_merge($normal_data, $load_data, $overload_data);
					usort($merge_array, function ($a, $b) {
						return strtotime(($a->start_day)) - strtotime(($b->start_day));
					});


					$data['rpm_data'] = $merge_array;

					$this->response($data);
				} else {
					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}
	//===================================26-05-2022====================================
	public function add_tripplan1_POST()
	{
		$headers = $this->input->request_headers();
		$trip_id = $this->input->post('trip_id');
		$poc_number = $this->input->post('poc_number');
		$vehicleid = $this->input->post('vehicleid');
		$start_location = $this->input->post('start_location');
		$end_location = $this->input->post('end_location');
		$pl_start_date = $this->input->post('pl_start_date');
		$pl_end_date = $this->input->post('pl_end_date');

		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$tripdata = array(
				'trip_id' => $trip_id,
				'client_id' => $result['data']->client_id,
				'vehicleid' => $vehicleid,
				'start_location' => $start_location,
				'end_location' => $end_location,
				'poc_number' => $poc_number,
				'pl_start_date' => $pl_start_date,
				'pl_end_date' => $pl_end_date,
				'pl_duration' => $this->input->post('pl_duration'),
				'pl_km' => $this->input->post('pl_km'),
				'status' => 1,
			);
			$lastid = $this->db->insert('zigma_plantrip', $tripdata);
			//                    $lastid = $this->route_model->save_zigmatrip($data);
			if ($lastid) {
				$data['message'] = 'Successfully Trip added';
				$this->response($data);
			} else {
				$data['message'] = 'Data Not Add......';
				$this->response($data);
			}

			//                    echo json_encode($lastid);

		} else {
			$this->response($result);
		}
	}
	public function get_tripID_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$client_id = $result['data']->client_id;
			$getid = $this->api_model->gettripid($client_id);
			if ($getid) {
				$data['tripid'] = $getid->trip_id + 1;
				$this->response($data);
			} else {
				$data['tripid'] = 1;
				$this->response($data);
			}
		} else {
			$this->response($result);
		}
	}
	public function tripplan_list_get($status = null)
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['tripplanlist'] = $this->api_model->tripplanlist($client_id, $status);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}
	public function tripplan_edit_GET($tripplainid = null)
	{
		// print_r($tripplainid);die;
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['tripplanedit'] = $this->api_model->tripplanedit($client_id, $tripplainid);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function add_tripplan_POST()
	{
		$headers = $this->input->request_headers();

		$trip_id = $this->input->post('trip_id');
		$poc_number = $this->input->post('poc_number');
		$vehicleid = $this->input->post('vehicleid');
		$start_location = $this->input->post('start_location');
		$end_location = $this->input->post('end_location');
		$pl_start_date = $this->input->post('pl_start_date');
		$pl_end_date = $this->input->post('pl_end_date');


		$new_vehiclename = $this->input->post('newvehiclename');
		$old_vehiclename = $this->input->post('oldvehiclename');

		$route_namedevi = implode(', ', $this->input->post('route_namedevi'));;
		$drivermobile = $this->input->post('drivermobile');

		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$client_id = $result['data']->client_id;
			if ($new_vehiclename == $old_vehiclename) {

				$tripdata = array(
					'trip_id' => $trip_id==0?time():$trip_id,
					'client_id' => $result['data']->client_id,
					'vehicleid' => $vehicleid,
					'start_location' => $start_location,
					'end_location' => $end_location,
					'poc_number' => $poc_number,
					'pl_start_date' => $pl_start_date,
					'pl_end_date' => $pl_end_date,
					'pl_duration' => $this->input->post('pl_duration'),
					'pl_km' => $this->input->post('pl_km'),
					'route_namedevi' => $route_namedevi,
					'drivermobile' => $drivermobile,
					'status' => 1,
				);
				$lastid = $this->db->insert('zigma_plantrip', $tripdata);

				$data1['status'] = 1;
				$data1['message'] = 'Trip Insert Successfully';
				$this->response($data1);
			} else {

				$query = $this->db->query("select * from vehicletbl where vehicleid = '" . $vehicleid . "' ");
				$getimei = $query->row();
				$data1 = array(
					'deviceimei' => $getimei->deviceimei,
					'vehiclename' => $old_vehiclename,
					'client_id' => $client_id,
					'work_startdate' => $getimei->createdon,
					'work_enddate' => date('Y-m-d H:i:s'),
					'createdon' => date('Y-m-d H:i:s'),
					'createdby' => $result['data']->userid,
				);

				$vehicle_exlist =  $this->db->insert('vehicle_expirelist', $data1);

				$vehicleid = $this->input->post('vehicleid');
				$data2 = array(
					'vehiclename' => $new_vehiclename,
					'registrationnumber' => $new_vehiclename,
					'modelnumber' => $new_vehiclename,
					// 'updatedon' => date('Y-m-d H:i:s'),
					'updatedby' => $result['data']->userid,
					'ipaddress' => $this->input->ip_address(),
				);
				$this->db->where('vehicleid', $vehicleid);
				$query = $this->db->update('vehicletbl', $data2);
				// update vehicletbl_2
				$this->db->where('vehicleid', $vehicleid);
				$query1 = $this->db->update('vehicletbl_2', $data2);


				$tripdata = array(
					'trip_id' => $trip_id,
					'client_id' => $result['data']->client_id,
					'vehicleid' => $vehicleid,
					'start_location' => $start_location,
					'end_location' => $end_location,
					'poc_number' => $poc_number,
					'pl_start_date' => $pl_start_date,
					'pl_end_date' => $pl_end_date,
					'pl_duration' => $this->input->post('pl_duration'),
					'pl_km' => $this->input->post('pl_km'),
					'route_namedevi' => $route_namedevi,
					'drivermobile' => $drivermobile,
					'status' => 1,
				);
				$lastid = $this->db->insert('zigma_plantrip', $tripdata);

				$data3['status'] = 1;
				$data3['message'] = 'Vehicle name Change and Trip Insert Successfully';
				$this->response($data3);
			}
		} else {
			$this->response($result);
		}
	}


	public function tripplan_update_POST()
	{
		$headers = $this->input->request_headers();

		//	$trip_id =$this->input->post('trip_id');
		$poc_number = $this->input->post('poc_number');
		$vehicleid = $this->input->post('vehicleid');
		$start_location = $this->input->post('start_location');
		$end_location = $this->input->post('end_location');
		$pl_start_date = $this->input->post('pl_start_date');
		$pl_end_date = $this->input->post('pl_end_date');
		$tripplanid = $this->input->post('id');

		$new_vehiclename = $this->input->post('newvehiclename');
		$old_vehiclename = $this->input->post('oldvehiclename');

		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$client_id = $result['data']->client_id;
			if ($new_vehiclename == $old_vehiclename) {

				$tripdata = array(
					'vehicleid' => $vehicleid,
					'start_location' => $start_location,
					'end_location' => $end_location,
					'poc_number' => $poc_number,
					'pl_start_date' => $pl_start_date,
					'pl_end_date' => $pl_end_date,
					'pl_duration' => $this->input->post('pl_duration'),
					'pl_km' => $this->input->post('pl_km'),
				);

				$this->db->where('id', $tripplanid);
				$lastid = $this->db->update('zigma_plantrip', $tripdata);

				$data1['status'] = 1;
				$data1['message'] = 'Trip Update Successfully';
				$this->response($data1);
			} else {

				$query = $this->db->query("select * from vehicletbl where vehicleid = '" . $vehicleid . "' ");
				$getimei = $query->row();
				$data1 = array(
					'deviceimei' => $getimei->deviceimei,
					'vehiclename' => $old_vehiclename,
					'client_id' => $client_id,
					'work_startdate' => $getimei->createdon,
					'work_enddate' => date('Y-m-d H:i:s'),
					'createdon' => date('Y-m-d H:i:s'),
					'createdby' => $result['data']->userid,
				);

				$vehicle_exlist =  $this->db->insert('vehicle_expirelist', $data1);

				$vehicleid = $this->input->post('vehicleid');
				$data2 = array(
					'vehiclename' => $new_vehiclename,
					'registrationnumber' => $new_vehiclename,
					'modelnumber' => $new_vehiclename,
					// 'updatedon' => date('Y-m-d H:i:s'),
					'updatedby' => $result['data']->userid,
					'ipaddress' => $this->input->ip_address(),
				);
				$this->db->where('vehicleid', $vehicleid);
				$query = $this->db->update('vehicletbl', $data2);
				// update vehicletbl_2
				$this->db->where('vehicleid', $vehicleid);
				$query1 = $this->db->update('vehicletbl_2', $data2);


				$tripdata = array(
					'vehicleid' => $vehicleid,
					'start_location' => $start_location,
					'end_location' => $end_location,
					'poc_number' => $poc_number,
					'pl_start_date' => $pl_start_date,
					'pl_end_date' => $pl_end_date,
					'pl_duration' => $this->input->post('pl_duration'),
					'pl_km' => $this->input->post('pl_km'),
				);
				$this->db->where('id', $tripplanid);
				$lastid = $this->db->update('zigma_plantrip', $tripdata);

				$data3['status'] = 1;
				$data3['message'] = 'Vehicle name Change and Trip Update Successfully';
				$this->response($data3);
			}
		} else {
			$this->response($result);
		}
	}


	public function tripplan_report_POST()
	{
		$fromdate = $this->input->post('fromdate');
		$todate = $this->input->post('todate');
		$status = $this->input->post('status');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$client_id = $result['data']->client_id;
			$zigmatrip_plan = $this->api_model->zigma_triplist($fromdate, $todate, $status, $client_id);
			$data['zigmatrip_plan'] = $zigmatrip_plan;
			if ($zigmatrip_plan) {
				$data['message'] = 'Trip plan report';
				$this->response($data);
			} else {
				$data['message'] = 'Data Not Add......';
				$this->response($data);
			}
		} else {
			$this->response($result);
		}
	}

	public function triplocation_list_GET()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		//print_r($result);exit;
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['triplocation'] = $this->api_model->location_triplist($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function tripvehicle_list_GET()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		//print_r($result);exit;
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['tripvehiclelist'] = $this->api_model->vehicle_triplist($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function trip_vehiclechange_POST()
	{
		$new_vehiclename = $this->input->post('newvehicle_names');
		$old_vehiclename = $this->input->post('oldvehicle_names');
		$vehicleid = $this->input->post('vehicleid');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$client_id = $result['data']->client_id;
			if ($new_vehiclename == $old_vehiclename) {
				$vehicleid = $this->input->post('vehicleid');
				$data = array(
					// 'updatedon'=>date('Y-m-d H:i:s'),
					'updatedby' => $result['data']->userid,
					'ipaddress' => $this->input->ip_address(),
				);

				$this->db->where('vehicleid', $vehicleid);
				$query = $this->db->update('vehicletbl', $data);
				$this->db->where('vehicleid', $vehicleid);
				$query1 = $this->db->update('vehicletbl_2', $data);
				if ($query1) {
					$data['message'] = 'Updated vehicle name';
					$this->response($data);
				} else {
					$data['message'] = 'Data Not Add......';
					$this->response($data);
				}
			} else {
				$query = $this->db->query("select * from vehicletbl where vehicleid = '" . $vehicleid . "' ");
				$getimei = $query->row();
				$data1 = array(
					'deviceimei' => $getimei->deviceimei,
					'vehiclename' => $old_vehiclename,
					'client_id' => $client_id,
					'work_startdate' => $getimei->createdon,
					'work_enddate' => date('Y-m-d H:i:s'),
					'createdon' => date('Y-m-d H:i:s'),
					'createdby' => $result['data']->userid,
				);

				$vehicle_exlist =  $this->db->insert('vehicle_expirelist', $data1);

				$vehicleid = $this->input->post('vehicleid');
				$data2 = array(
					'vehiclename' => $new_vehiclename,
					'registrationnumber' => $new_vehiclename,
					'modelnumber' => $new_vehiclename,
					// 'updatedon' => date('Y-m-d H:i:s'),
					'updatedby' => $result['data']->userid,
					'ipaddress' => $this->input->ip_address(),
				);
				$this->db->where('vehicleid', $vehicleid);
				$query = $this->db->update('vehicletbl', $data2);
				// update vehicletbl_2
				$this->db->where('vehicleid', $vehicleid);
				$query1 = $this->db->update('vehicletbl_2', $data2);
				if ($query1) {
					$data['message'] = 'Updated vehicle name';
					$this->response($data);
				} else {
					$data['message'] = 'Data Not Add......';
					$this->response($data);
				}
			}
		} else {
			$this->response($result);
		}
	}
	public function tripplan_map_get()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['tripplanmap'] = $this->api_model->trip_location_map($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}
	public function trip_notassignvehicles_get()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$userid = $result['data']->userid;
			$roleid = $result['data']->roleid;
			$data['notassign_vehicle'] = $this->api_model->notassign_vehicle($client_id, $userid, $roleid);

			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function trip_stschange_popup_post()
	{
		$id = $this->input->post('id');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$zigmatrip_plan_data = $this->api_model->zigmatrip_plan_data($id);
			$tripid = $zigmatrip_plan_data->trip_id;
			$vehicleid = $zigmatrip_plan_data->vehicleid;
			$deviceimei = $zigmatrip_plan_data->deviceimei;
			$data['startdate'] = $this->api_model->trip_actual_data($client_id, $tripid, $vehicleid);
			$start_date = $data['startdate']['startdatetime'];
			$end_date = date("Y-m-d H:i:s");
			$distance_data = $this->api_model->smart_distanceday_API($deviceimei, $start_date, $end_date, $device_type, $client_id);
			$distance = $distance_data->distance_km;
			$data['distance'] = $distance;
			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function trip_stschange_post()
	{
		$id = $this->input->post('id');

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$client_id = $result['data']->client_id;
			$data['newtriplist'] = $this->api_model->check_plantrip($client_id, $id);
			$deviceimei = $data['newtriplist']['deviceimei'];
			$start_date = $this->input->post('startdatetime');
			$end_date = $this->input->post('enddatetime');
			//=============================Get Idle duration===================================

			$idle_data = $this->Smartreport_model->smart_idleday($deviceimei, $start_date, $end_date);
			$idle_duration = $idle_data->idel_duration;
			//echo json_encode($idle_duration);die;
			$hours = floor($idle_duration / 60);
			$min = $idle_duration - ($hours * 60);
			$min = floor((($min -   floor($min / 60) * 60)) / 6);
			$second = $idle_duration % 60;
			$idle_hrs = $hours . " Hours " . $min . " Minutes";

			//=============================Get Idle duration===================================
			$park_data = $this->Smartreport_model->smart_parkday($deviceimei, $start_date, $end_date);
			$park_duration = $park_data->parking_duration;
			$hours = floor($park_duration / 60);
			$min = $park_duration - ($hours * 60);
			$min = floor((($min -   floor($min / 60) * 60)) / 6);
			$second = $park_duration % 60;
			$park_hrs = $hours . " Hours " . $min . " Minutes";

			//echo json_encode($idle_hrs);     
			//============================get Auto KM====================================                        

			$distance_data = $this->Smartreport_model->smart_distanceday($deviceimei, $start_date, $end_date, $device_type);
			$distance = $distance_data->distance_km;
			//================================================================================== 
			if ($data['newtriplist']['status'] == 1) {
				unset($data['newtriplist']['status']);
				$data['newtriplist']['trip_type'] = 'ApiManual';
				$data['newtriplist']['distance'] = $this->input->post('distance');
				$data['newtriplist']['create_datetime'] = $this->input->post('startdatetime');
				$data['newtriplist']['updated_datetime'] = $this->input->post('enddatetime');
				$data['newtriplist']['flag'] = 3;

				$this->db->insert('zigma_plantrip_report1', $data['newtriplist']);

				$update_status = array('status' => 3);
				$this->db->where('id', $id);
				$this->db->update('zigma_plantrip', $update_status);

				$data3['status'] = 1;
				$data3['tripsts'] = "Trip Completed";
				$this->response($data3);
			} elseif ($data['newtriplist']['status'] == 2) {

				$data1 = array(
					'distance' => $this->input->post('distance'),
					'e_lat' => $data['newtriplist']['e_lat'],
					'e_lng' => $data['newtriplist']['e_lng'],
					'trip_type' => 'ApiManual',
					'flag' => 3,
					'manual_idle_dur' => $idle_hrs,
					'parking_duration' => $park_hrs,
					'create_datetime' => $this->input->post('startdatetime'),
					'updated_datetime' => $this->input->post('enddatetime')
				);
				$this->db->where('trip_id', $data['newtriplist']['trip_id']);
				$this->db->where('client_id', $data['newtriplist']['client_id']);
				$this->db->where('vehicle_id', $data['newtriplist']['vehicle_id']);
				$this->db->update('zigma_plantrip_report1', $data1);
				$update_status = array('status' => 3);
				$this->db->where('id', $id);
				$this->db->update('zigma_plantrip', $update_status);
				$data3['status'] = 1;
				$data3['tripsts'] = "Trip Completed";
				$this->response($data3);
			} else {
				$data3['status'] = 0;
				$data3['tripsts'] = "Trip Not Found";
				$this->response($data3);
			}
		} else {
			$this->response($result);
		}
	}


	// public function trip_stschange_post() {
	//         $tripid = $this->input->post('id');
	//         $headers = $this->input->request_headers();
	//         $result = $this->authorization_token->validateToken($headers['Authorization']);
	//         if ($result['status'] == 1) {
	//             $data['status'] = 1;
	//             $data['tripsts'] = "Trip Completed";
	//             $client_id = $result['data']->client_id;
	//           //  $this->db->query("update zigma_plantrip set status = 3 where id=$tripid");
	//             $this->response($data);
	//         }else{
	//             $this->response($result);
	//         }       
	//     } 



	public function single_vehicle_map_post()
	{
		//          $param is   $tripreportID
		$client_id = $this->input->post('client_id');
		$trip_id = $this->input->post('trip_id');
		$vehicle_id = $this->input->post('vehicleid');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$timezone_minutes = $result['data']->timezone_minutes;
			$local_timezone = $timezone_minutes == 0 ? 'CURRENT_TIMESTAMP' : 'UTC_TIMESTAMP';


			$data['tripplanmap'] = $this->api_model->singlevehicle_map($client_id, $trip_id, $vehicle_id);
			if ($data['tripplanmap']->updated_datetime != '0000-00-00 00:00:00') {

				$todate = $data['tripplanmap']->updated_datetime;
				$fromdate = $data['tripplanmap']->create_datetime;
				$deviceimei = $data['tripplanmap']->deviceimei;
			} else {
				$todate = $data['tripplanmap']->updatedon;
				$fromdate = $data['tripplanmap']->create_datetime;
				$deviceimei = $data['tripplanmap']->deviceimei;
			}
			$data['tripplayback'] = $this->api_model->singlevehicle_playback($client_id, $deviceimei, $todate, $fromdate);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function trip_delete_post()
	{
		$tripid = $this->input->post('id');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$data['tripsts'] = "Trip Deleted";
			$client_id = $result['data']->client_id;
			$this->db->query("DELETE FROM zigma_plantrip WHERE id=$tripid AND status = 1");
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function edit_geolocation_post()
	{
		$location_id = $this->input->post('Location_Id');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$query = $this->db->query("SELECT Location_Id,Location_short_name,Lat,Lng,radius FROM location_list 
					WHERE Location_Id=$location_id AND client_id=$client_id");
			$data = $query->row();
			$this->response($data);
		} else {
			$this->response($result);
		}
	}



	public function geofence_list_get()
	{


		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$client_id = $result['data']->client_id;
			$userid = $result['data']->userid;
			$roleid = $result['data']->roleid;
			$geo_location_list = $this->api_model->geolocation($client_id, $userid, $roleid);
			if ($geo_location_list) {
				$data['status'] = 1;
				$data['geo_location_list'] = $geo_location_list;
			} else {
				$data['status'] = 0;
				$data['geo_location_list'] = array();
			}
			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function save_geofence_post()
	{

		$headers = $this->input->request_headers();
		$Location_Id = $this->input->post('Location_Id');
		$Location_short_name = $this->input->post('Location_short_name');
		$Lat = $this->input->post('Lat');
		$Lng = $this->input->post('Lng');
		$radius = $this->input->post('radius');

		$valid_status = 1;
		if ($Location_short_name == '' || $Lat == '' || $Lng == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {

				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;
				$dealer_id = $result['data']->dealer_id;
				$subdealer_id = $result['data']->subdealer_id;

				if ($Location_Id != '' && $Location_Id != null) {
					$data = array(
						'Location_short_name' => $Location_short_name,
						'Lat' => $Lat,
						'Lng' => $Lng,
						'radius' => $radius,
						'client_id' => $client_id,
						'dealer_id' => $dealer_id,
						'subdealer_id' => $subdealer_id,
						'UpdatedBy' => $userid,
						'UpdatedOn' => date('Y-m-d H:i:s')
					);
					$this->db->where('Location_Id', $Location_Id);
					$this->db->update('location_list', $data);

					$data2['status'] = 1;
					$data2['message'] = 'Update Data Successfully';
					$this->response($data2);
				} else {
					$data = array(
						'Location_short_name' => $Location_short_name,
						'Lat' => $Lat,
						'Lng' => $Lng,
						'radius' => $radius,
						'client_id' => $client_id,
						'dealer_id' => $dealer_id,
						'subdealer_id' => $subdealer_id,
						'CreatedBy' => $userid,
						'CreatedOn' => date('Y-m-d H:i:s'),
						'activecode' => 1
					);
					$this->db->insert('location_list', $data);

					$data1['status'] = 1;
					$data1['message'] = 'Insert Data Successfully';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}


	public function delete_geofence_get($id)
	{


		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {


			$query = $this->db->query("DELETE FROM location_list WHERE Location_Id=$id");
			if ($query) {
				$data['status'] = 1;
				$data['message'] = "Record Delete Successfully";
			} else {
				$data['status'] = 0;
				$data['message'] = "Not Deleted Record";
			}


			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function assign_geofencelist_get($Location_Id)
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$client_id = $result['data']->client_id;
			$userid = $result['data']->userid;
			$assign_geofencelist = $this->api_model->assign_locationlist($client_id, $userid, $Location_Id);
			if ($assign_geofencelist) {
				$data['status'] = 1;
				$data['assign_geofencelist'] = $assign_geofencelist;
			} else {
				$data['status'] = 0;
				$data['assign_geofencelist'] = array();
			}
			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function notassign_vehicles_post()
	{

		$headers = $this->input->request_headers();
		$Location_Id = $this->input->post('Location_Id');

		$valid_status = 1;
		if ($Location_Id == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {

				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;

				$notassign_vehicles = $this->api_model->notassign_vehicles($client_id, $Location_Id);


				$data1['status'] = 1;
				$data1['notassign_vehicles'] = $notassign_vehicles;
				$this->response($data1);
			} else {
				$this->response($result);
			}
		}
	}


	public function assign_geofence_vehicles_post()
	{

		$headers = $this->input->request_headers();
		$Location_Id = $this->input->post('Location_Id');
		$vehicle_id = $this->input->post('vehicle_id');

		$valid_status = 1;
		if ($Location_Id == '' || $vehicle_id == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);
			if ($result['status'] == 1) {

				$client_id = $result['data']->client_id;
				$userid = $result['data']->userid;

				$data = array(
					'geo_location_id' => $Location_Id,
					'vehicle_id' => $vehicle_id,
					'client_id' => $client_id,
					'created_by' => $userid,
					'created_datetime' => date('Y-m-d H:i:s'),
					'activecode' => 1
				);
				$this->db->insert('assign_geo_fenceing', $data);

				$data1['status'] = 1;
				$data1['message'] = 'Insert Data Successfully';
				$this->response($data1);
			} else {
				$this->response($result);
			}
		}
	}


	public function delete_assign_geofence_get($id)
	{


		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {


			$query = $this->db->query("DELETE FROM assign_geo_fenceing WHERE id=$id");
			if ($query) {
				$data['status'] = 1;
				$data['message'] = "Record Delete Successfully";
			} else {
				$data['status'] = 0;
				$data['message'] = "Not Deleted Record";
			}


			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function notification_alert_GET()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		//print_r($result);exit;
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['notification_alert'] = $this->api_model->notification_alert($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function ignitiononoff_post()
	{
		$headers = $this->input->request_headers();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$deviceimei = $this->input->post('deviceimei');
		$time = $this->input->post('time');
		$valid_status = 1;
		if ($from_date == '' || $to_date == '' || $deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				// $data['status'] = 1;
				$client_id = $result['data']->client_id;
				$vehicle_data['vehicle_details'] = $this->api_model->vehicledetails($deviceimei);
				if ($client_id == $vehicle_data['vehicle_details']->client_id) {
					$data['ign_onoff'] = $this->api_model->ign_onoff_list($from_date, $to_date, $deviceimei, $time);

					$this->response($data);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function vehicle_routelist_GET()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		//print_r($result);exit;
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['vehicle_route'] = $this->api_model->vehicle_routelist($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function safeparking_POST()
	{

		$headers = $this->input->request_headers();
		$safe_parking = $this->input->post('safe_park_status');
		$deviceimei = $this->input->post('deviceimei');
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$update_status = array('safe_parking' => $safe_parking);
			$this->db->where('deviceimei', $deviceimei);
			$this->db->update('vehicletbl', $update_status);
			$this->db->update('vehicletbl_2', $update_status);
			$client_id = $result['data']->client_id;
			if ($safe_parking == 1)
				$data['msg'] = "Safe Parking On";
			else
				$data['msg'] = "Safe Parking Off";
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function liveshare_POST()
	{

		$headers = $this->input->request_headers();
		$expiretime = $this->input->post('expiretime');
		$deviceimei = $this->input->post('deviceimei');
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$vehicle1 = $this->db->query("SELECT vehicleid from vehicletbl where deviceimei =$deviceimei");
			$vehicles = $vehicle1->row();
			$vehicle_id = $vehicles->vehicleid;

			$data['status'] = 1;
			$unique_id = rand(10, 10000);
			$livesharelink = array(
				'client_id' => $result['data']->client_id,
				'unique_id' => $unique_id,
				'expiretime' => $expiretime,
				'vehicleid' => $vehicle_id,
				'createdby' => $result['data']->userid
			);
			$query = $this->db->insert('sharelink_data', $livesharelink);
			$insert_id = $this->db->insert_id();
			$finalid = urlencode(base64_encode($insert_id));

			if ($query) {
				$data['msg'] = "Livelink  created";
				$data['Live_link'] = 'http://vts.trackingwings.com/Gfyt65jlkj4/' . $finalid;
			} else {
				$data['status'] = 1;
				$data['msg'] = "Livelink  Error";
			}

			$this->response($data);
		} else {
			$this->response($result);
		}
	}
	//============================Vehicle management===========================
	public function vehicle_service_type_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['vehicleServiceType'] = $this->api_model->vehicleServiceType();
			$this->response($data);
		} else {
			$this->response($result);
		}
	}
	public function paymentType_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['paymentType'] = $this->api_model->paymentType();
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_service_add_POST()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$client_id = $result['data']->client_id;
			$servicedata = array(
				'client_id' => $client_id,
				'vehicle_id' => $this->input->post('vehicle_id'),
				'service_type' => $this->input->post('service_type'),
				'purchase_product' => $this->input->post('purchase_product'),
				'purchase_amount' => $this->input->post('purchase_amount'),
				'payment_mode' => $this->input->post('payment_mode'),
				'mode_details' => $this->input->post('mode_details'),
				'purchase_date' => $this->input->post('purchase_date'),
				'description' => $this->input->post('description'),
				'reminder_date' => $this->input->post('reminder_date'),
				'reminder_km' => $this->input->post('reminder_km'),

			);
			$this->db->insert('vehicle_service', $servicedata);
			$data['status'] = 1;
			$data['message'] = 'Insert Data Successfully';
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_service_edit_GET()
	{
		$service_id = $this->input->get('service_id');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['service_edit'] = $this->api_model->vehicle_service_edit($service_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}
	public function vehicle_service_list_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['service_list'] = $this->api_model->vehicle_service_list($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_service_update_POST()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$client_id = $result['data']->client_id;
			$servicedata = array(
				'vehicle_id' => $this->input->post('vehicle_id'),
				'service_type' => $this->input->post('service_type'),
				'purchase_product' => $this->input->post('purchase_product'),
				'purchase_amount' => $this->input->post('purchase_amount'),
				'payment_mode' => $this->input->post('payment_mode'),
				'mode_details' => $this->input->post('mode_details'),
				'purchase_date' => $this->input->post('purchase_date'),
				'description' => $this->input->post('description'),
				'reminder_date' => $this->input->post('reminder_date'),
				'reminder_km' => $this->input->post('reminder_km'),
			);
			$service_id = $this->input->post('service_id');
			$this->db->where('service_id', $service_id);
			$this->db->where('client_id', $client_id);
			$this->db->update('vehicle_service', $servicedata);

			$data['status'] = 1;
			$data['message'] = 'Data Update Successfully';
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_service_action_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$service_id = $this->input->get('service_id');

			$data = array(
				'service_id' => $service_id,
				'createdby' => $result['data']->client_id,
				'createdon' => date('Y-m-d H:i:s'),
				'status' => '1',
			);

			$result = $this->db->insert('service_overdue', $data);

			if ($result) {

				$this->response('Completed');
			}
		}
	}

	public function vehicle_document_list_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['documnet_list'] = $this->api_model->vehicle_document_list($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	// public function vehicle_document_add_POST()
	// {
	// 	$headers = $this->input->request_headers();
	// 	$result = $this->authorization_token->validateToken($headers['Authorization']);
	// 	if ($result['status'] == 1) {
	// 		//===================image upload===============================

	// 		$imageName =$_FILES['image']['name'];
	// 		$tmp_name_1=$_FILES["image"]['tmp_name'];
	// 		$countfiles = count($_FILES['image']['name']); 
	// 		$img_type_id=$this->input->post('img_type_id');

	// 		// Looping all files
	// 		for($i=0;$i<$countfiles;$i++){

	// 			$expenses =  $imageName[$i];
	// 			$ext = pathinfo($expenses, PATHINFO_EXTENSION);
	// 			$filename='exp'.time();
	// 			$tmp_name = $tmp_name_1[$i];
	// 		if ($expenses != "") {
	// 			$image_file = do_upload($ext,$filename,$tmp_name);
	// 			} else {
	// 			$image_file = $this->input->post('hiddenimage_file');
	// 			}

	// 		//============================================================                
	// 		$client_id = $result['data']->client_id;
	// 		$documentdata = array(
	// 			'vehicle_id' => $this->input->post('vehicle_id'),
	// 			'polocy_no' => $this->input->post('polocy_no'),
	// 			'company_name' => $this->input->post('company_name'),
	// 			'client_id'=>$client_id,
	// 			'type' => $this->input->post('type'),
	// 			'start_date' => $this->input->post('start_date'),
	// 			'end_date' => $this->input->post('end_date'),
	// 			'fc_expiry_date' => $this->input->post('fc_expiry_date'),
	// 			'tax_expriy_date' => $this->input->post('tax_expriy_date'),
	// 			'permit_expiry_date' => $this->input->post('permit_expiry_date'),
	// 			'image_file' => $image_file,
	// 			'img_type_id' => $img_type_id,	
	// 			'dealer_id' => $result['data']->dealer_id,
	// 			'subdealer_id' => $result['data']->subdealer_id,
	// 			'createdon' => date('Y-m-d H:i:s'),
	// 			'createdby' => $client_id,
	// 		);

	// 		$this->db->insert('insurance_reminder', $documentdata);
	// 	}
	// 		$data['status'] = 1;
	// 		$data['message'] = 'Insert Data Successfully';
	// 		$this->response($data);
	// 	} else {
	// 		$this->response($result);
	// 	}
	// }

	public function vehicle_document_edit_GET()
	{
		$vehicle_id = $this->input->get('vehicle_id');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['document_edit'] = $this->api_model->vehicle_document_edit($vehicle_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_document_update_POST()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$front_insure_file = $_FILES["front_insure_file"]["name"];
			$ext = pathinfo($front_insure_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$front_insure_file_name = $_FILES["front_insure_file"]['tmp_name'];
			if ($front_insure_file != "") {
				$front_insure_file_up = do_upload($ext, $filename, $front_insure_file_name);
			} else {
				$front_insure_file_up = $this->input->post('hiddenfront_insure_file');
			}

			$back_insure_file = $_FILES["back_insure_file"]["name"];
			$ext = pathinfo($back_insure_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$back_insure_file_name = $_FILES["back_insure_file"]['tmp_name'];
			if ($back_insure_file != "") {
				$back_insure_file_up = do_upload($ext, $filename, $back_insure_file_name);
			} else {
				$back_insure_file_up = $this->input->post('hiddenback_insure_file');
			}

			$front_fc_file = $_FILES["front_fc_file"]["name"];
			$ext = pathinfo($front_fc_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$front_fc_file_name = $_FILES["front_fc_file"]['tmp_name'];
			if ($front_fc_file != "") {
				$front_fc_file_up = do_upload($ext, $filename, $front_fc_file_name);
			} else {
				$front_fc_file_up = $this->input->post('hiddenfront_fc_file');
			}

			$back_fc_file = $_FILES["back_fc_file"]["name"];
			$ext = pathinfo($back_fc_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$back_fc_file_name = $_FILES["back_fc_file"]['tmp_name'];
			if ($back_fc_file != "") {
				$back_fc_file_up = do_upload($ext, $filename, $back_fc_file_name);
			} else {
				$back_fc_file_up = $this->input->post('hiddenback_fc_file');
			}

			$front_rc_file = $_FILES["front_rc_file"]["name"];
			$ext = pathinfo($front_rc_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$front_rc_file_name = $_FILES["front_rc_file"]['tmp_name'];
			if ($front_rc_file != "") {
				$front_rc_file_up = do_upload($ext, $filename, $front_rc_file_name);
			} else {
				$front_rc_file_up = $this->input->post('hiddenfront_rc_file');
			}

			$back_rc_file = $_FILES["back_rc_file"]["name"];
			$ext = pathinfo($back_rc_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$back_rc_file_name = $_FILES["back_rc_file"]['tmp_name'];
			if ($back_rc_file != "") {
				$back_rc_file_up = do_upload($ext, $filename, $back_rc_file_name);
			} else {
				$back_rc_file_up = $this->input->post('hiddenback_rc_file');
			}

			$front_tax_file = $_FILES["front_tax_file"]["name"];
			$ext = pathinfo($front_tax_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$front_tax_file_name = $_FILES["front_tax_file"]['tmp_name'];
			if ($front_tax_file != "") {
				$front_tax_file_up = do_upload($ext, $filename, $front_tax_file_name);
			} else {
				$front_tax_file_up = $this->input->post('hiddenfront_tax_file');
			}

			$back_tax_file = $_FILES["back_tax_file"]["name"];
			$ext = pathinfo($back_tax_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$back_tax_file_name = $_FILES["back_tax_file"]['tmp_name'];
			if ($back_tax_file != "") {
				$back_tax_file_up = do_upload($ext, $filename, $back_tax_file_name);
			} else {
				$back_tax_file_up = $this->input->post('hiddenback_tax_file');
			}

			$front_permit_file = $_FILES["front_permit_file"]["name"];
			$ext = pathinfo($front_permit_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$front_permit_file_name = $_FILES["front_permit_file"]['tmp_name'];
			if ($front_permit_file != "") {
				$front_permit_file_up = do_upload($ext, $filename, $front_permit_file_name);
			} else {
				$front_permit_file_up = $this->input->post('hiddenfront_permit_file');
			}

			$back_permit_file = $_FILES["back_permit_file"]["name"];
			$ext = pathinfo($back_permit_file, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$back_permit_file_name = $_FILES["back_permit_file"]['tmp_name'];
			if ($back_permit_file != "") {
				$back_permit_file_up = do_upload($ext, $filename, $back_permit_file_name);
			} else {
				$back_permit_file_up = $this->input->post('hiddenback_permit_file');
			}

			//============================================================                
			$client_id = $result['data']->client_id;
			$documentdata = array(
				'vehicle_id' => $this->input->post('vehicle_id'),
				'polocy_no' => $this->input->post('polocy_no'),
				'company_name' => $this->input->post('company_name'),
				'type' => $this->input->post('type'),
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
				'fc_expiry_date' => $this->input->post('fc_expiry_date'),
				'tax_expriy_date' => $this->input->post('tax_expriy_date'),
				'permit_expiry_date' => $this->input->post('permit_expiry_date'),
				'rc_expiry_date' => $this->input->post('rc_expiry_date'),
				'front_insure_file' => $front_insure_file_up,
				'back_insure_file' => $back_insure_file_up,
				'front_rc_file' => $front_rc_file_up,
				'back_rc_file' => $back_rc_file_up,
				'front_fc_file' => $front_fc_file_up,
				'back_fc_file' => $back_fc_file_up,
				'front_tax_file' => $front_tax_file_up,
				'back_tax_file' => $back_tax_file_up,
				'front_permit_file' => $front_permit_file_up,
				'back_permit_file' => $back_permit_file_up,
				'dealer_id' => $result['data']->dealer_id,
				'subdealer_id' => $result['data']->subdealer_id,
				'createdon' => date('Y-m-d H:i:s'),
				'createdby' => $client_id,
			);

			$insurance_reminder_id = $this->input->post('insurance_reminder_id');

			if ($insurance_reminder_id) {
				$this->db->where('insurance_reminder_id', $insurance_reminder_id);
				$this->db->update('insurance_reminder', $documentdata);

				$data['status'] = 2;
				$data['message'] = 'Data Update Successfully';
			} else {
				$this->db->insert('insurance_reminder', $documentdata);
				$data['status'] = 1;
				$data['message'] = 'Data Inserted Successfully';
			}

			$this->response($data);
		} else {
			$this->response($result);
		}
	}
	//===================Fuel entry=========================
	public function vehicle_fuel_list_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['fuel_list'] = $this->api_model->vehicle_fuel_list($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_fuel_add_POST()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$client_id = $result['data']->client_id;
			$fueldata = array(
				'client_id' => $client_id,
				'vehicle_id' => $this->input->post('vehicle_id'),
				'kilo_meter' => $this->input->post('kilo_meter'),
				'fuel_liters' => $this->input->post('fuel_liters'),
				'fuel_amount' => $this->input->post('fuel_amount'),
				'fuel_date' => $this->input->post('fuel_date'),
				'bill_no' => $this->input->post('bill_no'),
				'payment_type_id' => $this->input->post('payment_type_id'),
				'createdby' => $client_id,
			);
			$this->db->insert('fuel_management', $fueldata);
			$data['status'] = 1;
			$data['message'] = 'Insert Data Successfully';
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_fuel_edit_GET()
	{
		$fuel_management_id = $this->input->get('fuel_management_id');
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['fuel_management_id'] = $this->api_model->vehicle_fuel_edit($fuel_management_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_fuel_update_POST()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$client_id = $result['data']->client_id;
			$fueldata = array(
				'client_id' => $client_id,
				'vehicle_id' => $this->input->post('vehicle_id'),
				'kilo_meter' => $this->input->post('kilo_meter'),
				'fuel_liters' => $this->input->post('fuel_liters'),
				'fuel_amount' => $this->input->post('fuel_amount'),
				'fuel_date' => $this->input->post('fuel_date'),
				'bill_no' => $this->input->post('bill_no'),
				'payment_type_id' => $this->input->post('payment_type_id'),
				'createdby' => $client_id,
			);
			$fuel_management_id = $this->input->post('fuel_management_id');
			$this->db->where('fuel_management_id', $fuel_management_id);
			$this->db->where('client_id', $client_id);
			$this->db->update('fuel_management', $fueldata);

			$data['status'] = 1;
			$data['message'] = 'Data Update Successfully';
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function vehicle_total_list_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['vehicle_total_list'] = $this->api_model->vehicle_total_list($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}
	public function vehicle_expireddate_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['vehicle_expireddate'] = $this->api_model->vehicle_expired_list($client_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function total_expensedatewise_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$vehicle_id = $this->input->get('vehicle_id');
			$fromDate = $this->input->get('fromDate');
			$endDate = $this->input->get('endDate');
			if ($vehicle_id == 0) {
				$data['total_expensedatewise'] = $this->api_model->allvehicle_expense($client_id);
			} else {
				$data['total_expensedatewise'] = $this->api_model->vehicleWise_expense($client_id, $vehicle_id, $fromDate, $endDate);
			}

			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function service_overdue_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['service_overdue'] = $this->api_model->vehicle_service_overdue($client_id);

			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function kotakapi_POST()
	{

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://kmblissuer.icashcard.in/KotakAggregator/KotakAggregatorApi/methodname',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				'CPID: 53',
				'PASSWORD: SidKot@12345',
				'ENCRYPTIONKEY: G1234GABCDEH98745B002221'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		echo $response;
	}



	public function history_details_POST()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {
			$data['status'] = 1;
			$vehicle = $this->input->post('imei');
			$from_date_post = $this->input->post('from_date');
			$to_date_post = $this->input->post('to_date');
			$client_id = $result['data']->client_id;
			$userid = $result['data']->userid;
			$role = $result['data']->roleid;
			$reporttype = $this->input->post('reporttype');

			if ($reporttype == 1) {

				$ct = date('Y-m-d');
				$from_date = $ct . ' 00:00:00';
				$to_date = $ct . ' 23:59:59';
			} elseif ($reporttype == 2) {
				$from_date = date('Y-m-d', strtotime('-1 Day'));
				$to_date = date('Y-m-d');
			} elseif ($reporttype == 3) {
				$from_date = date('Y-m-d', strtotime('-7 Day'));
				$to_date = date('Y-m-d');
			} elseif ($reporttype == 4) {
				$from_date = $from_date_post;
				$to_date = $to_date_post;
			}

			if ($reporttype != 1) {

				$data = $this->api_model->history_alldata($vehicle, $from_date, $to_date, $client_id, $userid, $role, $reporttype);

				//--------------------------------Its Contain get Values And Store A Variable--------------------------------------------------
				$total_km = round($data->distance);
				$total_park = $data->parking_duration;
				$total_ac = $data->ac_duration;
				$total_idle = $data->idle_duration;
				$total_rpm = $data->totalrpm;
				$fuelconsum = round($data->fuel_consumed_litre);
				$fuelfill = round($data->fuel_fill_litre);
				$fueldip = round($data->fuel_dip_litre);
				$average_speed = round($data->average_speed);
				$maximum_speed = round($data->maximum_speed);
				$secondary_engine = "1:2:3";
				//---------------------- Above datas stored in a variable-> that variable use to send response ---------------------------------------------

			} else {
				$yesterday_distance = $this->api_model->smart_distanceday_API($vehicle, $from_date, $to_date, $device_type = null, $client_id);
				$yesterday_park = $this->api_model->smart_parkday_API($vehicle, $from_date, $to_date);
				$yesterday_idle = $this->api_model->smart_idleday_API($vehicle, $from_date, $to_date);
				$yesterday_ign = $this->api_model->smart_ignday_API($vehicle, $from_date, $to_date);
				$yesterday_ac = $this->api_model->smart_acday_API($vehicle, $from_date, $to_date);
				$yesterday_fill = $this->api_model->smart_fuelfill_API($vehicle, $from_date, $to_date);
				$yesterday_dip = $this->api_model->smart_fueldip_API($vehicle, $from_date, $to_date);
				$yesterday_consumed = $this->api_model->smart_fuelconsumed_API($vehicle, $from_date, $to_date);
				$yesterday_rpm = $this->api_model->consolidate_allrpmday($vehicle, $from_date, $to_date);
				$yesterday_avg_max = $this->api_model->consolidate_playback_avg_max($vehicle, $from_date, $to_date, $client_id);

				$yesterday_park = $yesterday_park[0];
				$total_km = round($yesterday_distance->distance_km);
				$total_park = $yesterday_park->parking_duration;
				$total_ac = $yesterday_ac->ac_duration;
				$total_idle = $yesterday_idle->idel_duration;
				$total_rpm = $yesterday_rpm->totalrpm;
				$fuelconsum = round($yesterday_consumed->fuel_consumed_litre);
				$fuelfill = round($yesterday_fill->fuel_fill_litre);
				$fueldip = round($yesterday_dip->fuel_dip_litre);
				$average_speed = round($yesterday_avg_max->avg_speed);
				$maximum_speed = round($yesterday_avg_max->max_speed);
				$secondary_engine = "1:2:3";
			}


			//------------------------------its change parking value to H:M:S------------------------------------------------------

			$park_duration = $total_park; //Parking Duration
			$hours = floor($park_duration / 60);
			$min = $park_duration - ($hours * 60);
			$min = floor((($min -   floor($min / 60) * 60)) / 6);
			$second = $park_duration % 60;

			$tot_park = $hours . ":" . $min . ":" . $second;



			//-------------------------------its change A/C value to H:M:S----------------------------------------------------			

			$ac_duration = $total_ac; //AC Duration
			$hours = floor($ac_duration / 60);
			$min = $ac_duration - ($hours * 60);
			$min = floor((($min -   floor($min / 60) * 60)) / 6);
			$second = $ac_duration % 60;

			$tot_ac = $hours . ":" . $min . ":" . $second;

			//--------------------------------Its change idle value to H:M:S--------------------------------------------------------------

			$idle_duration = $total_idle;
			$hours = floor($idle_duration / 60);
			$min = $idle_duration - ($hours * 60);
			$min = floor((($min -   floor($min / 60) * 60)) / 6);
			$second = $idle_duration % 60;

			$tot_idle = $hours . ":" . $min . ":" . $second;

			//--------------------------------Its change RPM value to H:M:S-----------------------------------------------------			

			$totalrpm_duration = $total_rpm; //Total RPM Duration
			$hours = floor($totalrpm_duration / 3600);
			$min = ($totalrpm_duration / 60) % 60;
			$min1 = round($min / 60, 1) * 10;
			$second = $totalrpm_duration % 60;

			$tot_rpm = $hours . ":" . $min . ":" . $second;
			//--------------------------------------------------------------------------------------------------------			
			//print_r($tot_rpm);die;

			$data = array(
				'distance' => $total_km,
				'parking_duration' => $tot_park,
				'ac_duration' => $tot_ac,
				'idle_duration' => $tot_idle,
				'totalrpm' => $tot_rpm,
				'fuel_consumed_litre' => $fuelconsum,
				'fuel_fill_litre' => $fuelfill,
				'fuel_dip_litre' => $fueldip,
				'average_speed' => $average_speed,
				'maximum_speed' => $maximum_speed,
				'secondary_engine' => $secondary_engine
			);

			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function payment_history_POST()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		$total_count = $this->input->post('vehiclename');
		$vehicle_data = $this->input->post();
		if ($result['status'] == 1) {
			$data['status'] = 1;
			for ($i = 0; $i < count($total_count); $i++) {
				$insert_data['vehiclename'] =  $vehicle_data['vehiclename'][$i];
				$insert_data['deviceimei'] = $vehicle_data['imei'][$i];
				$insert_data['installation_date'] = $vehicle_data['installation_date'][$i];
				$insert_data['payment_status'] = $vehicle_data['payment_status'][$i];
				$insert_data['amonut'] = $vehicle_data['amonut'][$i];
				$insert_data['transaction_id'] = $vehicle_data['transaction_id'][$i];
				$insert_data['payment_date'] = date('Y-m-d');
				$insert_data['created_by'] =  $result['data']->userid;
				$insert_data['client_id'] =  $result['data']->client_id;
				$insert_data['userid'] =  $result['data']->userid;
				$insert_data['ip_address'] = $this->input->ip_address();
				$this->db->insert('payment_history', $insert_data);
			}
			$data['message'] = 'Data Inserted Successfully';

			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function contact_details_get()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$client_id = $result['data']->client_id;
			$userid = $result['data']->userid;
			$roleid = $result['data']->roleid;
			$dealer_id = $result['data']->dealer_id;
			$contact_details = $this->api_model->contact_details($client_id, $userid, $roleid, $dealer_id);
			if ($contact_details) {
				$data['status'] = 1;
				$data['contact_details'] = $contact_details;
			} else {
				$data['status'] = 0;
				$data['contact_details'] = array();
			}
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function profile_details_get()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$client_id = $result['data']->client_id;
			$userid = $result['data']->userid;
			$roleid = $result['data']->roleid;
			$profile_details = $this->api_model->profile_details($client_id, $userid, $roleid);
			if ($profile_details) {
				$data['status'] = 1;
				$data['profile_details'] = $profile_details;
			} else {
				$data['status'] = 0;
				$data['profile_details'] = array();
			}
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function update_profile_POST()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$userid = $result['data']->userid;
			$user_data = $this->input->post();
			$address = $this->input->post('address');
			$firstname = $this->input->post('customer_name');

			$user_data['postaladdres'] = $address;
			$user_data['firstname'] = $firstname;
			unset($user_data['address']);
			unset($user_data['customer_name']);
			$this->db->where('userid', $userid);
			$this->db->update('usertbl', $user_data);

			$data['status'] = 1;
			$data['message'] = 'Data Update Successfully';
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function logout_post()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {

			$client_id = $result['data']->client_id;
			$userid = $result['data']->userid;
			$push_code = $this->input->post('push_code');
			$query = $this->db->query("DELETE FROM user_pushcode WHERE client_id=$client_id AND push_code ='" . $push_code . "' ");
			if ($query) {
				$data['status'] = 1;
				$data['message'] = " Remove PushCode Successfully";
			} else {
				$data['status'] = 0;
				$data['message'] = "Error";
			}
			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function checkotp_driver_POST()
	{



		$mobile_number = $this->input->post('mobile_number');
		// $push_code = $this->input->post('push_code');

		$result = $this->api_model->checkotp_driver($mobile_number);

		if ($result) {

			$otp_code = 2346;
			//  $otp_code = substr(str_shuffle("0123456789"), 0, 4);
			$details = array(
				'otp' => $otp_code,
				// 'driver_name' => "surya",
				'updated_time' => date('Y-m-d H:i:s')
			);

			// $aoikey="vV9Yt3Canxhnh2ou";
			// $senderid="TWSGPS";
			// $messages="This Is OTP $otp_code";

			// $url = 'http://sms.sproutwings.in/vb/apikey.php?' . http_build_query([
			// 	'apikey' => $aoikey,
			// 	'senderid' => $senderid,
			// 	'number' => $mobile_number,
			// 	'message' => $messages
			// ]);
			// $ch = curl_init($url);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// $response=curl_exec($ch);

			$this->db->where('driver_mobile', $mobile_number);
			$this->db->update('driver_otp', $details);
			// $this->db->insert('driver_otp',$details);

			$response = array(
				'Status' => 1,
				"message" => "OTP Send Successfully"
			);
			// print_r($response);die;
			$this->response($response);
		} else {

			$otp_code = 4321;
			//  $otp_code = substr(str_shuffle("0123456789"), 0, 4);
			$details = array(
				'otp' => $otp_code,
				//   'driver_name' => "surya",
				'driver_mobile' => $mobile_number,
				'created_time' => date('Y-m-d H:i:s')
			);

			$this->db->insert('driver_otp', $details);

			$data = array(
				'Status' => 2,
				"message" => "OTP Send Successfully"
			);
			$this->response($data);
		}
	}

	public function verify_otp_POST()
	{
		$push_code = $this->input->post('push_code');
		$mobile_number = $this->input->post('mobile_number');
		$otp = $this->input->post('otp');
		//    $apikey=$this->input->post('apikey');

		$checkuser = $this->api_model->verify_otp($mobile_number, $otp);
		//    print_r($checkuser);die;
		if ($checkuser) {
			//    $token_data['driver_id'] = $checkuser->driver_id;
			$token_data['driver_mobile'] = $checkuser->driver_mobile;
			//    $token_data['trip_id'] = $checkuser->trip_id;
			$token_data['vehicleid'] = $checkuser->vehicleid;
			$token_data['client_id'] = $checkuser->client_id;
			//    $token_data['userid'] = $checkuser->driver_id;
			$token_data['dealer_id'] = $checkuser->dealer_id;

			$tokenData = $this->authorization_token->generateToken($token_data);



			$push_data = array(
				'client_id' => $checkuser->client_id,
				'push_code' => $push_code
			);

			$user_count = $this->db->query("SELECT count(*) as user_count FROM user_pushcode WHERE client_id=$checkuser->client_id");
			$count = $user_count->num_rows();
			if ($count > 5) {
				$this->db->query("DELETE FROM user_pushcode ORDER BY push_id ASC LIMIT 1");
				$sql = $this->db->insert_string('user_pushcode', $push_data) . ' ON DUPLICATE KEY UPDATE client_id=LAST_INSERT_ID(client_id)';
				$this->db->query($sql);
			} else {
				$sql = $this->db->insert_string('user_pushcode', $push_data) . ' ON DUPLICATE KEY UPDATE client_id=LAST_INSERT_ID(client_id)';
				$this->db->query($sql);
			}

			$array = array();
			$array['status'] = 1;
			$array['refresh_token'] = 'SWT ' . $tokenData;
			$array['data'] = $checkuser;
		} else {
			$array  = array(
				'status' => 0,
				'message' => 'Username and Password Not Excist'
			);
		}
		$this->response($array);
	}

	public function drivertrip_list_POST()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {
			//print_r($result);die;
			$mobile_number = $result['data']->driver_mobile;
			// print_r($mobile_number);die;


			$zigmatrip_plan = $this->api_model->zigma_addtriplist($status, $mobile_number);
			$zigmatrip_plan_start = $this->api_model->zigma_addtriplist_stat($x = 1, $mobile_number);
			$zigmatrip_plan_processing = $this->api_model->zigma_addtriplist_stat($x = 2, $mobile_number);
			$zigmatrip_plan_completed = $this->api_model->zigma_addtriplist($x = 3, $mobile_number);

			// $last_id = $this->api_model->last_zigmatrip();
			// $data['last_id'] = ($last_id->trip_id)+1;

			$data['zigmatrip_plan'] = $zigmatrip_plan;
			$data['zigmatrip_plan_start'] = $zigmatrip_plan_start;
			$data['zigmatrip_plan_processing'] = $zigmatrip_plan_processing;
			$data['zigmatrip_plan_completed'] = $zigmatrip_plan_completed;

			$this->response($data);
		} else {
			$this->response($result);
		}
	}
	public function drivertrip_expenses_POST()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {

			$exp_id = $this->input->post('exp_id');
			$exp_name = $this->input->post('exp_name');

			$expenses = $_FILES["exp_img"]["name"];
			$ext = pathinfo($expenses, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp_name = $_FILES["exp_img"]['tmp_name'];
			if ($expenses != "") {
				$expenses_file = do_upload_1($ext, $filename, $tmp_name);
			} else {
				$expenses_file = $this->input->post('hiddenfuel_ex_file');
			}
			$signature = $_FILES["signature"]["name"];
			$ext = pathinfo($signature, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp_name = $_FILES["signature"]['tmp_name'];
			if ($signature != "") {
				$signature_file = do_upload_1($ext, $filename, $tmp_name);
			} else {
				$signature_file = $this->input->post('hiddenfuel_ex_file');
			}



			$driver_documents = array(
				'trip_id' => $this->input->post('trip_id'),
				'driver_mobile' => $result['data']->driver_mobile,
				'client_id' => $result['data']->client_id,
				'created_on' => date('Y-m-d H:i:s'),
				'exp_id' => $exp_id,
				'exp_name' => $exp_name,
				'exp_img' => $expenses_file,
				'signature' => $signature_file,
				'remarks' => $this->input->post('remarks')
			);

			$result = $this->db->insert('driver_expenses', $driver_documents);

			if ($result) {
				$data['status'] = 1;
				$data['message'] = "File Inserted Successfully";
			} else {
				$data['status'] = 0;
				$data['message'] = "File Not Insert";
			}

			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function edit_drivertrip_expenses_GET()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		$id = $this->input->get('id');

		if ($result['status'] == 1) {
			$data['status'] = 1;
			$client_id = $result['data']->client_id;
			$data['expense_edit'] = $this->api_model->edit_drivertrip_expenses($id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function update_drivertrip_expenses_POST()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {

			$id = $this->input->post('id');
			$exp_id = $this->input->post('exp_id');
			$exp_name = $this->input->post('exp_name');
			$remarks = $this->input->post('remarks');

			$expenses = $_FILES["exp_img"]["name"];
			$ext = pathinfo($expenses, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp_name = $_FILES["exp_img"]['tmp_name'];
			if ($expenses != "") {
				$expenses_file = do_upload_1($ext, $filename, $tmp_name);
			} else {
				$expenses_file = $this->input->post('hidden_exp_img');
			}

			$signature = $_FILES["signature"]["name"];
			$ext = pathinfo($signature, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp_name = $_FILES["signature"]['tmp_name'];
			if ($signature != "") {
				$signature_file = do_upload_1($ext, $filename, $tmp_name);
			} else {
				$signature_file = $this->input->post('hidden_signature');
			}

			$driver_documents = array(
				'updated_on' => date('Y-m-d H:i:s'),
				'exp_id' => $exp_id,
				'exp_name' => $exp_name,
				'exp_img' => $expenses_file,
				'signature' => $signature_file,
				'remarks' => $remarks
			);

			$this->db->where('id', $id);
			$result = $this->db->update('driver_expenses', $driver_documents);

			if ($result) {
				$data['status'] = 1;
				$data['message'] = "File Updated Successfully";
			} else {
				$data['status'] = 0;
				$data['message'] = "File Not Update";
			}

			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function driver_details_POST()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {

			$mobile_number = $result['data']->driver_mobile;



			$rc_file = $_FILES["rc_file"]["name"];
			$rc_file_name = $_FILES["rc_file"]['tmp_name'];
			if ($rc_file != "") {
				$rc_file_up = do_upload_2($rc_file, $rc_file_name);
			} else {
				$rc_file_up = $this->input->post('hiddenfuel_ex_file');
			}

			$insurance_file = $_FILES["insurance_file"]["name"];
			$insurance_file_name = $_FILES["insurance_file"]['tmp_name'];
			if ($insurance_file != "") {
				$insurance_file_up = do_upload_2($insurance_file, $insurance_file_name);
			} else {
				$insurance_file_up = $this->input->post('hiddenfood_ex_file');
			}

			$license_file = $_FILES["license_file"]["name"];
			$license_file_name = $_FILES["license_file"]['tmp_name'];
			if ($license_file != "") {
				$license_file_up = do_upload_2($license_file, $license_file_name);
			} else {
				$license_file_up = $this->input->post('hiddenrto_ex_file');
			}

			$other_file = $_FILES["other_file"]["name"];
			$other_file_name = $_FILES["other_file"]['tmp_name'];
			if ($other_file != "") {
				$other_file_up = do_upload_2($other_file, $other_file_name);
			} else {
				$other_file_up = $this->input->post('hiddenrto_ex_file');
			}

			$drivername = $this->input->post('driver_name');

			$driver_files = array(
				'rc_file' => $rc_file_up,
				'insurance_file' => $insurance_file_up,
				'driver_license' => $license_file_up,
				'other_file' => $other_file_up,
				// 'driver_name' => $drivername,
			);

			$this->db->where('driver_mobile', $mobile_number);
			$result = $this->db->update('driver_otp', $driver_files);

			if ($result) {
				$data['status'] = 1;
				$data['message'] = "File Upload Successfully";
			} else {
				$data['status'] = 0;
				$data['message'] = "File Not Uploaded";
			}

			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function delete_expenses_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);

		$id = $this->input->GET('id');
		if ($result['status'] == 1) {

			$query = $this->db->query("DELETE FROM driver_expenses WHERE id='$id'");
			if ($query) {
				$data['status'] = 1;
				$data['message'] = "Record Delete Successfully";
			} else {
				$data['status'] = 0;
				$data['message'] = "Not Deleted Record";
			}


			$this->response($data);
		} else {
			$this->response($result);
		}
	}


	public function drivertrip_list_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			// $client_id = $result['data']->client_id;
			$trip_id = $this->input->GET('trip_id');
			$data['drivertrip_list'] = $this->api_model->drivertrip_list($trip_id);
			// print_r($data['drivertrip_list']);die;
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function get_driver_location_POST()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$data['Data'] = $this->input->post();
			$this->response($data);
		}else {
			$this->response($result);
		}
	}
	public function getAddress_get()
	{
		$Lattitute = $this->input->get('Lattitute');
		$Longitute = $this->input->get('Longitute');

		$format = 'json'; // set the desired format of the response

		$url = "http://69.197.153.82/nominatim/reverse.php?lat=$Lattitute&lon=$Longitute&format=$format";
		//$url = 'http://69.197.153.82/nominatim/reverse.php?lat='.trim($Lattitute).'&lon='.trim($Longitute).'&accept-language=en';
		//                 $curl = curl_init();

		// curl_setopt_array($curl, array(
		//   CURLOPT_URL => $url,
		//   CURLOPT_RETURNTRANSFER => true,
		//   CURLOPT_ENCODING => '',
		//   CURLOPT_MAXREDIRS => 10,
		//   CURLOPT_TIMEOUT => 0,
		//   CURLOPT_FOLLOWLOCATION => true,
		//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		//   CURLOPT_CUSTOMREQUEST => 'GET',
		// ));

		// $response = curl_exec($curl);

		// curl_close($curl);
		// //echo $response;
		// $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
		// $json = json_encode($xml);
		// $array = json_decode($json,TRUE);
		// 		$array  = array('status' => 'ok', 'address' =>$array['result']);
		// 		$this->response($array);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
		$response = curl_exec($curl);
		curl_close($curl);

		$data = json_decode($response, true); // decode the JSON response into a PHP associative array

		$address = $data['address'];
		$details = $address['road'] . ", " . $address['city'] . ", " . $address['state'] . ", " . $address['country'];
		//$array  = array('status' => 'ok', 'address' =>$details);
		$array = array('status' => 'ok', 'address' => $data["display_name"]);
		$this->response($array);

		// 		$url = 'http://69.197.153.82/nominatim/reverse.php?lat='.trim($Lattitute).'&lon='.trim($Longitute).'&accept-language=en';
		//                 $curl = curl_init();

		// curl_setopt_array($curl, array(
		//   CURLOPT_URL => $url,
		//   CURLOPT_RETURNTRANSFER => true,
		//   CURLOPT_ENCODING => '',
		//   CURLOPT_MAXREDIRS => 10,
		//   CURLOPT_TIMEOUT => 0,
		//   CURLOPT_FOLLOWLOCATION => true,
		//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		//   CURLOPT_CUSTOMREQUEST => 'GET',
		// ));

		// $response = curl_exec($curl);

		// curl_close($curl);
		// //echo $response;
		// $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
		// $json = json_encode($xml);
		// $array = json_decode($json,TRUE);
		// 		$array  = array('status' => 'ok', 'address' =>$array['result']);
		// 		$this->response($array);
	}
	public function demo_function_get()
	{
		echo "Test";
	}
	public function demo_address_get($lat, $lang)
	{
		$format = 'json'; // set the desired format of the response

		$url = "http://69.197.153.82/nominatim/reverse.php?lat=$Lattitute&lon=$Longitute&format=$format";
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
		$response = curl_exec($curl);
		curl_close($curl);

		$data = json_decode($response, true); // decode the JSON response into a PHP associative array

		$address = $data['address'];
		$details = $address['road'] . ", " . $address['city'] . ", " . $address['state'] . ", " . $address['country'];
		$array = array('status' => 'ok', 'address' => $data["display_name"]);
		print_r($data['display_name']);
		//$this->response($array);
	}
	public function generator_details_get()
	{
		$headers = $this->input->request_headers();
		$deviceimei = $this->input->get('imei');
		$valid_status = 1;
		if ($deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'deviceimei is Empty';
			$this->response($data, REST_Controller::HTTP_OK);
		}

		if ($valid_status) {

			$result = $this->authorization_token->validateToken($headers['Authorization']);

			if ($result['status'] == 1) {
				$ct = date('Y-m-d');
				$start_date = $ct . ' 00:00:00';
				$end_date = $ct . ' 23:59:59';

				$data['status'] = 1;
				$client_id = $result['data']->client_id;
				$data['vehicle_details'] = $this->api_model->generator_details($deviceimei);
				$device_type = $data['vehicle_details']->device_type;

				$last_ignition_on_off[0] = $this->api_model->generator_last_ign($deviceimei);
				// print_r($last_ignition_on_off);die;
				$ct_client_id = $data['vehicle_details']->client_id;
				$today_km_data = $this->api_model->calculate_distance($deviceimei, $start_date, $end_date, $client_id);
				$percentage = $this->api_model->percentage_detail($client_id, $deviceimei);

				$batteryvolt = ($percentage) ? 'No data' : $data['vehicle_details']->batteryvolt;

				$percentage = ($percentage) ? $percentage : 'No data';
				$today_km = ($today_km_data) ? $today_km_data->distance_km : 0;

				$today_km = ($today_km_data) ? $today_km_data->distance_km : 0;

				$data['vehicle_details'] = array(array(
					"client_id" => $data['vehicle_details']->client_id,
					"vehicle_id" => $data['vehicle_details']->vehicle_id,
					"alarm_set" => $data['vehicle_details']->alarm_set,
					"updatedon" => $data['vehicle_details']->updatedon,
					"update_time" => $data['vehicle_details']->update_time,
					"odometer" => $data['vehicle_details']->odometer,
					"speed" => $data['vehicle_details']->speed,
					"acc_on" => $data['vehicle_details']->acc_on,
					"ac_flag" => $data['vehicle_details']->ac_flag,
					"lat" => $data['vehicle_details']->lat,
					"lng" => $data['vehicle_details']->lng,
					"angle" => $data['vehicle_details']->angle,
					"latlon_address" => $data['vehicle_details']->latlon_address,
					"today_km" => $today_km,
					"trip_kilometer" => $data['vehicle_details']->today_km,
					"batteryvolt" => $batteryvolt,
					"fuel_ltr" => $data['vehicle_details']->fuel_ltr,
					"driver_name" => 'N/A',
					"ign_on" => $last_ignition_on_off[0]->start_day,
					"ign_off" => $last_ignition_on_off[0]->end_day,
					"ign_on_duration" => $last_ignition_on_off[0]->time_duration,
					"mileage" => $data['vehicle_details']->mileage,
					"distancetoEmpty" => 'N/A',
					"secondary_engine" => 'N/A',
					"battery_precentage" => $percentage,
					"hourmeter" => 'h:min',
					"rpm" => round($data['vehicle_details']->rpm_data),
					//"rpm"=>$data['vehicle_details']->rpm_data,
					"temperature" => $data['vehicle_details']->temperature,
					"safe_parking" => $data['vehicle_details']->safe_parking,
					"humidity" => 'N/A',
					"drum" => 'N/A',
					"bucket" => 'N/A',
					"gps" => $data['vehicle_details']->gps,
					"gsm" => $data['vehicle_details']->gsm,
					"altitude" => $data['vehicle_details']->altitude,
					"sattlite" => 'N/A'

				));

				$res = $data['vehicle_details'][0];

				if ($client_id == $ct_client_id) {

					$this->response($res, REST_Controller::HTTP_OK);
				} else {

					$data1['status'] = 0;
					$data1['message'] = 'Please send Current User Token';
					$this->response($data1, REST_Controller::HTTP_OK);
				}
			} else {
				$this->response($result);
			}
		}
	}

	public function generator_last_ign_fuel_GET()
	{
		$headers = $this->input->request_headers();

		$result = $this->authorization_token->validateToken($headers['Authorization']);

		if ($result['status'] == 1) {

			$deviceimei =  $this->input->get('imei');

			$last_ignition_on_off = $this->api_model->generator_last_ign($deviceimei);

			$from_date = $last_ignition_on_off->start_day;
			$to_date = $last_ignition_on_off->end_day;

			$fuel_data = $this->genericreport_model->fuel_data_distance($from_date, $to_date, $deviceimei);

			$query_filldip = $this->db->query("SELECT * FROM (SELECT SUM(difference_fuel) as filldiff FROM fuel_fill_dip_report WHERE running_no = '" . $deviceimei . "'  AND difference_fuel>0 AND (created_on >= '" . $from_date . "' AND created_on <= '" . $to_date . "') AND type_id ='2') A,(SELECT SUM(difference_fuel) as dipdiff FROM fuel_fill_dip_report WHERE running_no = '" . $deviceimei . "' AND difference_fuel<0 AND (created_on >= '" . $from_date . "' AND created_on <= '" . $to_date . "') AND type_id ='1') B");
			//  echo "SELECT * FROM (SELECT SUM(difference_fuel) as filldiff FROM fuel_fill_dip_report WHERE running_no = '".$vehicle."'  AND difference_fuel>0 AND (created_on >= '".$from_date."' AND created_on <= '".$to_date."') AND type_id ='2') A,(SELECT SUM(difference_fuel) as dipdiff FROM fuel_fill_dip_report WHERE running_no = '".$vehicle."' AND difference_fuel<0 AND (created_on >= '".$from_date."' AND created_on <= '".$to_date."') AND type_id ='1') B";exit;
			$filldip = $query_filldip->row();

			$fill_ltr = $filldip->filldiff;

			$dip_ltr = $filldip->dipdiff;

			$fuel_ltr = $fill_ltr + $dip_ltr;


			$query1 = $this->db->query("SELECT deviceimei as v_running_no,vehiclename,device_config_type from vehicletbl where deviceimei = '" . $deviceimei . "'");
			$vehicle_number = $query1->row();
			$vehicle_register_number = $vehicle_number->vehiclename;

			$data['last_consumed_fuel'] = null;
			$data['distance'] = null;
			$data['last_start_date'] = null;
			$data['last_end_date'] = null;

			if ($fuel_data) {

				$call_distance = null;
				$set_dis = 0;

				$call_fuel = null;
				$set_fuel = 0;
				$i = 0;

				$f_length = count($fuel_data) - 1;

				$n = count($fuel_data);

				$end_meter = $fuel_data[0]->odometer;
				$start_meter = $fuel_data[$n - 1]->odometer;

				$end_fuel = $fuel_data[0]->litres;
				$start_fuel = $fuel_data[$n - 1]->litres;
				$distance = $end_meter - $start_meter;


				if ($fuel_ltr < 0) {
					$fl = 0;
				} else {
					$fl = $fuel_ltr;
				}

				//echo $start_fuel." +". $fl."-".$end_fuel; exit();
				$cf = $start_fuel + $fl - $end_fuel;

				if ($cf < 0) {
					$cunsumed_fl = -1 * $cf;
				} else {
					$cunsumed_fl = $cf;
				}

				if ($cunsumed_fl != 0) {
					$mileage = $distance / $cunsumed_fl;
				} else {
					$mileage = 0;
				}

				$data['last_start_date'] = $from_date;
				$data['last_end_date'] = $to_date;
				$data['fill_fuel'] = round($fill_ltr, 2);
				$data['last_consumed_fuel'] = $cunsumed_fl;
				$data['start_odo'] = $start_meter;
				$data['end_odo'] = $end_meter;
				$data['distance'] = round($distance, 2);
				$data['mileage'] = round($mileage, 2);
				$data['vehicle'] = $vehicle_register_number;


				$this->response($data);
			} else {
				$data['last_start_date'] = $from_date;
				$data['last_end_date'] = $to_date;
				$data['fill_fuel'] = '';
				$data['last_consumed_fuel'] = '';
				$data['start_odo'] = '';
				$data['end_odo'] = '';
				$data['distance'] = '';
				$data['mileage'] = '';
				$data['vehicle'] = '';


				$this->response($data);
			}
		} else {
			$this->response($result);
		}
	}

	public function qatar_all_vehicles_GET()
	{


		$vehicles = $this->api_model->qatar_all_vehicles();
		$this->response($vehicles);
	}

	public function qatar_immobilizer_POST()
	{
		// $password = $this->input->post('password');
		// $newp = md5($password);
		$deviceimei = $this->input->post('deviceimei');
		$digit_output = $this->input->post('digit_output');
		$status = ($digit_output == 1) ? 1 : 0;
		$address = $this->input->post('address');

		$valid_status = 1;
		if ($deviceimei == '') {
			$valid_status = 0;
			$data['status'] = 0;
			$data['message'] = 'Require Fields Are empty';
			$this->response($data);
		}
		if ($valid_status) {

			$data['status'] = 1;
			$client_id = 484;
			// $user_id = $result['data']->userid;
			// $dealer_id = $result['data']->dealer_id;
			// $subdealer_id = $result['data']->subdealer_id;
			// $data['user_details'] = $this->api_model->user_details($user_id, $newp);

			$data1 = array(
				'client_id' => $client_id,
				'vehicle_id' => $deviceimei,
				'address' => $address,
				'status' => $status
			);


			$this->db->insert('immoblizer_data', $data1);

			$data2['status'] = 1;
			$data2['message'] = 'Insert Successfully';
			$this->response($data2);
		} else {
			$data1['status'] = 0;
			$data1['message'] = 'Password is mismatch';
			$this->response($data1);
		}
	}


	public function about_us_GET()
	{

		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$data['status'] = 1;
			$dealer_id = $result['data']->dealer_id;
			$role_id = $result['data']->roleid;
			if ($role_id == '1') {
				$dealer_id = 0;
			}
			$data['about_us'] = $this->api_model->about_us_data($dealer_id);
			$this->response($data);
		} else {
			$this->response($result);
		}
	}

	public function qatar_login_POST()
	{
		$username = $this->input->post('username');
		$password = md5($this->input->post('password'));

		$checkuser = $this->api_model->checkuser($username, $password);

		if ($checkuser) {
			$data1['status'] = 1;
			$data1['data'] = $checkuser;
			$this->response($data1);
		} else {
			$data1['status'] = 0;
			$data1['message'] = 'Username & Password is mismatch';
			$this->response($data1);
		}
	}

	public function qatar_clients_GET()
	{
		
        $clients = $this->api_model->qatar_all_clients();

		$this->response($clients);

	}

	public function trip_images_add_POST()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
			$file1_name = $_FILES["image_1"]["name"];
			$ext = pathinfo($file1_name, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp1_name = $_FILES["image_1"]['tmp_name'];
			if ($file1_name != "") {
				$image1_file = do_upload_4($ext, $file1_name, $tmp1_name);
			} else {
				$image1_file = $this->input->post('hidden_image_1');
			}

			$file2_name = $_FILES["image_2"]["name"];
			$ext = pathinfo($file2_name, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp2_name = $_FILES["image_2"]['tmp_name'];
			if ($file2_name != "") {
				$image2_file = do_upload_4($ext, $file2_name, $tmp2_name);
			} else {
				$image2_file = $this->input->post('hidden_image_2');
			}

			$file3_name = $_FILES["image_3"]["name"];
			$ext = pathinfo($file3_name, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp3_name = $_FILES["image_3"]['tmp_name'];
			if ($file3_name != "") {
				$image3_file = do_upload_4($ext, $file3_name, $tmp3_name);
			} else {
				$image3_file = $this->input->post('hidden_image_3');
			}

			$file4_name = $_FILES["image_4"]["name"];
			$ext = pathinfo($file4_name, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp4_name = $_FILES["image_4"]['tmp_name'];
			if ($file4_name != "") {
				$image4_file = do_upload_4($ext, $file4_name, $tmp4_name);
			} else {
				$image4_file = $this->input->post('hidden_image_4');
			}

			$file5_name = $_FILES["image_5"]["name"];
			$ext = pathinfo($file5_name, PATHINFO_EXTENSION);
			$filename = 'exp' . time();
			$tmp5_name = $_FILES["image_5"]['tmp_name'];
			if ($file5_name != "") {
				$image5_file = do_upload_4($ext, $file5_name, $tmp5_name);
			} else {
				$image5_file = $this->input->post('hidden_image_5');
			}

			$id = $this->input->post('id');

			if ($id) {
				$trip_imagedata = array(
				    'trip_id' => $this->input->post('trip_id'),
					'image_1' => $image1_file,
					'image_2' => $image2_file,
					'image_3' => $image3_file,
					'image_4' => $image4_file,
					'image_5' => $image5_file,
					'updatedon' => date('Y-m-d H:i:s')
				);

                $this->db->WHERE('id',$id);
				$update = $this->db->UPDATE('trip_planimages',$trip_imagedata);

				if ($update) {
					$data['status'] = 1;
					$data['message'] = "Update Successfully";
				}				
				$this->response($data);

			}else{
				$trip_imagedata = array(
					'trip_id' => $this->input->post('trip_id'),
					'image_1' => $image1_file,
					'image_2' => $image2_file,
					'image_3' => $image3_file,
					'image_4' => $image4_file,
					'image_5' => $image5_file,
					'client_id' => $result['data']->client_id,
					'vehicleid' => $this->input->post('vehicleid'),
					'createdon' => date('Y-m-d H:i:s')
				);
				$lastid = $this->db->insert('trip_planimages', $trip_imagedata);
				
				if ($lastid) {
					$data['status'] = 1;
					$data['message'] = "Insert Successfully";
				}
				$this->response($data);
			}
		} else {
			$this->response($result);
		}
	}

	public function trip_images_edit_GET()
	{
		$headers = $this->input->request_headers();
		$result = $this->authorization_token->validateToken($headers['Authorization']);
		if ($result['status'] == 1) {
        $data['status'] = 1;
        $trip_id = $this->input->get('trip_id');

		$data['data'] = $this->db->query("SELECT id,trip_id,image_1,image_2,image_3,image_4,image_5 FROM trip_planimages WHERE trip_id=$trip_id")->row();
       
		$this->response($data);
		} else {
			$this->response($result);
		}
	}

}
