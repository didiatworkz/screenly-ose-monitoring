<!--
                            _
   ____                    | |
  / __ \__      _____  _ __| | __ ____
 / / _` \ \ /\ / / _ \| '__| |/ /|_  /
| | (_| |\ V  V / (_) | |  |   <  / /
 \ \__,_| \_/\_/ \___/|_|  |_|\_\/___|
  \____/

		http://www.atworkz.de
		   info@atworkz.de
________________________________________
		  Screenly OSE Monitor
	   Version 2.0 - March 2019
________________________________________
-->
<?php
	if (!@file_exists('dbase.db')) die('Database does not exist! Please check if the file "dbase.db" exists.');
	ini_set('display_errors',0);
	error_reporting(E_ALL|E_STRICT);

	$db 			= new SQLite3("dbase.db");
	$set 			= $db->query("SELECT * FROM settings WHERE userID = 1");
	$set 			= $set->fetchArray(SQLITE3_ASSOC);
	$loginUsername 	= $set['username'];
	$loginPassword 	= $set['password'];
	$loginUserID 	= $set['userID'];
	$systemVersion  = file_get_contents('assets/tools/version.txt');

	if(isset($_GET['site'])){
		$site = $_GET['site'];
	} else $site = NULL;

	function redirect($url, $time = 1){
		echo'<meta http-equiv="refresh" content="'.$time.';URL='.$url.'">';
	}

	function sysinfo($status, $message, $refresh = false){
		echo'<script>$.toaster({ priority : \''.$status.'\', title : \'System\', message : \''.$message.'\'});</script>';
		if($refresh){
			echo'<meta http-equiv="refresh" content="2;URL=index.php">';
		}
	}

	function callURL($method, $ip, $params = false, $user = false, $pass = false, $ssl = false){
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
		);
		$curl = curl_init();
		if($ssl) $prefix = 'https://';
		else $prefix = 'http://';

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
				$url .= '?' . http_build_query($params);
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
		   return callURL($method, $ip, $params, $user, $pass, true);
		}
		elseif ($code == 401) {
			sysinfo('warning', 'Can not logged in to the player! - Wrong User or Password!');
			return 'authentication error '.$code;
		}	
		else return 'error '.$code;
	}

	function pingAddress($ip){
		$pingresult = exec("/bin/ping -c 1 $ip", $outcome, $status);
		if (0 == $status) return true;
		else return false;
	}

	function monitorScript($url){
		if(pingAddress($url)) {
			$monitor = callURL('GET', $url.':9020/monitor.txt');
			if($monitor == 1) return 'http://'.$url.':9020/screenshot.png';
			else return 'assets/img/online.png';
		}
		else return 'assets/img/offline.png';
	}

	function update($v){
		$github = 'http://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/master/assets/tools/version.txt'.time();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $github);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 500);
		$remoteVersion = curl_exec($ch);
		curl_close($ch);
		return version_compare($v, $remoteVersion, '<');
	}

?>
