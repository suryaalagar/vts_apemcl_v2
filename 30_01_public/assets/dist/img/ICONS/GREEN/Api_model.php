<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Api_model extends CI_model
{
    function __construct()
    {
        parent::__construct();
    }

    public function checkuser($username, $password)
    {

        $query = $this->db->query("SELECT userid,firstname,username,companyname,mobilenumber,email,roleid,client_id,dealer_id,subdealer_id,timezone_minutes FROM usertbl WHERE status=1 AND username='$username' AND (password='$password' OR secondarypassword='$password')");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function verify_apikey($apikey, $dealer_id)
    {
        if(!empty($dealer_id))
        $query = $this->db->query("SELECT apikey FROM apikey WHERE status=1 AND apikey='$apikey' AND dealer_id=$dealer_id");
        else
        $query = $this->db->query("SELECT apikey FROM apikey WHERE status=1 AND apikey='$apikey' AND dealer_id IS NULL");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    public function total_count($userid, $client_id, $roleid)
    {
        if ($roleid == "6") {
            $query = $this->db->query("SELECT (SELECT count(v.vehicleid) FROM vehicletbl v  INNER JOIN assign_owner ao ON ao.vehicle_id=v.vehicleid WHERE 
			v.status=1 AND TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =0 AND ao.owner_id =$userid) as park_count,
			(SELECT count(v.vehicleid) FROM vehicletbl v INNER JOIN assign_owner ao ON ao.vehicle_id=v.vehicleid WHERE v.status=1 AND 
			TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) = 0 and ao.owner_id =$userid) as idle_count,
			(SELECT count(v.vehicleid) FROM vehicletbl v INNER JOIN assign_owner ao ON ao.vehicle_id=v.vehicleid WHERE v.status=1 AND 
			TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) >= 1 and ao.owner_id =$userid) as move_count,
			(SELECT count(v.vehicleid) FROM vehicletbl v INNER JOIN assign_owner ao ON ao.vehicle_id=v.vehicleid WHERE (TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) > '10'
			 or v.updatedon IS NULL or v.acc_on IS NULL) and v.status=1 AND ao.owner_id =$userid) as outofcoverage_count");
        } elseif ($roleid == '5') {
            $query = $this->db->query("SELECT (SELECT count(v.vehicleid) FROM vehicletbl v  WHERE 
			v.status=1 AND TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =0 AND v.client_id=$client_id) as park_count,
			(SELECT count(v.vehicleid) FROM vehicletbl v WHERE v.status=1 AND 
			TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) = 0 and v.client_id=$client_id) as idle_count,
			(SELECT count(v.vehicleid) FROM vehicletbl v  WHERE v.status=1 AND 
			TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) >= 1 and v.client_id=$client_id) as move_count,
			(SELECT count(v.vehicleid) FROM vehicletbl v  WHERE (TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) > '10'
			 or v.updatedon IS NULL or v.acc_on IS NULL) and v.status=1 AND v.client_id=$client_id) as outofcoverage_count");
        }

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }


    public function vehicledetails($deviceimei,$timezone=0,$local_timezone=0)
    {

        $query = $this->db->query("SELECT client_id,vehicleid as vehicle_id,(updatedon + INTERVAL $timezone MINUTE) as updatedon,TIMESTAMPDIFF(MINUTE,updatedon,$local_timezone) AS update_time,
			ROUND(odometer,3) as odometer,round(speed) as speed,acc_on,ac_flag,lat,lng,angle,COALESCE(latlon_address,'') as latlon_address,
			today_km,COALESCE(ROUND(today_km,3),'0') as trip_kilometer,ROUND(car_battery,2) as batteryvolt,ROUND(litres,2) as fuel_ltr,
            COALESCE(driver_name,'N/A') as driver_name,last_ign_off,last_ign_on,mileage,IF(altitude >2, altitude, 'N/A') as altitude,
            IF(gsmsignal >0, gsmsignal, 'N/A') as gsm, IF(gpssignal >0, gpssignal, 'N/A') as gsm,
            COALESCE(ROUND((litres*keyword),1),'N/A') as distancetoEmpty,
            'N/A'  AS  secondary_engine,'75%'  AS  battery_precentage,'h:min' AS hourmeter,rpm_data,
			 round(temperature/100,2) as temperature,'NULL' AS humidity,'N/A' AS drum,'N/A' AS bucket,device_type,safe_parking
			 FROM vehicletbl where deviceimei=$deviceimei");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function demo_vehicledetails($deviceimei,$timezone=0,$local_timezone)
    {

        $query = $this->db->query("SELECT client_id,vehicleid as vehicle_id,(updatedon + INTERVAL $timezone MINUTE) as updatedon,TIMESTAMPDIFF(MINUTE,updatedon,$local_timezone) AS update_time,
			ROUND(odometer,3) as odometer,round(speed) as speed,acc_on,ac_flag,lat,lng,angle,COALESCE(latlon_address,'') as latlon_address,
			today_km,COALESCE(ROUND(today_km,3),'0') as trip_kilometer,ROUND(car_battery,2) as batteryvolt,ROUND(litres,2) as fuel_ltr,
            COALESCE(driver_name,'N/A') as driver_name,last_ign_off,last_ign_on,mileage,IF(altitude >2, altitude, 'N/A') as altitude,
            IF(gsmsignal >0, gsmsignal, 'N/A') as gsm, IF(gpssignal >0, gpssignal, 'N/A') as gsm,
            COALESCE(ROUND((litres*keyword),1),'N/A') as distancetoEmpty,
            'N/A'  AS  secondary_engine,'75%'  AS  battery_precentage,'h:min' AS hourmeter,rpm_data,
			 round(temperature/100,2) as temperature,'NULL' AS humidity,'N/A' AS drum,'N/A' AS bucket,device_type,safe_parking
			 FROM vehicletbl where deviceimei=$deviceimei");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    public function imeidetails($deviceimei)
    {
        $query = $this->db->query("SELECT vehicleid,client_id,deviceimei,speed,acc_on,updatedon,vehiclename FROM vehicletbl WHERE deviceimei=$deviceimei");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }


    public function vehiclelist($client_id, $status,$roleid,$timezone,$local_timezone)
    {
        if ($status == 1) {

            if($roleid == 6)  {
                $query = $this->db->query("SELECT vehicleid,IF(
                    TIMESTAMPDIFF(MINUTE,v.updatedon,$local_timezone) <= '10' AND v.acc_on =1 and round(v.speed) >= 1,
                    'Moving',
                    IF(
                     TIMESTAMPDIFF(MINUTE,v.updatedon,$local_timezone) <= '10' AND v.acc_on =1 and round(v.speed) = 0 ,
                    'Idle',
                    IF(
                    TIMESTAMPDIFF(MINUTE,v.updatedon,$local_timezone) <= '10' AND v.acc_on =0,         
                        'Parking',          
                        'OutOfCoverage'
                      )
                      )
                    ) as vehicle_status 
                        ,vehiclename,TIME_FORMAT(TIMEDIFF($local_timezone,v.last_ign_off), '%H Hours:%i Minutes') as last_update,
                          COALESCE(v.driver_name,'N/A') as driver_name, ROUND(v.speed) as speed,ROUND(v.odometer,3) as odometer,
                          COALESCE(v.latlon_address,'') as address,ROUND(v.litres,2) as fuel,
                          ROUND(v.car_battery,2) as battery_voltage, v.acc_on as acc_status, v.ac_flag as ac_status,
                          IF(v.car_battery<7, 0, 1) as battery_status,v.digital_output as immoblizer_status,v.digital_output as digital_output,v.gpssignal as gps_status,v.gsmsignal as gsmlevel,v.lat,v.lng,v.deviceimei as vehicle_imei, 
                          TIMESTAMPDIFF(MINUTE,v.updatedon,$local_timezone) AS update_time,v.angle,v.vehicletype,(v.updatedon + INTERVAL $timezone MINUTE) as updatedon,v.safe_parking,(CASE
			                WHEN CURRENT_DATE BETWEEN v.installationdate
                            AND DATE_SUB(v.expiredate, INTERVAL 1 MONTH) THEN '0'
                            WHEN CURRENT_DATE BETWEEN DATE_SUB(v.expiredate, INTERVAL 1 MONTH) AND  v.expiredate
                            THEN '1'
                            WHEN v.extenddate > CURRENT_DATE THEN '2'
                            ELSE '3'
		                END
	            ) AS Due_Status,
                        v.immobilizer_option,v.expiredate,v.installationdate
                FROM vehicletbl v INNER JOIN assign_owner ao ON ao.vehicle_id = v.vehicleid WHERE v.status=1 AND v.client_id=$client_id");
            }
            else
            {
                $query = $this->db->query("SELECT vehicleid,IF(
                    TIMESTAMPDIFF(MINUTE,v.updatedon,$local_timezone) <= '10' AND v.acc_on =1 and round(v.speed) >= 1,
                    'Moving',
                    IF(
                     TIMESTAMPDIFF(MINUTE,v.updatedon,$local_timezone) <= '10' AND v.acc_on =1 and round(v.speed) = 0 ,
                    'Idle',
                    IF(
                    TIMESTAMPDIFF(MINUTE,v.updatedon,$local_timezone) <= '10' AND v.acc_on =0,         
                        'Parking',          
                        'OutOfCoverage'
                      )
                      )
                    ) as vehicle_status 
              ,vehiclename,TIME_FORMAT(TIMEDIFF($local_timezone,last_ign_off), '%H Hours:%i Minutes') as last_update,
                          COALESCE(driver_name,'N/A') as driver_name, ROUND(speed) as speed,ROUND(odometer,3) as odometer,
                          COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,
                          ROUND(car_battery,2) as battery_voltage, acc_on as acc_status, ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,v.digital_output as digital_output,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
                          TIMESTAMPDIFF(MINUTE,updatedon,$local_timezone) AS update_time,angle,vehicletype,safe_parking,
                          (CASE
			WHEN CURRENT_DATE BETWEEN v.installationdate
			AND DATE_SUB(v.expiredate, INTERVAL 1 MONTH) THEN '0'
			WHEN CURRENT_DATE BETWEEN DATE_SUB(v.expiredate, INTERVAL 1 MONTH) AND  v.expiredate
		    THEN '1'
			WHEN v.extenddate > CURRENT_DATE THEN '2'
			ELSE '3'
		END
	) AS Due_Status,
                        v.immobilizer_option,v.expiredate,v.installationdate,(v.updatedon + INTERVAL $timezone MINUTE) as updatedon
                FROM vehicletbl v  WHERE v.status=1 AND v.client_id=$client_id");
            }
        
        } 
        
        elseif ($status == 2) {
            $query = $this->db->query("SELECT 'Moving' as vehicle_status,vehicleid,vehiclename,TIME_FORMAT(TIMEDIFF($local_timezone,last_ign_off), '%H Hours:%i Minutes') as last_update,COALESCE(driver_name,'N/A') as driver_name,
			ROUND(speed) as speed,ROUND(odometer,3) as odometer,COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,ROUND(car_battery,2) as battery_voltage,
			acc_on as acc_status,ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
			TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,updatedon,safe_parking
			FROM vehicletbl v  WHERE v.status=1 AND TIMESTAMPDIFF(MINUTE,v.updatedon,$local_timezone) <= '10' AND v.acc_on =1 and round(v.speed) >= 1 and v.client_id=$client_id");
        } elseif ($status == 3) {
            $query = $this->db->query("SELECT 'Idle' as vehicle_status,vehicleid,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,COALESCE(driver_name,'N/A') as driver_name,
			ROUND(speed) as speed,ROUND(odometer,3) as odometer,COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,ROUND(car_battery,2) as battery_voltage,
			acc_on as acc_status,ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
			TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,updatedon,safe_parking
			FROM vehicletbl v  WHERE v.status=1 AND TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) = 0 
			and	v.client_id=$client_id");
        } elseif ($status == 4) {
            $query = $this->db->query("SELECT 'Parking' as vehicle_status,vehicleid,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,COALESCE(driver_name,'N/A') as driver_name,
			ROUND(speed) as speed,ROUND(odometer,3) as odometer,COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,ROUND(car_battery,2) as battery_voltage,
			acc_on as acc_status,ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
			TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,updatedon,safe_parking
			FROM vehicletbl v  WHERE v.status=1  AND TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =0 
			AND v.client_id=$client_id");
        } elseif ($status == 5) {
            $query = $this->db->query("SELECT 'OutOfCoverage' as vehicle_status,vehicleid,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,
			COALESCE(driver_name,'N/A') as driver_name, ROUND(speed) as speed,ROUND(odometer,3) as odometer,
			COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,ROUND(car_battery,2) as battery_voltage, 
			acc_on as acc_status,ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,v.digital_output as immoblizer_status,v.digital_output as digital_output,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,
			angle,vehicletype,updatedon,safe_parking 
			FROM vehicletbl v WHERE v.status=1 AND (TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) > '10' OR updatedon IS NULL or acc_on IS NULL or speed IS NULL)
			AND v.client_id=$client_id");
        } else {
            $query = $this->db->query("SELECT IF(
				TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) >= 1,
				'Moving',
				IF(
				 TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) = 0 ,
				'Idle',
				IF(
				TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =0,         
					'Parking',          
					'OutOfCoverage'
				  )
				  )
				) as vehicle_status 
		  ,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,
					  COALESCE(driver_name,'N/A') as driver_name, ROUND(speed) as speed,ROUND(odometer,3) as odometer,
					  COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,
					  ROUND(car_battery,2) as battery_voltage, acc_on as acc_status, ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
					  TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,updatedon,safe_parking 
			FROM vehicletbl v  WHERE v.status=1 AND v.client_id=$client_id");
       
    }

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    public function demo_vehiclelist($client_id, $status,$roleid,$timezone,$local_timezone )
    {
        if ($status == 1) {

            if($roleid == 6)  {
                $query = $this->db->query("SELECT vehicleid,IF(
                    TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) >= 1,
                    'Moving',
                    IF(
                     TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) = 0 ,
                    'Idle',
                    IF(
                    TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =0,         
                        'Parking',          
                        'OutOfCoverage'
                      )
                      )
                    ) as vehicle_status 
                        ,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.last_ign_off), '%H Hours:%i Minutes') as last_update,
                          COALESCE(v.driver_name,'N/A') as driver_name, ROUND(v.speed) as speed,ROUND(v.odometer,3) as odometer,
                          COALESCE(v.latlon_address,'') as address,ROUND(v.litres,2) as fuel,
                          ROUND(v.car_battery,2) as battery_voltage, v.acc_on as acc_status, v.ac_flag as ac_status,
                          IF(v.car_battery<7, 0, 1) as battery_status,v.digital_output as immoblizer_status,v.digital_output as digital_output,v.gpssignal as gps_status,v.gsmsignal as gsmlevel,v.lat,v.lng,v.deviceimei as vehicle_imei, 
                          TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,v.angle,v.vehicletype,v.updatedon,v.safe_parking,(CASE
			                WHEN CURRENT_DATE BETWEEN v.installationdate
                            AND DATE_SUB(v.expiredate, INTERVAL 1 MONTH) THEN '0'
                            WHEN CURRENT_DATE BETWEEN DATE_SUB(v.expiredate, INTERVAL 1 MONTH) AND  v.expiredate
                            THEN '1'
                            WHEN v.extenddate > CURRENT_DATE THEN '2'
                            ELSE '3'
		                END
	            ) AS Due_Status,
                        v.immobilizer_option,v.expiredate,v.installationdate
                FROM vehicletbl v INNER JOIN assign_owner ao ON ao.vehicle_id = v.vehicleid WHERE v.status=1 AND v.client_id=$client_id");
            }
            else
            {
                $query = $this->db->query("SELECT vehicleid,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP),IF(
                    TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) >= 1,
                    'Moving',
                    IF(
                     TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) = 0 ,
                    'Idle',
                    IF(
                    TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =0,         
                        'Parking',          
                        'OutOfCoverage'
                      )
                      )
                    ) as vehicle_status 
              ,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,
                          COALESCE(driver_name,'N/A') as driver_name, ROUND(speed) as speed,ROUND(odometer,3) as odometer,
                          COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,
                          ROUND(car_battery,2) as battery_voltage, acc_on as acc_status, ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,v.digital_output as digital_output,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
                          TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,safe_parking,
                          (CASE
			WHEN CURRENT_DATE BETWEEN v.installationdate
			AND DATE_SUB(v.expiredate, INTERVAL 1 MONTH) THEN '0'
			WHEN CURRENT_DATE BETWEEN DATE_SUB(v.expiredate, INTERVAL 1 MONTH) AND  v.expiredate
		    THEN '1'
			WHEN v.extenddate > CURRENT_DATE THEN '2'
			ELSE '3'
		END
	) AS Due_Status,
                        v.immobilizer_option,v.expiredate,v.installationdate,(v.updatedon + INTERVAL $timezone MINUTE) as updatedon
                FROM vehicletbl v  WHERE v.status=1 AND v.client_id=$client_id");
            }
        
        } 
        
        elseif ($status == 2) {
            $query = $this->db->query("SELECT 'Moving' as vehicle_status,vehicleid,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,COALESCE(driver_name,'N/A') as driver_name,
			ROUND(speed) as speed,ROUND(odometer,3) as odometer,COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,ROUND(car_battery,2) as battery_voltage,
			acc_on as acc_status,ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
			TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,updatedon,safe_parking
			FROM vehicletbl v  WHERE v.status=1 AND TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) >= 1 and v.client_id=$client_id");
        } elseif ($status == 3) {
            $query = $this->db->query("SELECT 'Idle' as vehicle_status,vehicleid,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,COALESCE(driver_name,'N/A') as driver_name,
			ROUND(speed) as speed,ROUND(odometer,3) as odometer,COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,ROUND(car_battery,2) as battery_voltage,
			acc_on as acc_status,ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
			TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,updatedon,safe_parking
			FROM vehicletbl v  WHERE v.status=1 AND TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) = 0 
			and	v.client_id=$client_id");
        } elseif ($status == 4) {
            $query = $this->db->query("SELECT 'Parking' as vehicle_status,vehicleid,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,COALESCE(driver_name,'N/A') as driver_name,
			ROUND(speed) as speed,ROUND(odometer,3) as odometer,COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,ROUND(car_battery,2) as battery_voltage,
			acc_on as acc_status,ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
			TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,updatedon,safe_parking
			FROM vehicletbl v  WHERE v.status=1  AND TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =0 
			AND v.client_id=$client_id");
        } elseif ($status == 5) {
            $query = $this->db->query("SELECT 'OutOfCoverage' as vehicle_status,vehicleid,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,
			COALESCE(driver_name,'N/A') as driver_name, ROUND(speed) as speed,ROUND(odometer,3) as odometer,
			COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,ROUND(car_battery,2) as battery_voltage, 
			acc_on as acc_status,ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,v.digital_output as immoblizer_status,v.digital_output as digital_output,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,
			angle,vehicletype,updatedon,safe_parking 
			FROM vehicletbl v WHERE v.status=1 AND (TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) > '10' OR updatedon IS NULL or acc_on IS NULL or speed IS NULL)
			AND v.client_id=$client_id");
        } else {
            $query = $this->db->query("SELECT IF(
				TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) >= 1,
				'Moving',
				IF(
				 TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =1 and round(v.speed) = 0 ,
				'Idle',
				IF(
				TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <= '10' AND v.acc_on =0,         
					'Parking',          
					'OutOfCoverage'
				  )
				  )
				) as vehicle_status 
		  ,vehiclename,TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,last_ign_off), '%H Hours:%i Minutes') as last_update,
					  COALESCE(driver_name,'N/A') as driver_name, ROUND(speed) as speed,ROUND(odometer,3) as odometer,
					  COALESCE(latlon_address,'') as address,ROUND(litres,2) as fuel,
					  ROUND(car_battery,2) as battery_voltage, acc_on as acc_status, ac_flag as ac_status,IF(car_battery<7, 0, 1) as battery_status,digital_output as immoblizer_status,gpssignal as gps_status,gsmsignal as gsmlevel,lat,lng,deviceimei as vehicle_imei, 
					  TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,angle,vehicletype,updatedon,safe_parking 
			FROM vehicletbl v  WHERE v.status=1 AND v.client_id=$client_id");
       
    }

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    public function smart_distanceday_API($imei, $start_date, $end_date, $device_type, $client_id)
    {

        if ($device_type == '17') {

            $query1 = $this->db->query("SELECT odometer FROM omni_distance_data WHERE  deviceimei = '" . $imei . "' AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'   ORDER BY modified_date DESC LIMIT 1");

            if ($query1->num_rows() > 0) {
                $result = $query1->result();
                $Arr = array(

                    'distance_km' => $result[0]->odometer
                );
                return  $Obj = (object)$Arr;
            } else {

                $query1 = $this->db->query("SELECT DISTINCT SUM(distance_km) as distance_km FROM consolidate_distance_report WHERE imei = '" . $imei . "' AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND client_id ='" . $client_id . "'");



                if ($query1->num_rows() > 0) {
                    $result = $query1->result();
                    $Arr = array(

                        'distance_km' => $result[0]->distance_km
                    );
                    return  $Obj = (object)$Arr;
                }
            }
        } else {


            $playtable = "play_back_history_" . $client_id;

            $qry = $this->db->query("SHOW TABLES LIKE '" . $playtable . "'");

            if ($qry->num_rows() > 0) {

                $query1 = $this->db->query("SELECT odometer,modified_date FROM play_back_history
                     WHERE running_no =$imei AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND 
                     lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND modified_date
                     BETWEEN '" . $start_date . "' AND '" . $end_date . "'  UNION SELECT odometer,modified_date FROM $playtable WHERE running_no =$imei AND
                      lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND 
                      lon_message!='0.0' AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'  
                      ORDER BY modified_date DESC");
            } else {
                $query1 = $this->db->query("SELECT odometer,modified_date FROM play_back_history WHERE running_no =$imei AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY modified_date DESC");
            }

            if ($query1) {
                if ($query1->num_rows() > 0) {
                    $result = $query1->result();

                    $n = count($result) - 1;


                    $dist_km = round(($result[0]->odometer - $result[$n]->odometer), 3);


                    $Arr = array(
                        'distance_km' => $dist_km
                    );
                    return  $Obj = (object)$Arr;
                }
            } else {
                return false;
            }
        }
    }



    public function smart_ignday_API($imei, $start_date, $end_date)
    {

        $query = $this->db->query("SELECT SUM(TIMESTAMPDIFF(MINUTE,start_day,end_day)) as moving_duration FROM trip_report WHERE device_no =$imei AND end_day !='' AND TIMESTAMPDIFF(MINUTE,start_day,end_day) > 0 AND flag='2' AND start_day BETWEEN '" . $start_date . "' AND '" . $end_date . "'");

        if ($query->num_rows() > 0) {

            return $query->row();
        } else {
            return 0;
        }
    }

    public function smart_acday_API($imei, $start_date, $end_date)
    {


        $query = $this->db->query("SELECT SUM(TIMESTAMPDIFF(MINUTE,start_day,end_day)) as moving_duration FROM ac_report WHERE device_no =$imei AND end_day !='' AND flag='2' AND  type_id=1 AND TIMESTAMPDIFF(MINUTE,start_day,end_day) > 0 AND start_day BETWEEN '" . $start_date . "' AND '" . $end_date . "'");



        if ($query->num_rows() > 0) {

            return $query->row();
        } else {
            return 0;
        }
    }


    public function smart_idleday_API($imei, $start_date, $end_date)
    {

        $query = $this->db->query("SELECT SUM(TIMESTAMPDIFF(MINUTE,start_day,end_day)) as idel_duration FROM idle_report WHERE device_no =$imei AND end_day !='' AND flag='2' AND TIMESTAMPDIFF(MINUTE,start_day,end_day) > 0 AND start_day BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY start_day DESC");

        if ($query->num_rows() > 0) {

            return $query->row();
        } else {
            return 0;
        }
    }

    public function smart_parkday_API($imei, $start_date, $end_date)
    {
        $query = $this->db->query("SELECT SUM(TIMESTAMPDIFF(MINUTE,start_day,end_day)) as parking_duration FROM parking_report WHERE device_no =$imei AND TIMESTAMPDIFF(MINUTE,start_day,end_day) > 0 AND end_day !='' AND flag='2' AND start_day BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY start_day DESC");

        if ($query->num_rows() > 0) {

            $data[] =  $query->row();
            return $data;
        } else {
            return 0;
        }
    }




    public function smart_fuelfill_API($imei, $start_date, $end_date)
    {

        $query = $this->db->query("SELECT SUM(ROUND(fl.difference_fuel,2)) as fuel_fill_litre FROM  fuel_fill_dip_report fl  WHERE fl.running_no ='" . $imei . "' AND DATE_FORMAT(fl.end_date, '%Y-%m-%d %H:%i:%s') between '" . $start_date . "' AND '" . $end_date . "' AND fl.type_id ='2' ORDER BY fl.end_date DESC");

        if ($query->num_rows() > 0) {

            return  $query->row();
        } else {
            return '0';
        }
    }

    public function smart_fueldip_API($imei, $start_date, $end_date)
    {

        $query = $this->db->query("SELECT SUM(ROUND(fl.difference_fuel,2)) as fuel_dip_litre FROM  fuel_fill_dip_report fl  WHERE fl.running_no =$imei AND fl.end_date between '" . $start_date . "' AND '" . $end_date . "' AND fl.type_id =1 ");
        if ($query->num_rows() > 0) {

            return  $query->row();
        } else {
            return '0';
        }
    }

    public function smart_fuelconsumed_API($imei, $start_date, $end_date)
    {

        $query = $this->db->query("SELECT SUM(ROUND(fl.difference_fuel,2)) as fuel_fill_litre FROM  fuel_fill_dip_report fl  WHERE fl.running_no =$imei AND fl.end_date
         between '" . $start_date . "' AND '" . $end_date . "' AND fl.type_id =2");

        if ($query->num_rows() > 0) {
            $result = $query->row();

            $query1 = $this->db->query("SELECT odometer,litres,modified_date,speed,ignition from fuel_status  FORCE INDEX (running_no_4) WHERE 
            running_no =$imei AND flag=0 AND modified_date >= '" . $start_date . "' AND modified_date <= '" . $end_date . "' ORDER BY modified_date DESC");
            $result1 =  $query1->result();
            $n = count($result1) - 1;

            $fuel_consume = abs($result1[0]->litres + $result->fuel_fill_litre - $result1[$n]->litres);
            $distance = $result1[$n]->odometer -  $result1[0]->odometer;
            $fuel_milege = $fuel_consume / $distance;
            $Arr = array(
                'fuel_milege' => round($fuel_milege, 1),
                'fuel_consumed_litre' => round($fuel_consume, 1)
            );
            return  $Obj = (object)$Arr;
        } else {
            return 0;
        }
    }

    public function consolidate_distanceday($imei, $date, $device_type, $client_id)
    {

        $ct = date('Y-m-d');

        //  echo $device_type;exit;

        if ($date == $ct) {

            if ($device_type == '17') {

                $from_date1 = $date . " 00:00:00";
                $to_date1 = $date . " 23:59:59";



                $query1 = $this->db->query("SELECT odometer FROM omni_distance_data WHERE  deviceimei = '" . $imei . "' AND modified_date BETWEEN '" . $from_date1 . "' AND '" . $to_date1 . "'   ORDER BY modified_date DESC LIMIT 1");

                //echo "SELECT odometer FROM omni_distance_data WHERE  deviceimei = '".$imei."' AND modified_date BETWEEN '".$from_date1."' AND '".$to_date1."'   ORDER BY modified_date DESC LIMIT 1";exit;

                if ($query1->num_rows() > 0) {
                    $result = $query1->result();

                    $n = count($result) - 1;

                    // $dist_km = round(($result[0]->odometer-$result[$n]->odometer),3);


                    $Arr = array(
                        'end_odometer' => 0,
                        'start_odometer' => 0,
                        'distance_km' => $result[0]->odometer
                    );
                    return  $Obj = (object)$Arr;
                }
            } else {

                $playtable = "play_back_history_" . $client_id;

                $from_date1 = $date . " 00:00:00";
                $to_date1 = $date . " 23:59:59";

                $query1 = $this->db->query("SELECT odometer,modified_date FROM play_back_history WHERE running_no =$imei AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND DATE_FORMAT(modified_date, '%Y-%m-%d %H:%i:%s') BETWEEN '" . $from_date1 . "' AND '" . $to_date1 . "'  UNION SELECT odometer,modified_date FROM " . $playtable . " WHERE running_no =$imei AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND DATE_FORMAT(modified_date, '%Y-%m-%d %H:%i:%s') BETWEEN '" . $from_date1 . "' AND '" . $to_date1 . "'  ORDER BY modified_date DESC");


                if ($query1->num_rows() > 0) {
                    $result = $query1->result();

                    $n = count($result) - 1;

                    $dist_km = round(($result[0]->odometer - $result[$n]->odometer), 3);
                    if ($dist_km < 2) {
                        $dist_km = 0;
                    }

                    $Arr = array(
                        'end_odometer' => $result[0]->odometer,
                        'start_odometer' => $result[$n]->odometer,
                        'distance_km' => $dist_km
                    );
                    return  $Obj = (object)$Arr;
                }
            }
        } else {
            $query = $this->db->query("SELECT DISTINCT date,distance_km,start_odometer,end_odometer FROM consolidate_distance_report WHERE (imei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "' AND distance_km>1");

            if ($query->num_rows() > 0) {

                return  $query->row();
            } else {
                return 0;
            }
        }
    }




    public function consolidate_acday($imei, $date, $client_id)
    {


        $ct = date('Y-m-d');

        $ct = date('Y-m-d');

        if ($date == $ct) {
            $ct_from = $ct . ' 00:00:00';
            $ct_to = $ct . ' 23:59:59';
            $query = $this->db->query("SELECT SUM(TIMESTAMPDIFF(MINUTE,start_day,end_day)) as moving_duration FROM ac_report WHERE device_no ='" . $imei . "' AND end_day!='' AND TIMESTAMPDIFF(MINUTE,start_day,end_day)>0 AND flag='2' AND type_id=1 AND start_day BETWEEN '" . $ct_from . "' AND '" . $ct_to . "'");
        } else {
            $query = $this->db->query("SELECT DISTINCT date,running_duration as moving_duration FROM consolidate_ac_report WHERE (imei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");
        }

        if ($query->num_rows() > 0) {
            $res =  $query->row();
            return $res->moving_duration;
        } else {
            return 0;
        }
    }

    public function consolidate_ignday($imei, $date, $client_id)
    {

        $ct = date('Y-m-d');

        $ct = date('Y-m-d');

        if ($date == $ct) {
            $ct_from = $ct . ' 00:00:00';
            $ct_to = $ct . ' 23:59:59';
            $query = $this->db->query("SELECT SUM(TIMESTAMPDIFF(MINUTE,start_day,end_day)) as moving_duration FROM trip_report WHERE device_no ='" . $imei . "' AND end_day!='' AND flag='2' AND start_day BETWEEN '" . $ct_from . "' AND '" . $ct_to . "'");
        } else {
            $query = $this->db->query("SELECT DISTINCT date,moving_duration FROM consolidate_ign_report WHERE (imei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");
        }

        if ($query->num_rows() > 0) {
            $res =  $query->row();
            return $res->moving_duration;
        } else {
            return 0;
        }
    }

    public function consolidate_idleday($imei, $date, $client_id)
    {
        $ct = date('Y-m-d');

        if ($date == $ct) {
            $ct_from = $ct . ' 00:00:00';
            $ct_to = $ct . ' 23:59:59';

            $query = $this->db->query("SELECT SUM(TIMESTAMPDIFF(MINUTE,start_day,end_day)) as idel_duration FROM idle_report WHERE device_no ='" . $imei . "' AND end_day !='' AND end_day!='' AND flag='2' AND DATE_FORMAT(start_day, '%Y-%m-%d %H:%i') BETWEEN '" . $ct_from . "' AND '" . $ct_to . "' ORDER BY start_day DESC");
        } else {
            $query = $this->db->query("SELECT DISTINCT date,idel_duration FROM consolidate_idle_report WHERE (imei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");
        }




        if ($query->num_rows() > 0) {
            $res =  $query->row();
            return $res->idel_duration;
        } else {
            return 0;
        }
    }

    public function consolidate_parkday($imei, $date, $client_id)
    {

        $ct = date('Y-m-d');

        if ($date == $ct) {
            $ct_from = $ct . ' 00:00:00';
            $ct_to = $ct . ' 23:59:59';
            $query = $this->db->query("SELECT SUM(TIMESTAMPDIFF(MINUTE,start_day,end_day)) as parking_duration FROM parking_report WHERE device_no ='" . $imei . "' AND end_day !='' AND flag='2' AND DATE_FORMAT(start_day, '%Y-%m-%d %H:%i') BETWEEN '" . $ct_from . "' AND '" . $ct_to . "' ORDER BY start_day DESC");
        } else {

            $query = $this->db->query("SELECT DISTINCT date,parking_duration FROM consolidate_park_report WHERE (imei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");
        }


        if ($query->num_rows() > 0) {
            $res =  $query->row();
            return $res->parking_duration;
        } else {
            return 0;
        }
    }

    public function consolidate_allrpmday($imei, $date, $client_id)
    {
        $ct = date('Y-m-d');
        if ($date == $ct) {
            $ct_from = $ct . ' 00:00:00';
            $ct_to = $ct . ' 23:59:59';
            $query = $this->db->query("SELECT DISTINCT date,normal_rpm,idle_rpm,milege,under_load,SUM(normal_rpm + idle_rpm+under_load) as totalrpm FROM consolidate_rpm_report WHERE (deviceimei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");
        } else {
            $query = $this->db->query("SELECT DISTINCT date,normal_rpm,idle_rpm,milege,under_load FROM consolidate_rpm_report WHERE (deviceimei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");

            //  echo "SELECT DISTINCT date,normal_rpm,idle_rpm,milege FROM consolidate_rpm_report WHERE (deviceimei = '".$imei."' AND date = '".$date."') AND client_id ='".$client_id."'";exit;
        }

        if ($query->num_rows() > 0) {
            $res =  $query->row();
            return $res;
        } else {
            return 0;
        }
    }



    public function consolidate_fuelfill($imei, $date, $client_id)
    {

        $ct = date('Y-m-d');

        if ($date == $ct) {
            $ct_from = $ct . ' 00:00:00';
            $ct_to = $ct . ' 23:59:59';

            $query = $this->db->query("SELECT SUM(ROUND(fl.difference_fuel,2)) as fuel_fill_litre FROM  fuel_fill_dip_report fl  WHERE fl.running_no ='" . $imei . "' AND DATE_FORMAT(fl.end_date, '%Y-%m-%d %H:%i:%s') between '" . $ct_from . "' AND '" . $ct_to . "' AND fl.type_id ='2' ORDER BY fl.end_date DESC");

            if ($query->num_rows() > 0) {
                $result = $query->row();

                $query1 = $this->db->query("SELECT odometer,litres,modified_date,speed,ignition from fuel_status  FORCE INDEX (running_no_4) WHERE running_no ='" . $imei . "' AND flag=0 AND modified_date >= '" . $ct_from . "' AND modified_date <= '" . $ct_to . "' ORDER BY modified_date ASC");


                $result1 =  $query1->result();
                $n = count($result1) - 1;

                $Arr = array(
                    'start_fuel' => round($result1[0]->litres, 1),
                    'end_fuel' => round($result1[$n]->litres, 1),
                    'fuel_fill_litre' => $result->fuel_fill_litre
                );
                return  $Obj = (object)$Arr;
            }
        } else {

            $query = $this->db->query("SELECT DISTINCT date,fuel_fill_litre,start_fuel,end_fuel FROM consolidate_fuelfill_report WHERE (imei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");
            // echo "SELECT DISTINCT date,fuel_fill_litre,start_fuel,end_fuel FROM consolidate_fuelfill_report WHERE (imei = '".$imei."' AND date = '".$date."') AND client_id ='".$client_id."'";exit;
            if ($query->num_rows() > 0) {

                return $query->row();
            } else {
                return '0';
            }
        }
    }

    public function consolidate_fueldip($imei, $date, $client_id)
    {

        $ct = date('Y-m-d');

        if ($date == $ct) {
            $ct_from = $ct . ' 00:00:00';
            $ct_to = $ct . ' 23:59:59';

            $query = $this->db->query("SELECT ROUND(fl.difference_fuel,2) as difference_fuel,fl.start_fuel,fl.end_fuel FROM  fuel_fill_dip_report fl  WHERE fl.running_no ='" . $imei . "' AND DATE_FORMAT(fl.end_date, '%Y-%m-%d %H:%i:%s') between '" . $ct_from . "' AND '" . $ct_to . "' AND fl.type_id ='1' ORDER BY fl.end_date DESC");

            if ($query->num_rows() > 0) {
                $result = $query->result();

                $n = count($result) - 1;

                $fuel_fill = 0;
                foreach ($result as $list) {

                    $fuel_fill += $list->difference_fuel;
                }


                $Arr = array(
                    'start_fuel' => $result[0]->start_fuel,
                    'end_fuel' => $result[$n]->end_fuel,
                    'fuel_dip_litre' => $fuel_fill
                );
                return  $Obj = (object)$Arr;
            }
        } else {

            $query = $this->db->query("SELECT DISTINCT date,fuel_dip_litre FROM consolidate_fueldip_report WHERE (imei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");
            if ($query->num_rows() > 0) {
                $res =  $query->row();
                return $res->fuel_dip_litre;
            } else {
                return 0;
            }
        }
    }

    public function consolidate_fuelconsumed($imei, $date, $client_id)
    {

        $ct = date('Y-m-d');

        if ($date == $ct) {
            $ct_from = $ct . ' 00:00:00';
            $ct_to = $ct . ' 23:59:59';

            $query = $this->db->query("SELECT
                                        sum(difference_fuel) as fuel_dip_litre
                                         FROM fuel_fill_dip_report  WHERE type_id = '1' AND running_no='" . $imei . "' AND  
                                         DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:%s') between '" . $ct_from . "' AND '" . $ct_to . "'
                                         UNION SELECT sum(difference_fuel) as fuel_fill_litre
                                         FROM fuel_fill_dip_report WHERE type_id = '2' AND running_no='" . $imei . "' AND 
                                        DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:%s') between '" . $ct_from . "' AND '" . $ct_to . "'");

            // echo "(SELECT
            //                            sum(difference_fuel) as fuel_dip_litre
            //                             FROM fuel_fill_dip_report  WHERE type_id = '1' AND running_no='".$imei."' AND  
            //                             DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:%s') between '".$ct_from."' AND '".$ct_to."')
            //                             UNION
            //                           (SELECT
            //                            sum(difference_fuel) as fuel_fill_litre
            //                            FROM fuel_fill_dip_report WHERE type_id = '2' AND running_no='".$imei."' AND 
            //                            DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:%s') between '".$ct_from."' AND '".$ct_to."')";exit;


            if ($query->num_rows() > 0) {
                $result = $query->result();

                $query1 = $this->db->query("SELECT odometer,litres,modified_date,speed,ignition from fuel_status  FORCE INDEX (running_no_4) WHERE running_no ='" . $imei . "' AND flag='0' AND modified_date >= '" . $ct_from . "' AND modified_date <= '" . $ct_to . "' ORDER BY modified_date DESC");
                // echo "SELECT odometer,litres,modified_date,speed,ignition from fuel_status  FORCE INDEX (running_no_4) WHERE running_no ='".$imei."' AND flag='0' AND modified_date >= '".$ct_from."' AND modified_date <= '".$ct_to."' ORDER BY modified_date DESC";exit;
                $fuel_fill = $result[1]->fuel_dip_litre + ($result->fuel_dip_litre);
                $result1 =  $query1->result();
                $n = count($result1) - 1;

                $distance  = round($result1[0]->odometer, 3) - round($result1[$n]->odometer, 3);

                $fuel_consumed = round($result1[$n]->litres, 1) + $fuel_fill - round($result1[0]->litres, 1);

                $milege = $distance / $fuel_consumed;

                $Arr = array(
                    'fuel_consumed_litre' => $fuel_consumed,
                    'fuel_millege' => $milege
                );
                //    print_r($Arr);exit;
                return  $Obj = (object)$Arr;
            }
        } else {

            $query = $this->db->query("SELECT DISTINCT date,fuel_consumed_litre,fuel_millege FROM consolidate_fuelcosumed_report WHERE (imei = '" . $imei . "' AND date = '" . $date . "') AND client_id ='" . $client_id . "'");
            if ($query->num_rows() > 0) {
                return  $query->row();
            } else {
                return 0;
            }
        }
    }


    public function exereportchk_details($userid)
    {

        $query = $this->db->query("SELECT * FROM executive_report_chk WHERE status=1 AND user_id=$userid");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function smartreportchk_details($userid)
    {

        $query = $this->db->query("SELECT * FROM smart_report_chk WHERE status=1 AND user_id=$userid");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }


    public function fuel_vehicle($client_id, $userid, $roleid)
    {

        if ($roleid == 6) {
            $query = $this->db->query("SELECT v.vehicleid,v.vehiclename,v.deviceimei FROM vehicletbl v INNER JOIN assign_owner ao ON ao.vehicle_id = v.vehicleid  WHERE ao.owner_id = '" . $userid . "' AND v.client_id='" . $client_id . "' AND (v.device_type=2 OR v.device_type=4 OR v.device_type=6 OR v.device_type=7 OR v.device_type=12 OR v.device_type=13 OR v.device_type=14)");
        } else {
            $query = $this->db->query("SELECT vehicleid,vehiclename,deviceimei FROM vehicletbl WHERE client_id='" . $client_id . "' AND (device_type=2 OR device_type=4 OR device_type=6 OR device_type=7 OR device_type=12 OR device_type=13 OR device_type=14)");
        }

        if ($query->num_rows() > 0) {

            return $query->result();
        } else {
            return false;
        }
    }

    public function fuelfill_dipreport($from, $to, $d_id, $type_id)
    {

        if ($type_id == 2) {


            $query = $this->db->query("SELECT fl.id,fl.running_no ,fl.lat,fl.lng,fl.start_fuel,fl.end_fuel,ROUND(fl.difference_fuel,2) as difference_fuel,fl.end_date as created_on,fl.end_date,fl.type_id, fl.location_name,v.vehiclename FROM vehicletbl v LEFT JOIN fuel_fill_dip_report fl ON fl.running_no = v.deviceimei WHERE v.deviceimei =$d_id AND fl.end_date between '" . $from . "' AND '" . $to . "' AND fl.type_id ='2' ORDER BY fl.end_date DESC");

            // $query = $this->db->query("SELECT fl.id,fl.running_no ,fl.lat,fl.lng,fl.start_fuel,fl.end_fuel,ROUND(fl.difference_fuel,2) as difference_fuel,fl.end_date as created_on,fl.end_date,fl.type_id, fl.location_name FROM vehicletbl v LEFT JOIN fuel_fill_dip_report fl ON fl.running_no = v.deviceimei WHERE v.deviceimei ='".$d_id."' AND fl.end_date between '".$from."' AND '".$to."' AND fl.type_id ='2' ORDER BY fl.end_date DESC");
        } else if ($type_id == 3) {

            $query = $this->db->query("SELECT fl.id,fl.running_no ,fl.lat,fl.lng,fl.start_fuel,fl.end_fuel,ROUND(fl.difference_fuel,2) as difference_fuel,fl.end_date as created_on,fl.end_date,fl.type_id, fl.location_name,v.vehiclename FROM vehicletbl v LEFT JOIN fuel_fill_dip_report fl ON fl.running_no = v.deviceimei WHERE v.deviceimei =$d_id AND fl.end_date between '" . $from . "' AND '" . $to . "' ORDER BY fl.end_date DESC");
            // $query = $this->db->query("SELECT fl.id,fl.running_no ,fl.lat,fl.lng,fl.start_fuel,fl.end_fuel,ROUND(fl.difference_fuel,2) as difference_fuel,fl.start_date,fl.end_date,fl.end_date as created_on,fl.type_id, fl.location_name FROM vehicle v LEFT JOIN fuel_fill_dip_report fl ON fl.running_no = v.deviceimei WHERE v.deviceimei ='".$d_id."' AND fl.end_date between '".$from."' AND '".$to."' AND fl.type_id ='1' ORDER BY fl.end_date DESC");
        } else if ($type_id == 1) {

            $query = $this->db->query("SELECT fl.id,fl.running_no ,fl.lat,fl.lng,fl.start_fuel,fl.end_fuel,ROUND(fl.difference_fuel,2) as difference_fuel,fl.end_date as created_on,fl.end_date,fl.type_id, fl.location_name,v.vehiclename FROM vehicletbl v LEFT JOIN fuel_fill_dip_report fl ON fl.running_no = v.deviceimei WHERE v.deviceimei =$d_id AND fl.end_date between '" . $from . "' AND '" . $to . "' AND fl.type_id ='1' ORDER BY fl.end_date DESC");

            //$query = $this->db->query("SELECT fl.id,fl.running_no ,fl.lat,fl.lng,fl.start_fuel,fl.end_fuel,ROUND(fl.difference_fuel,2) as difference_fuel,fl.start_date,fl.end_date,fl.end_date as created_on,fl.type_id, fl.location_name FROM vehicletbl v LEFT JOIN fuel_fill_dip_report fl ON fl.running_no = v.deviceimei WHERE v.deviceimei ='".$d_id."' AND fl.end_date between '".$from."' AND '".$to."' ORDER BY fl.end_date DESC");
        }

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function geolocation($client_id, $userid, $roleid)
    {

        if ($roleid == 8) {
            $query = $this->db->query("SELECT Location_Id,Location_short_name,Lat,Lng,radius FROM location_list WHERE client_id='" . $client_id . "'");
        } else {
            $query = $this->db->query("SELECT Location_Id,Location_short_name,Lat,Lng,radius FROM location_list WHERE CreatedBy='" . $userid . "'");
        }
        if ($query->num_rows() > 0) {
            $geolocation = $query->result();
            foreach ($geolocation  as $list) {
                $assignvehicles_count = $this->db->query("SELECT count(id) as assign_location_count FROM 
                   assign_geo_fenceing WHERE geo_location_id = $list->Location_Id AND client_id=$client_id");
                $count = $assignvehicles_count->row();
                $count =  ($count) ? $count->assign_location_count : 0;

                $data[] = array(
                    'Location_Id' => $list->Location_Id,
                    'Location_short_name' => $list->Location_short_name,
                    'Lat' => $list->Lat,
                    'Lng' => $list->Lng,
                    'radius' => $list->radius,
                    'assignvehicles_count' => $count
                );
            }

            return $data;
        } else {
            return false;
        }
    }

    public function geofence_report($from, $to, $vehicle, $location, $client_id, $user_id)
    {

        if ($vehicle == 'all' && $location == '') {

            $query = $this->db->query("SELECT SEC_TO_TIME(TIMESTAMPDIFF(SECOND,tg.in_datetime,tg.out_datetime)) as time_duration,tg.out_datetime,tg.in_datetime,
                            tg.geo_location_id,v.vehiclename,ll.Location_short_name,tg.client_id,ll.createdby
                            from geofence_report tg 
                            LEFT JOIN vehicletbl v ON v.vehicleid = tg.vehicle_id 
                            LEFT JOIN location_list ll on ll.Location_Id = tg.geo_location_id 
                            where tg.in_datetime!='' 
                            AND tg.client_id='" . $client_id . "'
                            AND ll.createdby='" . $user_id . "'
                            AND tg.in_datetime 
                            BETWEEN '" . $from . "' AND '" . $to . "' 
                            order by tg.in_datetime DESC");
            //echo '1'; exit;

            if ($query) {
                return $query->result();
            } else {
                return false;
            }
        } else if ($vehicle == 'all' && $location != '') {

            $query = $this->db->query("SELECT SEC_TO_TIME(TIMESTAMPDIFF(SECOND,tg.in_datetime,tg.out_datetime)) as time_duration,tg.out_datetime,tg.in_datetime,tg.geo_location_id,v.vehiclename,ll.Location_short_name,tg.client_id,ll.createdby from geofence_report tg LEFT JOIN vehicletbl v ON v.vehicleid = tg.vehicle_id LEFT JOIN location_list ll on ll.Location_Id = tg.geo_location_id where tg.geo_location_id = '" . $location . "' and tg.in_datetime!='' and tg.client_id='" . $client_id . "' AND ll.CreatedBy='" . $user_id . "' and tg.in_datetime BETWEEN '" . $from . "' AND '" . $to . "' order by tg.in_datetime DESC");


            if ($query) {
                return $query->result();
            } else {
                return false;
            }
        } else if ($vehicle != '' && $location != '') {

            $query = $this->db->query("SELECT SEC_TO_TIME(TIMESTAMPDIFF(SECOND,tg.in_datetime,tg.out_datetime)) as time_duration,tg.out_datetime,tg.in_datetime,tg.geo_location_id,v.vehiclename,ll.Location_short_name,tg.client_id,ll.createdby from geofence_report tg LEFT JOIN vehicletbl v ON v.vehicleid = tg.vehicle_id LEFT JOIN location_list ll on ll.Location_Id = tg.geo_location_id where tg.geo_location_id = '" . $location . "' and v.vehicleid = '" . $vehicle . "' and tg.in_datetime!='' and tg.client_id='" . $client_id . "' AND ll.CreatedBy='" . $user_id . "' and tg.in_datetime BETWEEN '" . $from . "' AND '" . $to . "' order by tg.in_datetime DESC");


            if ($query) {
                return $query->result();
            } else {
                return false;
            }
        } else if ($vehicle != '' && $location == '') {

            $query = $this->db->query("SELECT SEC_TO_TIME(TIMESTAMPDIFF(SECOND,tg.in_datetime,tg.out_datetime)) as time_duration,tg.out_datetime,tg.in_datetime,tg.geo_location_id,v.vehiclename,ll.Location_short_name,tg.client_id,ll.createdby from geofence_report tg LEFT JOIN vehicletbl v ON v.vehicleid = tg.vehicle_id LEFT JOIN location_list ll on ll.Location_Id = tg.geo_location_id where v.vehicleid = '" . $vehicle . "' and tg.in_datetime!='' and tg.client_id='" . $client_id . "' AND ll.CreatedBy='" . $user_id . "' and tg.in_datetime BETWEEN '" . $from . "' AND '" . $to . "' order by tg.in_datetime DESC");



            if ($query) {
                return $query->result();
            } else {
                return false;
            }
        } else if ($location != '' && $vehicle == '') {

            $query = $this->db->query("SELECT SEC_TO_TIME(TIMESTAMPDIFF(SECOND,tg.in_datetime,tg.out_datetime)) as time_duration,tg.out_datetime,tg.in_datetime,tg.geo_location_id,v.vehiclename,ll.Location_short_name,tg.client_id,ll.createdby from geofence_report tg LEFT JOIN vehicletbl v ON v.vehicleid = tg.vehicle_id LEFT JOIN location_list ll on ll.Location_Id = tg.geo_location_id where tg.geo_location_id = '" . $location . "' and tg.in_datetime!='' and tg.client_id='" . $client_id . "' AND ll.CreatedBy='" . $user_id . "'  and tg.in_datetime BETWEEN '" . $from . "' AND '" . $to . "' order by tg.in_datetime DESC");
            //echo '2'; exit;

            if ($query) {
                return $query->result();
            } else {
                return false;
            }
        } else {
            $query = $this->db->query("SELECT SEC_TO_TIME(TIMESTAMPDIFF(SECOND,tg.in_datetime,tg.out_datetime)) as time_duration,tg.out_datetime,tg.in_datetime,tg.geo_location_id,v.vehiclename,ll.Location_short_name,tg.client_id,ll.createdby from geofence_report tg LEFT JOIN vehicletbl v ON v.vehicleid = tg.vehicle_id LEFT JOIN location_list ll on ll.Location_Id = tg.geo_location_id where  tg.in_datetime!='' and  tg.client_id='" . $client_id . "' AND ll.CreatedBy='" . $user_id . "' and tg.in_datetime BETWEEN '" . $from . "' AND '" . $to . "' order by tg.in_datetime DESC");


            if ($query) {
                return $query->result();
            } else {
                return false;
            }
        }
    }

    public function hublocation($client_id)
    {

        $query = $this->db->query("SELECT id,location_name FROM hubpoint_location WHERE client_id='" . $client_id . "'");
        if ($query->num_rows() > 0) {

            return $query->result();
        } else {
            return false;
        }
    }



    public function hubpoint_report($from, $to, $vehicleid, $client_id)
    {

        if (!empty($vehicleid)) {

            $query1 = $this->db->query("SELECT ts.start_odometer,ts.end_odometer,ts.out_datetime as start_day,ts.in_datetime as end_day,
                TIME_FORMAT(TIMEDIFF(ts.in_datetime,ts.out_datetime), '%H:%i:%s') as total_dur,round((ts.end_odometer - ts.start_odometer)) as distance,
                ts.trip_id,ts.vehicle_id,v.deviceimei,v.vehiclename,TIMESTAMPDIFF(MINUTE,ts.out_datetime,ts.in_datetime) as trip_mins ,
                TIMESTAMPDIFF(HOUR,ts.out_datetime,ts.in_datetime) as trip_hours,tsl.location_name 
                FROM hub_report ts 
                LEFT JOIN vehicletbl v ON v.vehicleid = ts.vehicle_id 
                LEFT JOIN hubpoint_location tsl ON tsl.id = ts.g_id 
                WHERE ts.vehicle_id ='" . $vehicleid . "' 
                AND ts.client_id = '" . $client_id . "' 
                AND ts.location_status ='2' 
                AND (ts.end_odometer - ts.start_odometer) >0 
                AND ts.out_datetime
                BETWEEN '" . $from . "' AND '" . $to . "' ORDER BY ts.id DESC");




            if ($query1->num_rows() > 0) {
                //$result=$query->result_array();
                return $query1->result();
            } else {
                return FALSE;
            }
        } else if ($vehicleid == NULL) {

            return FALSE;
        }
    }
    public function acvehiclelist($client_id)
    {
        $query = $this->db->query("SELECT deviceimei,vehicleid,vehiclename,client_id,vehicletype
			FROM vehicletbl_2 
			WHERE status=1 AND client_id=$client_id AND device_type='18'");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    public function ac_report_list($from_date, $to_date, $deviceimei, $time)
    {
        $query = $this->db->query("SELECT ir.report_id,ir.device_no,TIME_FORMAT(TIMEDIFF(ir.end_day,ir.start_day), '%H:%i:%s') as time_duration,
                    ir.s_lat,ir.s_lng,ir.start_day,ir.end_day,v.vehiclename,ir.start_location,ir.end_location,v.vehiclename 
                    FROM ac_report ir 
                    LEFT JOIN vehicletbl v ON ir.vehicle_id = v.vehicleid  
                    WHERE ir.device_no ='" . $deviceimei . "' 
                    AND ir.flag='2' 
                    AND ir.end_day !='' 
                    AND TIMESTAMPDIFF(MINUTE,ir.start_day,ir.end_day)>'" . $time . "' 
                    AND ir.start_day >= '" . $from_date . "' 
                    AND ir.start_day <= '" . $to_date . "' 
                    ORDER BY start_day DESC");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function consolidatedata_json($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {
                $query =  $this->db->query("SELECT 
        (SELECT SUM(DISTINCT d.`distance_km`) FROM consolidate_distance_report d INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as distance,
        (SELECT SUM(DISTINCT d.`moving_duration`) FROM consolidate_ign_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as moving_duration,
        (SELECT SUM(DISTINCT d.`idel_duration`) FROM consolidate_idle_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid ) as idle_duration,
        (SELECT SUM(DISTINCT d.`parking_duration`) FROM consolidate_park_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as parking_duration,
        (SELECT SUM(DISTINCT d.`running_duration`) FROM consolidate_ac_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as ac_duration,
        (SELECT SUM(DISTINCT d.`fuel_consumed_litre`) FROM consolidate_fuelcosumed_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as fuel_consumed_litre,
        (SELECT SUM(DISTINCT d.`fuel_millege`) FROM consolidate_fuelcosumed_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as fuel_milege,
        (SELECT SUM(DISTINCT d.`fuel_fill_litre`) FROM consolidate_fuelfill_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as fuel_fill_litre,
        (SELECT SUM(DISTINCT d.`fuel_dip_litre`) FROM consolidate_fueldip_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as fuel_dip_litre,
        (SELECT SUM(DISTINCT d.normal_rpm+ d.under_load+ d.idle_rpm) FROM consolidate_rpm_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicle_id 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as totalrpm
        ");
            } else {
                $query =  $this->db->query("SELECT (SELECT SUM(distance_km) FROM consolidate_distance_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as distance,
        (SELECT SUM(moving_duration) FROM consolidate_ign_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as moving_duration,
        (SELECT SUM(idel_duration) FROM consolidate_idle_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as idle_duration,
        (SELECT SUM(parking_duration) FROM consolidate_park_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as parking_duration,
        (SELECT SUM(running_duration) FROM consolidate_ac_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as ac_duration,
        (SELECT SUM(fuel_consumed_litre) FROM consolidate_fuelcosumed_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as fuel_consumed_litre,
        (SELECT SUM(fuel_millege) FROM consolidate_fuelcosumed_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as fuel_milege,
        (SELECT SUM(fuel_fill_litre) FROM consolidate_fuelfill_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as fuel_fill_litre,
        (SELECT SUM(fuel_dip_litre) FROM consolidate_fueldip_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as fuel_dip_litre,
        (SELECT SUM(normal_rpm+under_load+idle_rpm) FROM consolidate_rpm_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as totalrpm
        ");
            }
        } else {
            $query =  $this->db->query("SELECT (SELECT SUM(distance_km) FROM consolidate_distance_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  AND imei=$vehicle) as distance,
    (SELECT SUM(moving_duration) FROM consolidate_ign_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as moving_duration,
    (SELECT SUM(idel_duration) FROM consolidate_idle_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as idle_duration,
    (SELECT SUM(parking_duration) FROM consolidate_park_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as parking_duration,
    (SELECT SUM(running_duration) FROM consolidate_ac_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as ac_duration,
    (SELECT SUM(fuel_consumed_litre) FROM consolidate_fuelcosumed_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as fuel_consumed_litre,
    (SELECT SUM(fuel_millege) FROM consolidate_fuelcosumed_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as fuel_milege,
    (SELECT SUM(fuel_fill_litre) FROM consolidate_fuelfill_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as fuel_fill_litre,
    (SELECT SUM(fuel_dip_litre) FROM consolidate_fueldip_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as fuel_dip_litre,
    (SELECT SUM(normal_rpm+under_load+idle_rpm) FROM consolidate_rpm_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND deviceimei=$vehicle ) as totalrpm
    ");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->row();
            }
        } else {
            return array();
        }
    }



    public function consolidate_distance_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {

                $query =  $this->db->query(" SELECT DISTINCT d.date, round(SUM(d.distance_km)) as distance FROM consolidate_distance_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(DISTINCT d.distance_km)) as distance FROM consolidate_distance_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(DISTINCT d.distance_km)) as distance FROM consolidate_distance_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }


    public function consolidate_moving_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {


                $query =  $this->db->query(" SELECT d.date,SUM(d.moving_duration * 60) as  moving_duration FROM consolidate_ign_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query(" SELECT d.date,SUM(d.moving_duration * 60) as  moving_duration FROM consolidate_ign_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query(" SELECT d.date,SUM(d.moving_duration * 60) as  moving_duration FROM consolidate_ign_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }


    public function consolidate_idle_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {


                $query =  $this->db->query(" SELECT d.date,SUM(d.idel_duration * 60) as  idle_duration FROM consolidate_idle_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query(" SELECT d.date,SUM(d.idel_duration * 60) as  idle_duration FROM consolidate_idle_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query(" SELECT d.date,SUM(d.idel_duration * 60) as  idle_duration FROM consolidate_idle_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }


    public function consolidate_park_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {


                $query =  $this->db->query(" SELECT d.date,SUM(d.parking_duration * 60) as  parking_duration FROM consolidate_park_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query(" SELECT d.date,SUM(d.parking_duration * 60) as  parking_duration FROM consolidate_park_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query(" SELECT d.date,SUM(d.parking_duration * 60) as  parking_duration FROM consolidate_park_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }



    public function consolidate_ac_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {

                $query =  $this->db->query(" SELECT d.date,SUM(d.running_duration * 60) as  ac_duration FROM consolidate_ac_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query(" SELECT d.date,SUM(d.running_duration * 60) as  ac_duration FROM consolidate_ac_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query(" SELECT d.date,SUM(d.running_duration * 60) as  ac_duration FROM consolidate_ac_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }




    public function consolidate_fuelfill_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {
                $query =  $this->db->query(" SELECT DISTINCT d.date, round(SUM(d.fuel_fill_litre)) as fuel_fill_litre FROM consolidate_fuelfill_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(d.fuel_fill_litre)) as fuel_fill_litre FROM consolidate_fuelfill_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(d.fuel_fill_litre)) as fuel_fill_litre FROM consolidate_fuelfill_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }





    public function consolidate_fueldip_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {
                $query =  $this->db->query(" SELECT DISTINCT d.date, round(SUM(d.fuel_dip_litre)) as fuel_dip_litre FROM consolidate_fueldip_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(d.fuel_dip_litre)) as fuel_dip_litre FROM consolidate_fueldip_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(d.fuel_dip_litre)) as fuel_dip_litre FROM consolidate_fueldip_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }




    public function consolidate_fuelconsume_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {
                $query =  $this->db->query(" SELECT DISTINCT d.date, round(SUM(d.fuel_consumed_litre)) as fuel_consumed_litre, FROM consolidate_fuelcosumed_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(d.fuel_consumed_litre)) as fuel_consumed_litre FROM consolidate_fuelcosumed_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(d.fuel_consumed_litre)) as fuel_consumed_litre FROM consolidate_fuelcosumed_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }



    public function consolidate_fuelmilege_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {
                $query =  $this->db->query(" SELECT DISTINCT d.date, round(SUM(d.fuel_millege)) as fuel_millege, FROM consolidate_fuelcosumed_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicleid WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(d.fuel_millege)) as fuel_millege FROM consolidate_fuelcosumed_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query("SELECT DISTINCT d.date, round(SUM(d.fuel_millege)) as fuel_millege FROM consolidate_fuelcosumed_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.imei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {
            return array();
        }
    }


    public function consolidate_rpm_chart($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {
        if ($vehicle == 0) {

            if ($role == 6) {


                $query =  $this->db->query(" SELECT d.date,SUM(d.normal_rpm+d.under_load+d.idle_rpm) as  rpm_duration FROM consolidate_rpm_report d INNER JOIN assign_owner a 
        ON a.vehicle_id = d.vehicle_id WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id AND a.owner_id=$userid GROUP BY d.date");
            } else {
                $query =  $this->db->query(" SELECT d.date,SUM(d.normal_rpm+d.under_load+d.idle_rpm) as  rpm_duration FROM consolidate_rpm_report d 
        WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
        AND d.client_id=$client_id GROUP BY d.date");
            }
        } else {
            $query =  $this->db->query(" SELECT d.date,SUM(d.normal_rpm+d.under_load+d.idle_rpm) as  rpm_duration FROM consolidate_rpm_report d 
    WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
    AND d.client_id=$client_id AND d.deviceimei=$vehicle GROUP BY d.date");
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        } else {

            return array();
        }
    }


    public function drum_report_list($from, $to, $vehicleid = NULL, $time)
    {
        if (!empty($vehicleid)) {
            $query = $this->db->query("SELECT TIME_FORMAT(TIMEDIFF(ip.end_day,ip.start_day), '%H:%i:%s') as time_duration,
                        TIMESTAMPDIFF(MINUTE,ip.start_day,ip.end_day) as t_min,ip.start_day,ip.end_day,(ip.end_odometer - ip.start_odometer) as distance,
                        ip.s_lng,ip.s_lat,ip.e_lng,ip.e_lat,ip.device_no,v.vehiclename,v.device_config_type,v.deviceimei,ip.end_odometer,ip.start_odometer,
                        ip.start_location,ip.end_location,ip.report_id 
                        FROM trip_report ip 
                        LEFT JOIN vehicletbl v ON ip.vehicle_id = v.vehicleid
                        WHERE ip.device_no='" . $vehicleid . "' 
                        AND ip.end_day !='' 
                        AND ip.flag='2' 
                        AND TIMESTAMPDIFF(MINUTE,ip.start_day,ip.end_day)>'" . $time . "' 
                        AND  ip.start_day >= '" . $from . "' 
                        AND ip.start_day <= '" . $to . "' 
                        GROUP BY ip.end_day 
                        ORDER BY ip.start_day DESC");
            //            $query = $this->db->query("SELECT ir.report_id,ir.device_no,TIME_FORMAT(TIMEDIFF(ir.end_day,ir.start_day), '%H:%i:%s') as time_duration,
            //                        ir.s_lat,ir.s_lng,ir.start_day,ir.end_day,v.vehiclename,ir.start_location,ir.end_location,v.vehiclename 
            //                        FROM idle_report ir 
            //                        LEFT JOIN vehicletbl v ON ir.vehicle_id = v.vehicleid  
            //                        WHERE ir.device_no ='".$vehicleid."' 
            //                        AND ir.flag='2' 
            //                        AND ir.end_day !='' 
            //                        AND TIMESTAMPDIFF(MINUTE,ir.start_day,ir.end_day)>'".$time."' 
            //                        AND ir.start_day >= '".$from."' 
            //                        AND ir.start_day <= '".$to."' 
            //                        ORDER BY start_day DESC");

            if ($query->num_rows() > 0) {
                //$result=$query->result_array();
                return $query->result();
            } else {
                return array();
            }
        } else if ($vehicleid == NULL) {
            return array();
        }
    }
    public function bucket_report_list($from, $to, $vehicleid = NULL, $time)
    {
        if (!empty($vehicleid)) {
            $query = $this->db->query("SELECT TIME_FORMAT(TIMEDIFF(ip.end_day,ip.start_day), '%H:%i:%s') as time_duration,
                        TIMESTAMPDIFF(MINUTE,ip.start_day,ip.end_day) as t_min,ip.start_day,ip.end_day,(ip.end_odometer - ip.start_odometer) as distance,
                        ip.s_lng,ip.s_lat,ip.e_lng,ip.e_lat,ip.device_no,v.vehiclename,v.device_config_type,v.deviceimei,ip.end_odometer,ip.start_odometer,
                        ip.start_location,ip.end_location,ip.report_id 
                        FROM trip_report ip 
                        LEFT JOIN vehicletbl v ON ip.vehicle_id = v.vehicleid
                        WHERE ip.device_no='" . $vehicleid . "' 
                        AND ip.end_day !='' 
                        AND ip.flag='2' 
                        AND TIMESTAMPDIFF(MINUTE,ip.start_day,ip.end_day)>'" . $time . "' 
                        AND  ip.start_day >= '" . $from . "' 
                        AND ip.start_day <= '" . $to . "' 
                        GROUP BY ip.end_day 
                        ORDER BY ip.start_day DESC");
            //            $query = $this->db->query("SELECT ir.report_id,ir.device_no,TIME_FORMAT(TIMEDIFF(ir.end_day,ir.start_day), '%H:%i:%s') as time_duration,
            //                        ir.s_lat,ir.s_lng,ir.start_day,ir.end_day,v.vehiclename,ir.start_location,ir.end_location,v.vehiclename 
            //                        FROM idle_report ir 
            //                        LEFT JOIN vehicletbl v ON ir.vehicle_id = v.vehicleid  
            //                        WHERE ir.device_no ='".$vehicleid."' 
            //                        AND ir.flag='2' 
            //                        AND ir.end_day !='' 
            //                        AND TIMESTAMPDIFF(MINUTE,ir.start_day,ir.end_day)>'".$time."' 
            //                        AND ir.start_day >= '".$from."' 
            //                        AND ir.start_day <= '".$to."' 
            //                        ORDER BY start_day DESC");

            if ($query->num_rows() > 0) {
                //$result=$query->result_array();
                return $query->result();
            } else {
                return array();
            }
        } else if ($vehicleid == NULL) {
            return array();
        }
    }



    public function user_details($userid, $password)
    {

        $query = $this->db->query("SELECT userid FROM usertbl WHERE status=1 AND userid='$userid' AND (password='$password' OR secondarypassword='$password')");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return array();
        }
    }

    public function playback_data($d_id, $from, $to, $client_id)
    {

        $play_table = "play_back_history_" . $client_id;
        $qry = $this->db->query("SHOW TABLES LIKE '" . $play_table . "'");

        if ($qry->num_rows() > 0) {

            $query = $this->db->query("SELECT p.odometer,p.lat_message,p.lon_message,p.speed,p.acc_status,p.modified_date,p.angle,p.zap as address FROM play_back_history p INNER JOIN vehicletbl v ON v.deviceimei = p.running_no
        WHERE p.running_no =$d_id AND p.lat_message!='000000000' AND p.lon_message!='000000000' AND p.lat_message!='0' AND
        p.lon_message!='0' AND p.lat_message!='0.0' AND p.lon_message!='0.0' AND p.modified_date
        BETWEEN '" . $from . "' AND '" . $to . "' UNION SELECT
        ps.odometer,ps.lat_message,ps.lon_message,ps.speed,ps.acc_status,ps.modified_date,ps.angle,ps.zap as address FROM $play_table ps INNER JOIN vehicletbl v ON v.deviceimei = ps.running_no WHERE
        ps.running_no =$d_id AND
        ps.lat_message!='000000000' AND ps.lon_message!='000000000' AND ps.lat_message!='0' AND ps.lon_message!='0' AND ps.lat_message!='0.0'
        AND
        ps.lon_message!='0.0' AND ps.modified_date BETWEEN '" . $from . "' AND '" . $to . "' 
        ORDER BY modified_date ASC");
        } else {
            $query = $this->db->query("SELECT p.odometer,p.lat_message,p.lon_message,p.speed,p.acc_status,p.modified_date,p.angle,p.zap as address FROM play_back_history p INNER JOIN vehicletbl v ON v.deviceimei = p.running_no WHERE p.running_no =$d_id AND p.lat_message!='000000000' AND p.lon_message!='000000000' AND p.lat_message!='0' AND p.lon_message!='0' AND p.lat_message!='0.0' AND p.lon_message!='0.0' AND p.modified_date BETWEEN '" . $from . "' AND '" . $to . "'  ORDER BY p.modified_date ASC");
        }



        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }
    public function demo_playback_data($d_id, $from, $to, $client_id)
    {

        $play_table = "play_back_history_" . $client_id;
        $qry = $this->db->query("SHOW TABLES LIKE '" . $play_table . "'");
        $query = $this->db->query ("SELECT t.lat_message,t.lon_message,t.odometer,t.running_no,t.speed,t.angle,t.acc_status,t.door_status,t.modified_date,v.vehicleid,v.vehiclename FROM vehicletbl v  JOIN ".$play_table." t ON t.running_no = v.deviceimei WHERE v.deviceimei =$d_id  AND t.modified_date>='".$from."' AND t.modified_date<='".$to."' ORDER BY t.modified_date ASC");
        //$query = $this->db->query("SELECT t.zap AS address, t.odometer, t.running_no, t.lat_message, t.lon_message, t.speed, t.angle, t.acc_status, t.door_status, t.modified_date, v.vehicleid, v.vehiclename FROM vehicletbl v JOIN play_back_history_154 t ON t.running_no = v.deviceimei WHERE v.deviceimei = '".$d_id."' AND t.lat_message != '000000000' AND t.lon_message != '000000000' AND t.lat_message != '0' AND t.lon_message != '0' AND t.lat_message != '0.0' AND t.lon_message != '0.0' AND t.modified_date >= '".$from."' AND t.modified_date < '".$to."' ORDER BY t.modified_date ASC");
        if($query->num_rows() > 0) 
        {
            return $query->result();
        }else{
            return array();
        } 
        // if ($qry->num_rows() > 0) {

        //     $query = $this->db->query("SELECT p.odometer,p.lat_message,p.lon_message,p.speed,p.acc_status,p.modified_date,p.angle,p.zap as address FROM play_back_history p INNER JOIN vehicletbl v ON v.deviceimei = p.running_no
        // WHERE p.running_no =$d_id AND p.lat_message!='000000000' AND p.lon_message!='000000000' AND p.lat_message!='0' AND
        // p.lon_message!='0' AND p.lat_message!='0.0' AND p.lon_message!='0.0' AND p.modified_date
        // BETWEEN '" . $from . "' AND '" . $to . "' UNION SELECT
        // ps.odometer,ps.lat_message,ps.lon_message,ps.speed,ps.acc_status,ps.modified_date,ps.angle,ps.zap as address FROM $play_table ps INNER JOIN vehicletbl v ON v.deviceimei = ps.running_no WHERE
        // ps.running_no =$d_id AND
        // ps.lat_message!='000000000' AND ps.lon_message!='000000000' AND ps.lat_message!='0' AND ps.lon_message!='0' AND ps.lat_message!='0.0'
        // AND
        // ps.lon_message!='0.0' AND ps.modified_date BETWEEN '" . $from . "' AND '" . $to . "' 
        // ORDER BY modified_date ASC");
        // } else {
        //     $query = $this->db->query("SELECT p.odometer,p.lat_message,p.lon_message,p.speed,p.acc_status,p.modified_date,p.angle,p.zap as address FROM play_back_history p INNER JOIN vehicletbl v ON v.deviceimei = p.running_no WHERE p.running_no =$d_id AND p.lat_message!='000000000' AND p.lon_message!='000000000' AND p.lat_message!='0' AND p.lon_message!='0' AND p.lat_message!='0.0' AND p.lon_message!='0.0' AND p.modified_date BETWEEN '" . $from . "' AND '" . $to . "'  ORDER BY p.modified_date ASC");
        // }



        // if ($query->num_rows() > 0) {
        //     return $query->result();
        // } else {
        //     return array();
        // }
    }
    public function playback_data_old($d_id, $from, $to, $client_id)
    {

        $play_table = "play_back_history_" . $client_id;
        $qry = $this->db->query("SHOW TABLES LIKE '" . $play_table . "'");
        $query = $this->db->query ("SELECT t.zap as address,t.odometer,t.running_no,t.lat_message,t.lon_message,t.speed,t.angle,t.acc_status,t.door_status,t.modified_date,v.vehicleid,v.vehiclename FROM vehicletbl v INNER JOIN ".$play_table." t ON t.running_no = v.deviceimei WHERE v.deviceimei ='".$d_id."' AND t.lat_message!='000000000' AND t.lon_message!='000000000' AND t.lat_message!='0' AND t.lon_message!='0' AND t.lat_message!='0.0' AND t.lon_message!='0.0' AND DATE_FORMAT(t.modified_date, '%Y-%m-%d %H:%i') BETWEEN '".$from."' AND '".$to."' ORDER BY modified_date ASC");
        
        if($query->num_rows() > 0) 
        {
            return $query->result();
        }else{
            return array();
        } 
        // if ($qry->num_rows() > 0) {

        //     $query = $this->db->query("SELECT p.odometer,p.lat_message,p.lon_message,p.speed,p.acc_status,p.modified_date,p.angle,p.zap as address FROM play_back_history p INNER JOIN vehicletbl v ON v.deviceimei = p.running_no
        // WHERE p.running_no =$d_id AND p.lat_message!='000000000' AND p.lon_message!='000000000' AND p.lat_message!='0' AND
        // p.lon_message!='0' AND p.lat_message!='0.0' AND p.lon_message!='0.0' AND p.modified_date
        // BETWEEN '" . $from . "' AND '" . $to . "' UNION SELECT
        // ps.odometer,ps.lat_message,ps.lon_message,ps.speed,ps.acc_status,ps.modified_date,ps.angle,ps.zap as address FROM $play_table ps INNER JOIN vehicletbl v ON v.deviceimei = ps.running_no WHERE
        // ps.running_no =$d_id AND
        // ps.lat_message!='000000000' AND ps.lon_message!='000000000' AND ps.lat_message!='0' AND ps.lon_message!='0' AND ps.lat_message!='0.0'
        // AND
        // ps.lon_message!='0.0' AND ps.modified_date BETWEEN '" . $from . "' AND '" . $to . "' 
        // ORDER BY modified_date ASC");
        // } else {
        //     $query = $this->db->query("SELECT p.odometer,p.lat_message,p.lon_message,p.speed,p.acc_status,p.modified_date,p.angle,p.zap as address FROM play_back_history p INNER JOIN vehicletbl v ON v.deviceimei = p.running_no WHERE p.running_no =$d_id AND p.lat_message!='000000000' AND p.lon_message!='000000000' AND p.lat_message!='0' AND p.lon_message!='0' AND p.lat_message!='0.0' AND p.lon_message!='0.0' AND p.modified_date BETWEEN '" . $from . "' AND '" . $to . "'  ORDER BY p.modified_date ASC");
        // }



        // if ($query->num_rows() > 0) {
        //     return $query->result();
        // } else {
        //     return array();
        // }
    }

    public function vehicletype_data($deviceimei)
    {

        $query = $this->db->query("SELECT vehicletype FROM vehicletbl_2 WHERE status=1 AND deviceimei=$deviceimei");

        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data->vehicletype;
        } else {
            return false;
        }
    }

    public function alerttypes()
    {

        $query = $this->db->query("SELECT alert_type_id as alert_id,alert_type FROM alert_type WHERE status=1");

        if ($query->num_rows() > 0) {

            return  $query->result();
        } else {
            return false;
        }
    }


    public function alert_report($from, $to, $deviceimei = NULL, $alert_id)
    {

        if ($alert_id == 0) {

            $query = $this->db->query("SELECT v.vehiclename,a.alert_type as alert_name,s.createdon as datetime,s.alert_location as location
            FROM sms_alert s
            LEFT JOIN vehicletbl v ON s.vehicle_id = v.vehicleid INNER JOIN alert_type a ON a.alert_type_id = s.all_status
            WHERE v.deviceimei =$deviceimei AND s.createdon BETWEEN '" . $from . "' AND '" . $to . "'
            ORDER BY s.createdon DESC");
        } else {
            $query = $this->db->query("SELECT v.vehiclename,a.alert_type as alert_name,s.createdon as datetime,s.alert_location as location
            FROM sms_alert s
            LEFT JOIN vehicletbl v ON s.vehicle_id = v.vehicleid INNER JOIN alert_type a ON a.alert_type_id = s.all_status
            WHERE v.deviceimei =$deviceimei AND s.createdon BETWEEN '" . $from . "' AND '" . $to . "' AND a.alert_type_id= $alert_id
            ORDER BY s.createdon DESC");
        }

        if ($query->num_rows() > 0) {
            //$result=$query->result_array();
            return $query->result();
        } else {
            return FALSE;
        }
    }

    public function alert_settings($client_id)
    {

        $query = $this->db->query("SELECT `ac_on`,`ac_off`,`ignition_on`,`ignition_off`,`speed_alert`,`route_deviation`,`temperature_alert`,`sos_alert`,
     `geo_fence_in_circle`,`geo_fence_out_circle`,`harsh_acceleration`,`harsh_braking`,`harsh_cornering`,`speed_breaker_bump`,`accident`,`fuel_dip`,
     `fuel_fill`,`power_off`,`hub_in_circle`,`hub_out_circle`,`low_battery` FROM `alter_contacts` WHERE client_id = $client_id LIMIT 1");

        if ($query->num_rows() > 0) {

            return  $query->result();
        } else {
            return false;
        }
    }


    public function update_settings($update_data, $client_id)
    {

        $this->db->where('client_id', $client_id);
        $query = $this->db->update("alter_contacts", $update_data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function vehicle_settings($deviceimei)
    {

        $query = $this->db->query("SELECT vehiclename,parking_alerttime,idle_alerttime,speed_limit,expected_milege,idle_rpm,max_rpm,temp_low,temp_high,fuel_ltr,fuel_dip_ltr  FROM vehicletbl_2 WHERE deviceimei=$deviceimei");

        if ($query->num_rows() > 0) {

            return  $query->row();
        } else {
            return false;
        }
    }

    public function update_vehiclesettings($update_data, $deviceimei)
    {

        $this->db->where('deviceimei', $deviceimei);
        $this->db->update('vehicletbl_2', $update_data);


        $this->db->where('deviceimei', $deviceimei);
        $query = $this->db->update("vehicletbl", $update_data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function rpm_vehicle($client_id, $userid, $roleid)
    {

        if ($roleid == 6) {
            $query = $this->db->query("SELECT v.vehicleid,v.vehiclename,v.deviceimei FROM vehicletbl v INNER JOIN assign_owner ao ON ao.vehicle_id = v.vehicleid  WHERE ao.owner_id = '" . $userid . "' AND v.client_id='" . $client_id . "' AND v.device_type=17");
        } else {
            $query = $this->db->query("SELECT vehicleid,vehiclename,deviceimei FROM vehicletbl WHERE client_id='" . $client_id . "' AND device_type=17");
        }

        if ($query->num_rows() > 0) {

            return $query->result();
        } else {
            return false;
        }
    }

    public function gettripid($client_id)
    {
        $query = $this->db->query("SELECT * FROM zigma_plantrip ORDER BY trip_id DESC Limit 1");
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    public function tripplanlist($client_id, $status)
    {
        $fromdate = date('Y-m-d H:i:s', strtotime('00:00:00'));
        $todate = date('Y-m-d H:i:s', strtotime('23:59:59'));
        if ($status == 1) //in hub
        {
            $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
				v.vehiclename,v.angle,v.speed,v.updatedon,v.vehicletype,
				v.deviceimei,v.simnumber,v.vehicleid,v.lat,v.lng,
				v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
            v.acc_on,v.acc_flag,v.acc_date_time,
            v.ac_flag,v.ac_date,v.ac_km,
                                v.device_config_type,v.device_type,
				 ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
                 ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
				  '0 km' AS act_km,'0 Mins' AS act_duration,NULL as act_starttime,NULL as act_endtime
				FROM zigma_plantrip p 
				INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid 
				INNER JOIN location_list ll ON ll.Location_Id = p.start_location
				INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location 
				WHERE p.client_id =$client_id AND p.created_date BETWEEN '" . $fromdate . "' AND '" . $todate . "' AND p.status=1 ORDER BY p.created_date ASC");
        } elseif ($status == 2) { // trip Processing
            $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
				v.vehiclename,v.angle,v.speed,v.updatedon,v.vehicletype,
				v.deviceimei,v.simnumber,v.vehicleid,v.lat,v.lng,
				v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
            v.acc_on,v.acc_flag,v.acc_date_time,
            v.ac_flag,v.ac_date,v.ac_km,
                                v.device_config_type,v.device_type,
				ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
                 ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
				 '0 km' AS act_km,'0 Mins' AS act_duration,NULL as act_starttime,NULL as act_endtime
				FROM zigma_plantrip p 
				INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid 
				INNER JOIN location_list ll ON ll.Location_Id = p.start_location
				INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location 
				WHERE p.client_id =$client_id AND p.created_date BETWEEN '" . $fromdate . "' AND '" . $todate . "' AND p.status=2 ORDER BY p.created_date ASC");
        } elseif ($status == 3) { // trip completed
            $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
				v.vehiclename,v.angle,v.speed,v.updatedon,v.vehicletype,
				v.deviceimei,v.simnumber,v.vehicleid,v.lat,v.lng,
				v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
            v.acc_on,v.acc_flag,v.acc_date_time,
            v.ac_flag,v.ac_date,v.ac_km,
                                v.device_config_type,v.device_type,
				ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
                 ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
                        round(zpr.end_odometer-zpr.start_odometer) as act_km,
                        TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s') as act_duration,
                        zpr.create_datetime as act_starttime,zpr.updated_datetime as act_endtime
						
				FROM zigma_plantrip p 
				INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid 
				INNER JOIN location_list ll ON ll.Location_Id = p.start_location
				INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location 
				INNER JOIN zigma_plantrip_report1 zpr ON zpr.vehicle_id=p.vehicleid 
				WHERE p.client_id =$client_id AND p.created_date BETWEEN '" . $fromdate . "' AND '" . $todate . "' AND p.status=3 GROUP BY v.vehicleid ORDER BY p.created_date ASC");
        } else {

            $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
             v.vehiclename,v.angle,v.speed,v.updatedon,v.vehicletype,
             v.deviceimei,v.simnumber,v.vehicleid,v.lat,v.lng,
             v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
             TIME_FORMAT(TIMEDIFF(NOW(),v.last_ign_off), '%H hours:%i minutes') as last_dur, 
 TIME_FORMAT(TIMEDIFF(NOW(),v.updatedon), '%H:%i:%s') as no_last_dur,
         v.acc_on,v.acc_flag,v.acc_date_time,
         v.ac_flag,v.ac_date,
                             v.device_config_type,v.device_type,
             ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
              ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
                     round(zpr.end_odometer-zpr.start_odometer) as act_km,
                    --  TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s') as act_duration,
                     if (TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s') > 0, TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s'), 0) as act_duration,
--                      IF( 
--                         (TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s')) < 0,
--     0-TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s'),
--     TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s')
-- ) AS act_duration,
zpr.create_datetime as act_starttime,zpr.updated_datetime as act_endtime,zpr.id as ReptripID
FROM zigma_plantrip p
INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid
INNER JOIN location_list ll ON ll.Location_Id = p.start_location
INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location
LEFT JOIN zigma_plantrip_report1 zpr ON zpr.trip_id = p.trip_id AND zpr.client_id=p.client_id
WHERE p.client_id =$client_id AND (p.created_date BETWEEN '" . $fromdate . "' AND '" . $todate . "' OR (p.status=2 OR p.status=1)) GROUP BY p.id ORDER BY p.id desc");
        }
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }



    public function zigmatrip_plan_data($id)
    {

        $query = $this->db->query("SELECT p.vehicleid,p.trip_id,v.deviceimei from zigma_plantrip p INNER JOIN vehicletbl v ON v.vehicleid=p.vehicleid where p.id=$id");


        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function check_plantrip($client_id, $id)
    {

        $query = $this->db->query("SELECT  p.created_date as trip_date,p.status,p.trip_id,p.poc_number,p.client_id,p.vehicleid as vehicle_id,v.vehiclename as vehicle_name,
			v.deviceimei,p.start_location as start_geo_id,p.end_location as end_geo_id,l.Lat as s_lat,l.Lng as s_lng,l2.Lat as e_lat,l2.Lng as e_lng from zigma_plantrip p INNER JOIN location_list l ON l.Location_Id = p.start_location
			INNER JOIN location_list l2 ON l2.Location_Id=p.end_location INNER JOIN vehicletbl_2 v ON v.vehicleid=p.vehicleid where p.id=" . $id . "");


        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }


    public function trip_actual_data($client_id, $tripid, $vehicleid)
    {
        $query = $this->db->query("SELECT create_datetime as startdatetime,vehicle_name FROM zigma_plantrip_report1 z WHERE z.trip_id=$tripid AND z.vehicle_id=$vehicleid AND z.client_id=$client_id");

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {

            return (object)[];
        }
    }


    public function tripplanedit($client_id, $tripplainid)
    {
        $query = $this->db->query("SELECT * FROM zigma_plantrip WHERE status=1 AND client_id='$client_id' AND id=$tripplainid");
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return array();
        }
    }
    public function zigma_triplist($fromdate, $todate, $status, $client_id)
    {
        $query = $this->db->query("SELECT * FROM zigma_plantrip_report1 WHERE  client_id =$client_id AND create_datetime BETWEEN '" . $fromdate . "' AND '" . $todate . "' AND flag=$status ORDER BY create_datetime ASC");
        //	echo "SELECT * FROM zigma_plantrip_report1 WHERE  client_id =$client_id AND create_datetime BETWEEN '".$fromdate."' AND '".$todate."' AND flag=$status";exit;

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }
    public function location_triplist($client_id)
    {
        $query = $this->db->query("SELECT * FROM location_list WHERE client_id=$client_id");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function vehicle_triplist($client_id)
    {
        //	$query = $this->db->query("SELECT v.vehicleid,v.vehiclename,v.deviceimei,'0' AS editsts
        //	from vehicletbl v WHERE v.client_id='".$client_id."'");
        if ($client_id == 377) {
            $query = $this->db->query("SELECT v.vehicleid,v.vehiclename,v.deviceimei,
                   1 as editsts 
					from vehicletbl v 
        INNER JOIN devicetbl d ON d.deviceimei=v.deviceimei  WHERE v.client_id=$client_id GROUP BY v.vehicleid ");
        } else {

            $query = $this->db->query("SELECT v.vehicleid,v.vehiclename,v.deviceimei,
                    IF((d.device_model='Assert_Tracker' OR d.device_model='Twings_104' OR d.device_model='CONCOX'),1,0) as editsts 
					from vehicletbl v 
        INNER JOIN devicetbl d ON d.deviceimei=v.deviceimei  WHERE v.client_id=$client_id GROUP BY v.vehicleid ");
        }


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function trip_location_map($client_id)
    {
        $fromdate = date('Y-m-d H:i:s', strtotime('00:00:00'));
        $todate = date('Y-m-d H:i:s', strtotime('23:59:59'));
        $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
               v.vehiclename,v.angle,v.speed,v.updatedon,v.vehicletype,
               v.deviceimei,v.vehicleid,v.lat,v.lng,
               v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
               TIME_FORMAT(TIMEDIFF(NOW(),v.last_ign_off), '%H hours:%i minutes') as last_dur, 
   TIME_FORMAT(TIMEDIFF(NOW(),v.updatedon), '%H:%i:%s') as no_last_dur,
           v.acc_on,v.acc_flag,v.acc_date_time,
           v.ac_flag,v.ac_date,v.ac_km,
                               v.device_config_type,v.device_type,
               ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
                ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
                       round(zpr.end_odometer-zpr.start_odometer) as act_km,
                       if (TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s') > 0, TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s'), 0) as act_duration,
  zpr.create_datetime as act_starttime,zpr.updated_datetime as act_endtime,zpr.id as ReptripID
  FROM zigma_plantrip p
  INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid
  INNER JOIN location_list ll ON ll.Location_Id = p.start_location
  INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location
  LEFT JOIN zigma_plantrip_report1 zpr ON zpr.trip_id = p.trip_id AND zpr.client_id=p.client_id
  WHERE p.client_id =$client_id AND p.status=2");

        //                $query = $this->db->query("SELECT simnumber,installationdate,expiredate,mdvr_terminal_no,today_km,internal_battery_voltage,vehicle_sleep,deviceimei,angle,vehicletype as vehicle_type,angle,vehicleid,vehiclename,lat,lng,speed,updatedon,TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,lat,lng,acc_on,acc_flag,acc_date_time,ac_flag,ac_date,ac_km,odometer as kilometer,fuel_ltr,fuel_tank_capacity,temperature,TIME_FORMAT(TIMEDIFF(NOW(),last_ign_off), '%H:%i:%s') as last_dur, TIME_FORMAT(TIMEDIFF(NOW(),updatedon), '%H:%i:%s') as no_last_dur,device_config_type,device_type,hub_ETA,round(litres*keyword) as DTE FROM vehicletbl WHERE status='1' AND visible_status=1 AND TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) <= '10' AND acc_on ='1' and round(speed) >= '1' AND client_id='" . $client_id . "'");


        if ($query->num_rows() > 0) {

            return $query->result();
        } else {
            //return false;
            return array();
        }
    }
    public function singlevehicle_map($client_id, $trip_id, $vehicle_id)
    {
        $fromdate = date('Y-m-d H:i:s', strtotime('00:00:00'));
        $todate = date('Y-m-d H:i:s', strtotime('23:59:59'));
        $query = $this->db->query("SELECT v.simnumber,v.today_km,v.internal_battery_voltage,v.vehicle_sleep,
            v.deviceimei,v.angle,v.vehicletype as vehicle_type,
            v.angle,v.vehicleid,v.vehiclename,v.lat,v.lng,v.speed,
			v.latlon_address,
            v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,TIME_FORMAT(TIMEDIFF(NOW(),v.last_ign_off), '%H hours:%i minutes') as last_dur, 
            TIME_FORMAT(TIMEDIFF(NOW(),v.updatedon), '%H:%i:%s') as no_last_dur,
            v.acc_on,v.acc_flag,v.acc_date_time,
            v.ac_flag,v.ac_date,v.ac_km,v.odometer as kilometer,
            v.device_config_type,v.device_type,ao.create_datetime,ao.updated_datetime
            FROM vehicletbl v 
            INNER JOIN zigma_plantrip_report1 ao ON ao.vehicle_id = v.vehicleid 
            WHERE ao.trip_id='" . $trip_id . "'
            AND  ao.client_id='" . $client_id . "' 
            AND  ao.vehicle_id='" . $vehicle_id . "'
            AND  v.status='1'           
             ");
        if ($query->num_rows() > 0) {

            return $query->row();
        } else {
            return array();
        }
    }
    public function singlevehicle_playback($client_id, $deviceimei, $todate, $fromdate)
    {
        $play_table = "play_back_history_" . $client_id;
        $qry = $this->db->query("SHOW TABLES LIKE '" . $play_table . "'");

        if ($qry->num_rows() > 0) {
            $query = $this->db->query("SELECT odometer,modified_date,lat_message,lon_message FROM play_back_history
            WHERE running_no ='" . $deviceimei . "' AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND 
            lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND DATE_FORMAT(modified_date, '%Y-%m-%d %H:%i:%s') 
            BETWEEN '" . $fromdate . "' AND '" . $todate . "'  UNION SELECT odometer,modified_date,lat_message,lon_message FROM " . $play_table . " WHERE running_no ='" . $deviceimei . "' AND
             lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND 
             lon_message!='0.0' AND DATE_FORMAT(modified_date, '%Y-%m-%d %H:%i:%s') BETWEEN '" . $fromdate . "' AND '" . $todate . "'  
             ORDER BY modified_date ASC");
        } else {
            $query = $this->db->query("SELECT odometer,running_no,lat_message,lon_message,speed,acc_status,door_status,modified_date as datetime FROM play_back_history WHERE running_no ='" . $deviceimei . "' AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND DATE_FORMAT(modified_date, '%Y-%m-%d %H:%i') BETWEEN '" . $fromdate . "' AND '" . $todate . "'  ORDER BY datetime ASC");
        }



        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }


    public function assign_locationlist($client_id, $userid, $Location_Id)
    {

        $query = $this->db->query("SELECT geo.id as assigned_id,ll.Location_short_name,ll.Location_Id,v.vehiclename,v.vehicleid FROM 
        assign_geo_fenceing geo 
        INNER JOIN vehicletbl v ON v.vehicleid = geo.vehicle_id INNER JOIN location_list ll ON  ll.Location_Id = geo.geo_location_id 
        WHERE geo.client_id =$client_id AND ll.CreatedBy=$userid AND geo.geo_location_id=$Location_Id");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }


    public function speed_distance_data($start_date, $end_date, $deviceimei, $client_id)
    {

        $playtable = "play_back_history_" . $client_id;

        $qry = $this->db->query("SHOW TABLES LIKE '" . $playtable . "'");

        if ($qry->num_rows() > 0) {

            $query1 = $this->db->query("SELECT speed,odometer,modified_date FROM play_back_history
                     WHERE running_no =$deviceimei AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND 
                     lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND modified_date
                     BETWEEN '" . $start_date . "' AND '" . $end_date . "'  UNION SELECT speed,odometer,modified_date FROM $playtable WHERE running_no =$deviceimei AND
                      lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND 
                      lon_message!='0.0' AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'  
                      ORDER BY modified_date ASC");
        } else {
            $query1 = $this->db->query("SELECT speed,odometer,modified_date FROM play_back_history WHERE running_no =$deviceimei AND 
                        lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND
                         lon_message!='0.0' AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY modified_date ASC");
        }

        if ($query1) {
            if ($query1->num_rows() > 0) {
                return $query1->result();
            }
        } else {
            return array();
        }
    }




    public function Fuel_report_list($from, $to, $vehicleid)
    {

        if (!empty($vehicleid)) {

            $query1 = $this->db->query("SELECT fs.litres,fs.modified_date as modified_date  FROM fuel_status fs WHERE fs.running_no = $vehicleid AND fs.flag='0' AND fs.lat!='000000000' AND fs.lng!='000000000' AND (fs.modified_date >= '" . $from . "' AND fs.modified_date <= '" . $to . "') ORDER BY modified_date ASC");

            if ($query1->num_rows() > 0) {

                return $query1->result();
            } else {
                return array();
            }
        } else if ($vehicleid == NULL) {

            return array();
        }
    }


    public function Fuel_smooth_data($from, $to, $vehicleid)
    {

        if (!empty($vehicleid)) {

            $query1 = $this->db->query("SELECT litres,modified_date FROM fueldata_smooth WHERE running_no =$vehicleid AND flag='0' AND lat!='000000000' AND lng!='000000000' AND (modified_date >= '" . $from . "' AND modified_date <= '" . $to . "') ORDER BY modified_date ASC");

            if ($query1->num_rows() > 0) {

                return $query1->result();
            } else {
                return array();
            }
        } else if ($vehicleid == NULL) {

            return array();
        }
    }

    public function engine_rpm_data($from, $to, $vehicleid)
    {

        if (!empty($vehicleid)) {

            $query1 = $this->db->query("SELECT value as rpmvalues,modified_date FROM engine_rpms WHERE deviceimei =354007185 AND (modified_date >=
            '2022-05-01 00:00:00' AND modified_date <= '2022-05-02 00:00:00' ) ORDER BY modified_date ASC");

            if ($query1->num_rows() > 0) {

                return $query1->result();
            } else {
                return array();
            }
        } else if ($vehicleid == NULL) {

            return array();
        }
    }


    public function temperature_value($from, $to, $vehicleid)
    {

        if (!empty($vehicleid)) {

            $query1 = $this->db->query("SELECT round(temp_status1/100,2) as tempvalue,modified_date FROM temperature_status WHERE imei =$vehicleid AND (modified_date >=
            '" . $from . "' AND modified_date <= '" . $to . "' ) ORDER BY modified_date ASC");

            if ($query1->num_rows() > 0) {

                return $query1->result();
            } else {
                return array();
            }
        } else if ($vehicleid == NULL) {

            return array();
        }
    }


    public function notification_alert($client_id)
    {
        $query = $this->db->query("SELECT v.vehiclename,(CASE WHEN a.alert_type_id = '18' OR a.alert_type_id = '27' OR a.alert_type_id = '6'  THEN '1' WHEN a.alert_type_id = '3' THEN '2' WHEN a.alert_type_id= '4' THEN '3' ELSE '4' END) as alert_toneid,
        a.alert_type as alert_name,s.createdon as datetime,s.alert_location as location,s.lat,s.lng
        FROM sms_alert s
        LEFT JOIN vehicletbl v ON s.vehicle_id = v.vehicleid INNER JOIN alert_type a ON a.alert_type_id = s.all_status
        WHERE s.client_id =$client_id ORDER BY s.sms_alert_id DESC LIMIT 15");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }


    public function notassign_vehicle($client_id, $user_id, $role)
    {

        if ($role == 6) {

            $query = $this->db->query("SELECT v.vehiclename,v.latlon_address,v.odometer,
      v.angle,v.acc_on,v.speed, v.deviceimei,v.lat,v.lng,
       v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
   TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H:%i:%s') as no_last_dur,  v.vehicleid,v.vehicletype
   FROM assign_owner ao INNER JOIN vehicletbl v ON v.vehicleid = ao.vehicle_id WHERE v.vehicleid NOT IN (SELECT vehicleid FROM zigma_plantrip 
       WHERE (status=1 OR status=2) AND client_id=$client_id GROUP BY vehicleid) AND
        v.client_id=$client_id AND ao.owner_id=$user_id AND TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) <=10 AND v.speed >  3 AND v.acc_on=1 GROUP BY v.vehicleid");


            // $query = $this->db->query("SELECT vehicleid,vehiclename FROM  assign_owner ao INNER JOIN vehicletbl v ON v.vehicleid = ao.vehicle_id WHERE ao.owner_id='".$user_id."' AND v.client_id='".$client_id."' ");


        } else {

            $query = $this->db->query("SELECT vehiclename,latlon_address,odometer,
            angle,acc_on,speed, deviceimei,lat,lng,
             updatedon,TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,
         TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,updatedon), '%H:%i:%s') as no_last_dur,vehicleid
          FROM vehicletbl WHERE vehicleid
             NOT IN 
             (SELECT vehicleid FROM zigma_plantrip WHERE (status=1 OR status=2) AND client_id=$client_id GROUP BY vehicleid)
              AND client_id=$client_id AND TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) <=10 AND speed >  3 AND acc_on=1 GROUP BY vehicleid");
        }

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    public function ign_onoff_list($from, $to, $vehicleid = NULL, $time)
    {

        if (!empty($vehicleid)) {

            $query1 = $this->db->query("SELECT ip.start_day,ip.end_day,(ip.end_odometer - ip.start_odometer) as distance,
                        ip.s_lng,ip.s_lat,ip.e_lng,ip.e_lat,ip.device_no,v.vehiclename
                        FROM trip_report ip 
                        LEFT JOIN vehicletbl v ON ip.vehicle_id = v.vehicleid
                        WHERE ip.device_no='" . $vehicleid . "' 
                        AND ip.end_day !='' 
                        AND ip.flag='2' 
                        AND TIMESTAMPDIFF(MINUTE,ip.start_day,ip.end_day)>'" . $time . "' 
                        AND  ip.start_day >= '" . $from . "' 
                        AND ip.start_day <= '" . $to . "' 
                        GROUP BY ip.end_day 
                        ORDER BY ip.start_day DESC");

            if ($query1->num_rows() > 0) {
                //$result=$query->result_array();
                return $query1->result();
            } else {
                return FALSE;
            }
        } else if ($vehicleid == NULL) {

            return FALSE;
        }
    }


    public function calculate_distance($imei, $start_date, $end_date, $client_id)
    {


        $playtable = "play_back_history_" . $client_id;

        $qry = $this->db->query("SHOW TABLES LIKE '" . $playtable . "'");

        if ($qry->num_rows() > 0) {

            $query1 = $this->db->query("SELECT odometer,modified_date FROM play_back_history
                     WHERE running_no =$imei AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND 
                     lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND modified_date
                     BETWEEN '" . $start_date . "' AND '" . $end_date . "'  UNION SELECT odometer,modified_date FROM " . $playtable . " WHERE running_no =$imei AND
                      lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND 
                      lon_message!='0.0' AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'  
                      ORDER BY modified_date DESC");
        } else {
            $query1 = $this->db->query("SELECT odometer,modified_date FROM play_back_history WHERE running_no =$imei AND lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY modified_date DESC");
        }

        if ($query1) {
            if ($query1->num_rows() > 0) {
                $result = $query1->result();

                $n = count($result) - 1;


                $dist_km = round(($result[0]->odometer - $result[$n]->odometer), 3);


                $Arr = array(
                    'distance_km' => $dist_km
                );
                return  $Obj = (object)$Arr;
            }
        } else {
            return false;
        }
    }

    public function percentage_detail($client_id, $deviceimei)
    {

        $query = $this->db->query("SELECT CONCAT(v.car_battery, ' ', '%') AS  percent FROM vehicletbl v 
         INNER JOIN devicetbl d ON d.deviceimei=v.deviceimei  WHERE d.device_model = 'Assert_Tracker' AND v.deviceimei=$deviceimei AND v.client_id=$client_id
         GROUP BY v.vehicleid");

        if ($query->num_rows() > 0) {
            $data =  $query->row();
            return $data->percent;
        } else {
            return 0;
        }
    }


    public function notassign_vehicles($client_id, $location_id)
    {

        $query = $this->db->query("SELECT v.vehicleid,v.vehiclename FROM vehicletbl_2 v WHERE v.vehicleid NOT IN (select vehicle_id 
         FROM assign_geo_fenceing
          WHERE client_id=$client_id AND geo_location_id=$location_id) AND v.client_id=$client_id AND v.status=1");

        if ($query->num_rows() > 0) {
            return  $query->result();
        } else {
            return 0;
        }
    }

    public function vehicle_routelist($client_id)
    {
        $query = $this->db->query("select route_id,routename AS route_name from trip_routes where client_id=$client_id");
        if ($query) {
            return $query->result();
        } else {
            return array();
        }
    }


    //==========================Vehicle management ===========================
    public function vehicleServiceType()
    {
        $query = $this->db->query("select * from expense_type where status = 1");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function paymentType()
    {
        $query = $this->db->query("SELECT * FROM ref_paymentmode WHERE status = 1");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }
    public function vehicle_service_list($client_id)
    {
        $query = $this->db->query("SELECT vs.*,(CASE WHEN so.status = '1' THEN '2' WHEN vt.odometer > vs.reminder_km THEN '1'  ELSE '0' END) AS odo_reminder_flag, 
        (CASE WHEN so.status = '1' THEN '2' WHEN vs.reminder_date > CURDATE() THEN '1' ELSE '0' END) AS date_reminder_flag
        FROM vehicle_service vs
        inner join vehicletbl vt ON vt.vehicleid = vs.vehicle_id
        LEFT JOIN service_overdue so ON vs.service_id = so.service_id
        WHERE vs.client_id = $client_id ORDER BY odo_reminder_flag =1 || date_reminder_flag = 1  DESC");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
       
    }
    public function vehicle_service_edit($service_id)
    {
        $query = $this->db->query("SELECT * FROM vehicle_service WHERE service_id = $service_id");
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return array();
        }
    }

    //    ================================
    public function vehicle_document_list($client_id)
    {
        $query = $this->db->query("SELECT * FROM insurance_reminder WHERE client_id = $client_id");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }
    public function vehicle_document_edit($vehicle_id)
    {
        $query = $this->db->query("SELECT * FROM insurance_reminder WHERE vehicle_id ='$vehicle_id'");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    // ============================================================

    public function vehicle_fuel_list($client_id)
    {
        $query = $this->db->query("SELECT * FROM fuel_management WHERE client_id = $client_id");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function vehicle_fuel_edit($fuel_management_id)
    {
        $query = $this->db->query("SELECT * FROM fuel_management WHERE fuel_management_id = $fuel_management_id");
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return array();
        }
    }
    public function vehicle_total_list($client_id)
    {
        $query = $this->db->query("SELECT vehiclename,deviceimei,simnumber,installationdate,expiredate,due_amount,'TWINGS-104' as devicemodel FROM vehicletbl WHERE status=1 AND client_id = $client_id");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }
    public function vehicle_expired_list($client_id)
    {
        $query = $this->db->query("SELECT vehiclename,deviceimei,simnumber,vehiclemodel,installationdate,expiredate,due_amount,CURDATE() AS currentdate FROM vehicletbl WHERE status=1 AND client_id = $client_id AND expiredate < CURDATE()");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function allvehicle_expense($client_id)
    {
        $query = $this->db->query("select vs.service_id,vs.client_id,vs.vehicle_id,vs.service_type,vs.purchase_amount,vs.purchase_date,vs.purchase_product,
        vt.vehiclename,vt.vehiclemodel,vt.deviceimei,et.expense_name
        FROM vehicle_service vs
        inner join vehicletbl vt ON vt.vehicleid = vs.vehicle_id
        inner join expense_type et ON et.id = vs.service_type
        where vs.client_id=$client_id      
       order by  vs.purchase_date desc");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function vehicleWise_expense($client_id, $vehicle_id, $fromDate, $endDate)
    {
        $query = $this->db->query("select vs.service_id,vs.client_id,vs.vehicle_id,vs.service_type,vs.purchase_amount,vs.purchase_date,vs.purchase_product,
        vt.vehiclename,vt.vehiclemodel,vt.deviceimei,et.expense_name
        FROM vehicle_service vs
        inner join vehicletbl vt ON vt.vehicleid = vs.vehicle_id
        inner join expense_type et ON et.id = vs.service_type
        where vs.client_id=$client_id AND 
        vs.vehicle_id = $vehicle_id AND
         vs.purchase_date BETWEEN '" . $fromDate . "' AND '" . $endDate . "'
       order by  vs.purchase_date desc");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }
    public function vehicle_service_overdue($client_id)
    {
        $query = $this->db->query("select vs.service_id,vs.client_id,vs.vehicle_id,vs.service_type,vs.purchase_amount,
        vs.purchase_date,vs.purchase_product,vs.reminder_km,vs.reminder_date,
        vt.vehiclename,vt.vehiclemodel,vt.deviceimei,et.expense_name
        FROM vehicle_service vs
        inner join vehicletbl vt ON vt.vehicleid = vs.vehicle_id
        inner join expense_type et ON et.id = vs.service_type
        where vs.client_id=$client_id 
        AND vs.reminder_date < CURDATE()
        order by vs.purchase_date desc");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }


    public function history_alldata($vehicle, $from_date, $to_date, $client_id, $userid, $role)
    {

        if ($vehicle == 0) {
            if ($role == 6) {
                $query =  $this->db->query("SELECT 
            (SELECT SUM(DISTINCT d.`distance_km`) FROM consolidate_distance_report d INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as distance,
            (SELECT SUM(DISTINCT d.`moving_duration`) FROM consolidate_ign_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as moving_duration,
            (SELECT SUM(DISTINCT d.`idel_duration`) FROM consolidate_idle_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid ) as idle_duration,
            (SELECT SUM(DISTINCT d.`parking_duration`) FROM consolidate_park_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as parking_duration,
            (SELECT SUM(DISTINCT d.`running_duration`) FROM consolidate_ac_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as ac_duration,
            (SELECT SUM(DISTINCT d.`fuel_consumed_litre`) FROM consolidate_fuelcosumed_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as fuel_consumed_litre,
            (SELECT SUM(DISTINCT d.`fuel_millege`) FROM consolidate_fuelcosumed_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as fuel_milege,
            (SELECT SUM(DISTINCT d.`fuel_fill_litre`) FROM consolidate_fuelfill_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as fuel_fill_litre,
            (SELECT SUM(DISTINCT d.`fuel_dip_litre`) FROM consolidate_fueldip_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicleid 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as fuel_dip_litre,
            (SELECT SUM(DISTINCT d.normal_rpm+ d.under_load+ d.idle_rpm) FROM consolidate_rpm_report d  INNER JOIN assign_owner a ON a.vehicle_id = d.vehicle_id 
            WHERE d.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND d.client_id=$client_id AND a.owner_id=$userid) as totalrpm
            ");
            } else {
                //echo"hii";die;
                $query =  $this->db->query("SELECT (SELECT SUM(DISTINCT distance_km) FROM consolidate_distance_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as distance,
            (SELECT SUM(DISTINCT moving_duration) FROM consolidate_ign_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as moving_duration,
            (SELECT SUM(DISTINCT idel_duration) FROM consolidate_idle_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as idle_duration,
            (SELECT SUM(DISTINCT parking_duration) FROM consolidate_park_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as parking_duration,
            (SELECT SUM(DISTINCT running_duration) FROM consolidate_ac_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as ac_duration,
            (SELECT SUM(DISTINCT fuel_consumed_litre) FROM consolidate_fuelcosumed_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as fuel_consumed_litre,
            (SELECT SUM(DISTINCT fuel_millege) FROM consolidate_fuelcosumed_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as fuel_milege,
            (SELECT SUM(DISTINCT fuel_fill_litre) FROM consolidate_fuelfill_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as fuel_fill_litre,
            (SELECT SUM(DISTINCT fuel_dip_litre) FROM consolidate_fueldip_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as fuel_dip_litre,
            (SELECT SUM(DISTINCT normal_rpm+under_load+idle_rpm) FROM consolidate_rpm_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  ) as totalrpm
            ");
            }
        } else {

            $query =  $this->db->query("SELECT (SELECT SUM(DISTINCT distance_km) FROM consolidate_distance_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id  AND imei=$vehicle) as distance,
        (SELECT SUM(DISTINCT moving_duration) FROM consolidate_ign_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as moving_duration,
        (SELECT SUM(DISTINCT idel_duration) FROM consolidate_idle_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as idle_duration,
        (SELECT SUM(DISTINCT parking_duration) FROM consolidate_park_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as parking_duration,
        (SELECT SUM(DISTINCT running_duration) FROM consolidate_ac_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as ac_duration,
        (SELECT SUM(DISTINCT fuel_consumed_litre) FROM consolidate_fuelcosumed_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as fuel_consumed_litre,
        (SELECT SUM(DISTINCT fuel_millege) FROM consolidate_fuelcosumed_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as fuel_milege,
        (SELECT SUM(DISTINCT fuel_fill_litre) FROM consolidate_fuelfill_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as fuel_fill_litre,
        (SELECT SUM(DISTINCT fuel_dip_litre) FROM consolidate_fueldip_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND imei=$vehicle ) as fuel_dip_litre,
        (SELECT SUM(DISTINCT normal_rpm+under_load+idle_rpm) FROM consolidate_rpm_report WHERE date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND deviceimei=$vehicle ) as totalrpm,
        (SELECT AVG(speed) FROM play_back_history WHERE created_date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND running_no=$vehicle ) as average_speed,
        (SELECT MAX(speed) FROM play_back_history WHERE created_date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND client_id=$client_id AND running_no=$vehicle ) as maximum_speed
        ");
            //print_r($query);die;
        }

        if ($query) {
            if ($query->num_rows() > 0) {
                //echo"hi";die;
                return $query->row();
            }
        } else {
            return array();
        }
    }

    public function consolidate_playback_avg_max($imei, $start_date, $end_date, $client_id)
    {

        $playtable = "play_back_history_" . $client_id;

        $qry = $this->db->query("SHOW TABLES LIKE '" . $playtable . "'");

        if ($qry->num_rows() > 0) {

            $query1 = $this->db->query("SELECT MAX(speed) as max_speed,ROUND(AVG(speed)) as avg_speed FROM $playtable WHERE running_no =$imei AND
                      lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND 
                      lon_message!='0.0' AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'  ");
        } else {
            $query1 = $this->db->query("SELECT MAX(speed) as max_speed,ROUND(AVG(speed)) as avg_speed FROM play_back_history WHERE running_no =$imei AND 
            lat_message!='000000000' AND lon_message!='000000000' AND lat_message!='0' AND lon_message!='0' AND lat_message!='0.0' AND lon_message!='0.0' 
            AND modified_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY modified_date DESC");
        }

        if ($query1) {
            if ($query1->num_rows() > 0) {
               return $query1->row();
            }
        } else {
            return false;
        }
    }
    public function contact_details($client_id,$userid,$roleid,$dealer_id)
    {
        if(empty($dealer_id) || $dealer_id=='')
        $query = $this->db->query("SELECT company_name,contact_person,company_email,primary_mobile,secondary_mobile,company_address FROM settings");
        else
        $query = $this->db->query("SELECT dealer_company as company_name,dealer_name as contact_person,dealer_email as company_email,
        dealer_mobile as primary_mobile,'' as secondary_mobile,dealer_address as company_address  FROM dealertbl WHERE dealer_id=$dealer_id");
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    public function profile_details($client_id,$userid,$roleid)
    {

        $query = $this->db->query("SELECT username,firstname as customer_name,email,mobilenumber,postaladdres as address,pincode FROM usertbl WHERE userid=$userid");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }


    public function checkotp_driver($mobile_number)
  {

    $query = $this->db->query("SELECT do.*,zp.* FROM zigma_plantrip zp
    INNER JOIN driver_otp do ON do.driver_mobile = zp.drivermobile
    Where zp.drivermobile ='$mobile_number' AND do.driver_mobile ='$mobile_number'");

    if ($query) {
      if ($query->num_rows() > 0) {
  
        return $query->row();
      }
    } else {
      return array();
    }
  }

  public function verify_otp($mobile_number,$otp)
  {
    $query = $this->db->query("SELECT do.*,z.trip_id,z.vehicleid,z.client_id,v.dealer_id,z.poc_number,z.status,ll.Location_short_name AS startlocation,ll2.Location_short_name AS endlocation FROM driver_otp do
    INNER JOIN zigma_plantrip z ON z.drivermobile = do.driver_mobile
    INNER JOIN location_list ll ON ll.Location_Id = z.start_location
    INNER JOIN location_list ll2 ON ll2.Location_Id = z.end_location
    INNER JOIN vehicletbl v ON v.vehicleid = z.vehicleid
    Where do.driver_mobile=$mobile_number AND do.otp=$otp");

    if ($query) {
      if ($query->num_rows() > 0) {
        return $query->row();
      }
    } else {
      return array();
    }
  }

  public function list_trip($mobile_number)
  {
    $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
    v.vehiclename,v.angle,v.speed,v.vehicletype,v.simnumber,
    v.deviceimei as deviceimei,v.vehicleid,v.lat,v.lng,
    v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
    TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H hours:%i minutes') as last_dur, 
  TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H:%i:%s') as no_last_dur,
  v.acc_on,v.acc_flag,v.acc_date_time,
  v.ac_flag,v.ac_date,
            v.device_config_type,v.device_type,
    ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
    ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
        round(zpr.end_odometer-zpr.start_odometer) as act_km,
        if (TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s') > 0, TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s'), 0) as act_duration,
  zpr.create_datetime as act_starttime,zpr.updated_datetime as act_endtime,zpr.id as ReptripID
  FROM zigma_plantrip p
  INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid
  INNER JOIN location_list ll ON ll.Location_Id = p.start_location
  INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location
  LEFT JOIN zigma_plantrip_report1 zpr ON zpr.trip_id = p.trip_id AND zpr.client_id=p.client_id
    Where p.drivermobile=$mobile_number");
    // print_r($query);die;
    if ($query) {
      if ($query->num_rows() > 0) {
        return $query->result();
      }
    } else {
      return array();
    }

  }

  public function zigma_addtriplist($status,$mobile_number)
  {
    // $client_id = $this->session->userdata['client_id']; 
    if($status==0)
    {
      $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
      v.vehiclename,v.angle,v.speed,v.vehicletype,v.simnumber,
      v.deviceimei as deviceimei,v.vehicleid,v.lat,v.lng,
      v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
      TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H hours:%i minutes') as last_dur, 
    TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H:%i:%s') as no_last_dur,
    v.acc_on,v.acc_flag,v.acc_date_time,
    v.ac_flag,v.ac_date,
              v.device_config_type,v.device_type,
      ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
      ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
          round(zpr.end_odometer-zpr.start_odometer) as act_km,
          if (TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s') > 0, TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s'), 0) as act_duration,
    zpr.create_datetime as act_starttime,zpr.updated_datetime as act_endtime,zpr.id as ReptripID
    FROM zigma_plantrip p
    INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid
    INNER JOIN location_list ll ON ll.Location_Id = p.start_location
    INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location
    LEFT JOIN zigma_plantrip_report1 zpr ON zpr.trip_id = p.trip_id AND zpr.client_id=p.client_id
    Where p.drivermobile=$mobile_number");
    }
        
    else
    {
      $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
  v.vehiclename,v.angle,v.speed,v.vehicletype,v.simnumber,
      v.deviceimei as deviceimei,v.vehicleid,v.lat,v.lng,
  v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
  TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H hours:%i minutes') as last_dur, 
TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H:%i:%s') as no_last_dur,
v.acc_on,v.acc_flag,v.acc_date_time,
v.ac_flag,v.ac_date,
          v.device_config_type,v.device_type,
  ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
  ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
      round(zpr.end_odometer-zpr.start_odometer) as act_km,
      if (TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s') > 0, TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s'), 0) as act_duration,
zpr.create_datetime as act_starttime,zpr.updated_datetime as act_endtime,zpr.id as ReptripID
FROM zigma_plantrip p
INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid
INNER JOIN location_list ll ON ll.Location_Id = p.start_location
INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location
LEFT JOIN zigma_plantrip_report1 zpr ON zpr.trip_id = p.trip_id AND zpr.client_id=p.client_id
WHERE p.drivermobile=$mobile_number AND p.status=$status");
  
    }
    
    
    if ($query->num_rows() > 0) 
    {
      return $query->result();
    }
    else 
    {
      return FALSE;
    }
  }

  public function zigma_addtriplist_stat($status,$mobile_number)
  {
  
      $query = $this->db->query("SELECT p.*,v.latlon_address,odometer,
  v.vehiclename,v.angle,v.speed,v.vehicletype,v.simnumber,
  v.deviceimei,v.vehicleid,v.lat,v.lng,
  v.updatedon,TIMESTAMPDIFF(MINUTE,v.updatedon,CURRENT_TIMESTAMP) AS update_time,
  TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H hours:%i minutes') as last_dur, 
TIME_FORMAT(TIMEDIFF(CURRENT_TIMESTAMP,v.updatedon), '%H:%i:%s') as no_last_dur,
v.acc_on,v.acc_flag,v.acc_date_time,
v.ac_flag,v.ac_date,v.device_config_type,v.device_type,
  ll.Location_short_name AS startlocation,ll.Lat AS startlat,ll.Lng AS startlang,
  ll2.Location_short_name AS endlocation,ll2.Lat AS endlat,ll2.Lng AS endlang,'0 mins' AS ETA,
      round(zpr.end_odometer-zpr.start_odometer) as act_km,
      if (TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s') > 0, TIME_FORMAT(TIMEDIFF(zpr.updated_datetime,zpr.create_datetime), '%H:%i:%s'), 0) as act_duration,
zpr.create_datetime as act_starttime,zpr.updated_datetime as act_endtime,zpr.id as ReptripID
FROM zigma_plantrip p
INNER JOIN vehicletbl v ON v.vehicleid = p.vehicleid
INNER JOIN location_list ll ON ll.Location_Id = p.start_location
INNER JOIN location_list ll2 ON ll2.Location_Id = p.end_location
LEFT JOIN zigma_plantrip_report1 zpr ON zpr.trip_id = p.trip_id AND zpr.client_id=p.client_id
WHERE p.drivermobile=$mobile_number AND p.status=$status");
  
    if ($query->num_rows() > 0) 
    {
      return $query->result();
    }
    else 
    {
      return FALSE;
    }
  }

  public function last_zigmatrip()
  {
    // $client_id = $this->session->userdata['client_id']; 
    
    $query = $this->db->query("SELECT trip_id FROM zigma_plantrip ORDER BY trip_id DESC LIMIT 1");
    
    if ($query->num_rows() > 0) 
    {
      return $query->row();
    }
    else 
    {
      return FALSE;
    }
  }

  public function check_driver($mobile_number)
  {
    $query = $this->db->query("SELECT * FROM driver_otp Where ");

    if ($query) {
      if ($query->num_rows() > 0) {
        return $query->row();
      }
    } else {
      return array();
    }
  }

  public function edit_drivertrip_expenses($id)
  {
  
    $query = $this->db->query("SELECT * FROM driver_expenses Where id='$id'");

    if ($query) {
      if ($query->num_rows() > 0) {
        return $query->row();
      }
    } else {
      return array();
    }
  }

  public function drivertrip_list($trip_id)
  {
    $query = $this->db->query("SELECT * FROM driver_expenses WHERE trip_id='$trip_id'");
    if ($query->num_rows() > 0) {
      return $query->result();
    } else {
      return array();
    }
  }
  
  public function check_excist_trip($client_id)
  {

      $query = $this->db->query("SELECT client_id FROM trip_route_settings WHERE status=1 AND client_id=$client_id");
      if ($query->num_rows() > 0) {
          return $query->row();
      } else {
          return false;
      }
  }
  public function check_vehicle($vehiclename,$client_id)
  {

      $query = $this->db->query("SELECT vehicleid,vehiclename FROM vehicletbl_2 WHERE status=1 AND vehiclename='$vehiclename' AND client_id=$client_id");
      if ($query->num_rows() > 0) {
          return $query->row();
      } else {
          return false;
      }
  }
  public function cron_route_details($route_id)
  {

      $query = $this->db->query("SELECT * FROM trip_routes WHERE status=1 AND route_id='$route_id'");
      if ($query->num_rows() > 0) {
          return $query->row();
      } else {
          return false;
      }
  }
  
  public function check_oute_diviate($client_id)
  {

      $query = $this->db->query("SELECT deviationdistance FROM trip_route_settings WHERE status=1 AND client_id=$client_id");
      if ($query->num_rows() > 0) {
          $data =  $query->row();
          return $data->deviationdistance;
      } else {
          return false;
      }
  }

  public function route_deviation_alert($vehicle_id,$fromdate,$todate)
  {

      $query = $this->db->query("SELECT dateTime,address,latitude,vehicleNumber,deviationMagnitude,type,longitude,stoppage_status FROM api_sms_alert 
      WHERE vehicle_id=$vehicle_id AND dateTime BETWEEN  '$fromdate' AND '$todate'");
      if ($query->num_rows() > 0) {
          $data =  $query->result_array();
          return $data;
      } else {
          return false;
      }
  }

  
  public function stoppage_alerts($vehicle_id,$fromdate,$todate)
  {

      $query = $this->db->query("SELECT createdon as dateTime,alert_location as address,lat as latitude,vehicle_number as vehicleNumber,'
      Stoppage' as type,lng as longitude,IF(all_status=33, 'VehicleStart/IgnitionOn', 'VehicleStop/IgnitionOff') as stoppage_status FROM 
      sms_alert WHERE vehicle_id=$vehicle_id AND createdon BETWEEN  '$fromdate' AND '$todate' ");
      if ($query->num_rows() > 0) {
          $data =  $query->result_array();
          return $data;
      } else {
          return array();
      }
  }

  public function check_trip($vehicleid)
  {
    $query = $this->db->query("SELECT status FROM zigma_plantrip Where vehicleid=$vehicleid ORDER BY id DESC LIMIT 1");

      if ($query->num_rows() > 0) {
        $data =  $query->row();
         return $data->status;
      }
    else {
      return 3;
    }
  }

  public function generator_details($deviceimei)
  {

      $query = $this->db->query("SELECT client_id,vehicleid as vehicle_id,updatedon,TIMESTAMPDIFF(MINUTE,updatedon,CURRENT_TIMESTAMP) AS update_time,
          ROUND(odometer,3) as odometer,round(speed) as speed,acc_on,ac_flag,lat,lng,angle,COALESCE(latlon_address,'') as latlon_address,
          today_km,COALESCE(ROUND(today_km,3),'0') as trip_kilometer,ROUND(car_battery,2) as batteryvolt,ROUND(litres,2) as fuel_ltr,
          COALESCE(driver_name,'N/A') as driver_name,last_ign_off,last_ign_on,mileage,IF(altitude >2, altitude, 'N/A') as altitude,
          IF(gsmsignal >0, gsmsignal, 'N/A') as gsm, IF(gpssignal >0, gpssignal, 'N/A') as gsm,
          COALESCE(ROUND((litres*keyword),1),'N/A') as distancetoEmpty,
          'N/A'  AS  secondary_engine,'75%'  AS  battery_precentage,'h:min' AS hourmeter,rpm_data,
           round(temperature/100,2) as temperature,'NULL' AS humidity,'N/A' AS drum,'N/A' AS bucket,device_type,safe_parking
           FROM vehicletbl where deviceimei=$deviceimei");

      if ($query->num_rows() > 0) {
          return $query->row();
      } else {
          return false;
      }
  }

  public function generator_last_ign($deviceimei)
  {
    $query1 = $this->db->query("SELECT TIME_FORMAT(TIMEDIFF(ip.end_day,ip.start_day), '%H:%i:%s') as time_duration,
    TIMESTAMPDIFF(MINUTE,ip.start_day,ip.end_day) as t_min,ip.start_day,ip.end_day,(ip.end_odometer - ip.start_odometer) as distance,
    ip.s_lng,ip.s_lat,ip.e_lng,ip.e_lat,ip.device_no,v.vehiclename,v.device_config_type,v.deviceimei,ip.end_odometer,ip.start_odometer,
    ip.start_location,ip.end_location,ip.report_id 
    FROM trip_report ip 
    LEFT JOIN vehicletbl v ON ip.vehicle_id = v.vehicleid
    WHERE ip.device_no='".$deviceimei."' AND flag='2' ORDER BY ip.start_day DESC LIMIT 1");
    if ($query1->num_rows() > 0) {
        //$result=$query->result_array();

        return $query1->row();
    } else {
        return array();
    }
  }


  public function qatar_all_vehicles()
  {

    $query1 = $this->db->query("SELECT * FROM vehicletbl WHERE dealer_id ='52'");
    if ($query1->num_rows() > 0) {

        return $query1->result();

    } else {

        return array();

    }
    
  }

  public function about_us_data($dealer_id)
  {
    $query = $this->db->query("SELECT content FROM about_us WHERE dealer_id='$dealer_id'");

    if ($query->num_rows() > 0) {
        return $query->row();
    } else {
        return false;
    }
  }

  public function qatar_all_clients()
  {
    
    $query1 = $this->db->query("SELECT * FROM usertbl WHERE dealer_id=52 AND roleid=5 ");

    if ($query1->num_rows() > 0) {

        return $query1->result();

    } else {

        return array();

    }

  }
  public function get_vehicle_client($deviceimei)
  {
    $query = $this->db->query("SELECT client_id,vehicletype FROM vehicletbl where deviceimei=$deviceimei");
    if($query->num_rows()>0)
    {
        return $query->row();
    }else{
        return array();
    }

  }

}
