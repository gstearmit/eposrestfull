<?php

//Get config URL and Query string.
include 'configs/config.php';

/**
 * Description of Footer
 *
 * @author hoaitn
 */
class Footer {

	private function __construct() {
		
	}

	/**
	 * Get database information
	 */
	public static function database_alive() {
		//Starting call curl.
		$ch = curl_init();
		//Set url to get database information
		$url = BASE_URL;
		$qry_str = SERVER_UPTIME_QUERY;
		/**
		 * Request data GET for database information
		 */
		curl_setopt($ch, CURLOPT_URL, $url . $qry_str);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$db = curl_exec($ch);
		// close curl 
		curl_close($ch);
		//explode data to using array.
		//$s_state = null;
		if ($db != null) {
			$db = json_encode($db);
			$db = str_replace('\n', ';', $db);
			/*$s_state = str_replace('?>', '', $s_state);*/
			$db = str_replace('"', '', $db);
			$db = @explode(';', $db);		
			$times = @round(($db[5]/86400), 2);			
			$times = @explode('.',$times);
			if(count($times) > 0){
				$times[1] = ($times[1]/100)*24;				
				$database_alive = $times[0] . ' days '. $times[1]. ' hours';			
			}else{
				$database_alive = $times . ' days ';
			}
		} else {
			$database_alive = '';
		}

		return $database_alive;
	}

	/**
	 * Get server information
	 */
	public static function server_alive() {
		//Starting call curl.
		$ch = curl_init();
		//Set url to get server information
		$url = BASE_URL;
		$qry_str = DATABASE_UPTIME_QUERY;
		curl_setopt($ch, CURLOPT_URL, $url . $qry_str);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server = curl_exec($ch);
		// close curl 
		curl_close($ch);
		//explode data to using array.
		if ($server != null) {
			$server = json_encode($server);
			$server = str_replace('\n', ';', $server);
			$server = str_replace('?>', '', $server);
			$server = str_replace('"', '', $server);
			$server = explode(';', $server);
			$times = @round(($server[5]/86400), 2);			
			$times = @explode('.',$times);
			if(count($times) > 0){
				$times[1] = ($times[1]/100)*24;				
				$sever_alive  = $times[0] . ' days '. $times[1]. ' hours';			
			}else{
				$sever_alive  = $times . ' days ';
			}			
		} else {
			$sever_alive = '';
		}

		return $sever_alive;
	}

	/**
	 * Get server state
	 */
	public static function server_state() {
		//Starting call curl.
		$ch = curl_init();
		//Set url to get server information
		$url = BASE_URL;
		$qry_str = SERVER_STATE_QUERY;
		curl_setopt($ch, CURLOPT_URL, $url . $qry_str);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$s_state = curl_exec($ch);
		// close curl 
		curl_close($ch);
		//explode data to using array.
		if ($s_state != null) {
			$s_state = json_encode($s_state);
			$s_state = str_replace('\n', '#@', $s_state);
			$s_state = str_replace('?>', '', $s_state);
			$s_state = str_replace('"', '', $s_state);
			$s_state = explode('#@', $s_state);
			if (strlen($s_state[0]) > 1) {
				$s_icon = '<img src="images/Circle_Green_16.png" style="float: left;">';
			} else {
				$s_icon = '<img src="images/Circle_Red_16.png" style="float: left;">';
			}
		} else {
			$s_icon = '';
		}
		return $s_icon;
	}

	/**
	 * Get server state
	 */
	public static function database_state() {
		//Starting call curl.
		$ch = curl_init();
		$url = BASE_URL;
		$qry_str = DATABASE_STATE_QUERY;
		curl_setopt($ch, CURLOPT_URL, $url . $qry_str);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$d_state = curl_exec($ch);
		// close curl 
		curl_close($ch);
		if ($d_state != null) {
			$d_state = json_encode($d_state);
			$d_state = str_replace('\n', '#@', $d_state);
			$s_state = str_replace('?>', '', $s_state);
			$d_state = str_replace('"', '', $d_state);
			$d_state = explode('#@', $d_state);
			if (strlen($d_state[0]) > 1) {
				$d_icon = '<img src="images/Circle_Green_16.png" style="float: left;">';
			} else {
				$d_icon = '<img src="images/Circle_Red_16.png" style="float: left;">';
			}
		} else {
			$d_icon = '';
		}
		return $d_icon;
	}

	/**
	 * Get server state
	 */
	public static function response_time() {
		//Starting call curl.
		$ch = curl_init();
		$url = BASE_URL;
		$qry_str = RESPONSE_TIME_QUERY;
		curl_setopt($ch, CURLOPT_URL, $url . $qry_str);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$res_time = curl_exec($ch);
		// close curl 
		curl_close($ch);
		if ($res_time != null) {
			$res_time = json_encode($res_time);
			$res_time = str_replace('\n', '#@', $res_time);
			$s_state = str_replace('?>', '', $s_state);
			$res_time = str_replace('"', '', $res_time);
			$res_time = explode('#@', $res_time);
			if (strlen($res_time[0]) > 1) {
				$r_icon = '<img src="images/Circle_Green_16.png" style="float: left;">';
			} else {
				$r_icon = '<img src="images/Circle_Red_16.png" style="float: left;">';
			}
		} else {
			$r_icon = '';
		}
		return $r_icon;
	}

	public static function backup_information($client, $start_date, $end_date) {
		//Starting call curl.
		$ch = curl_init();
		$url = BACKUP_URL;
		$qry_str = '?op=get&client=' . $client . '&period=' . $start_date . ';' . $end_date;
		curl_setopt($ch, CURLOPT_URL, $url . $qry_str);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$backup = curl_exec($ch);
		return $backup;
	}

}

?>
