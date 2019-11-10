<?php

	$playerCount = $db->query("SELECT COUNT(*) AS counter FROM player");
	$playerCount = $playerCount->fetchArray(SQLITE3_ASSOC);
	$playerCount = $playerCount['counter'];

	function playerAuthentication($value = null){
		global $dbase_file;
		$db = new SQLite3($dbase_file);
		if(filter_var($value, FILTER_VALIDATE_IP)){
			$playerSQL 	= $db->query("SELECT * FROM player WHERE address='".$value."'");
		}
		else if(is_numeric($value)){
			$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$value."'");
		}
		else return FALSE;
		$player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
		$player['player_user'] != '' ? $user = $player['player_user'] : $user = false;
		$player['player_password'] != '' ? $pass = $player['player_password'] : $pass = false;
		return array('username' => $user, 'password' => $pass);
	}

	function callURL($method, $ip, $params = false, $playerID = null, $ssl = false){
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
		);
		$curl = curl_init();
		if($ssl) $prefix = 'https://';
		else $prefix = 'http://';

		$playerAuth = playerAuthentication($playerID);
		if($playerAuth['username'] != '' AND $playerAuth['password'] !=''){
			$user = $playerAuth['username'];
			$pass = $playerAuth['password'];
		}
		else {
			$user = false;
			$pass = false;
		}
		if($user AND $pass) $url = $prefix.$user.':'.$pass.'@'.$ip;
		else $url = $prefix.$ip;

		switch($method){
			case 'GET':
				//$url .= '?' . http_build_query($params);
				break;
			case 'POST':
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
				break;
			case 'PUT':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
				break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				//$url .= '?' . http_build_query($params);
				break;
		}

		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 250);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($code == 200) {
			return $response = json_decode($response, true);
		}
		elseif ($code == 301) {
		   return callURL($method, $ip, $params, $playerID, true);
		}
		elseif ($code == 401) {
			sysinfo('warning', 'Can not logged in to the player! - Wrong Username or Password!');
			return 'authentication error '.$code;
		}
		else return 'error '.$code;
	}

	function checkAddress($ip){
		$ch = curl_init($ip);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 200);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if(($httpcode>=200 && $httpcode<300) || $httpcode==401) return true;
		else return false;
	}

	function monitorScript($url){
		if(checkAddress($url)) {
			$monitor = callURL('GET', $url.':9020/monitor.txt');
			if($monitor == 1) return 'http://'.$url.':9020/screen/screenshot.png';
			else return 'assets/img/online.png';
		}
		else return 'assets/img/offline.png';
	}
