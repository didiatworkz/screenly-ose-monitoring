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
	ini_set('display_errors',0);
	error_reporting(E_ALL|E_STRICT);

	$dbase_key		= 'assets/tools/key.txt';
	if(!@file_exists('dbase.db')) {
		$dbase_file = file_get_contents($dbase_key);
	} else $dbase_file = 'dbase.db';

	$db 			= new SQLite3($dbase_file);
	$set 			= $db->query("SELECT * FROM settings WHERE userID = 1");
	$set 			= $set->fetchArray(SQLITE3_ASSOC);
	$loginUsername 	= $set['username'];
	$loginPassword 	= $set['password'];
	$loginUserID 	= $set['userID'];
	$securityToken	= $set['token'];
	$systemVersion  = file_get_contents('assets/tools/version.txt');
	$apiVersion		= 'v1.2';
	
	if(!@file_exists($dbase_key)){
		$token = md5($systemVersion.time().$loginPassword).'.db';
		$current = file_get_contents($dbase_key);
		file_put_contents($dbase_key, $token);
		rename("dbase.db",$token);
	}
	
	if(@file_exists('assets/tools/version_old.txt')){
		$oldVersion = file_get_contents('assets/tools/version_old.txt');
		if($oldVersion <= '2.0'){			// Update Database to Version 2.0
			$db->exec("ALTER TABLE `settings` ADD COLUMN `token` TEXT");
			$db->exec("ALTER TABLE `settings` ADD COLUMN `end_date` INTEGER");
			$db->exec("ALTER TABLE `settings` ADD COLUMN `duration` INTEGER");
			$db->exec("UPDATE `settings` SET token='d1bf93299de1b68e6d382c893bf1215f' WHERE userID=1");
			$db->exec("UPDATE `settings` SET end_date=1 WHERE userID=1");
			$db->exec("UPDATE `settings` SET duration=30 WHERE userID=1");
		}
		unlink('assets/tools/version_old.txt');
	}
	
	if(isset($_GET['site'])){
		$site = $_GET['site'];
	} else $site = NULL;

	function redirect($url, $time = 1){
		echo'<meta http-equiv="refresh" content="'.$time.';URL='.$url.'">';
	}

	function sysinfo($status, $message, $refresh = false){
		echo'<script>$.notify({icon: "tim-icons icon-bell-55",message: "'.$message.'"},{type: "'.$status.'",timer: 1000,placement: {from: "top",align: "center"}});</script>';
		if($refresh) echo'<meta http-equiv="refresh" content="2;URL=index.php">';
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
			if($monitor == 1) return 'http://'.$url.':9020/screenshot.png';
			else return 'assets/img/online.png';
		}
		else return 'assets/img/offline.png';
	}

	function update(){
		$now=time();
		if($set['updatecheck']<$now && (date("d",$set['updatecheck'])!=date("d"))){
			shell_exec('ose-monitoring --scriptupdate');
			if(@file_exists('update.txt')){
				return true;
			} else return false;
			$db->exec("UPDATE settings SET updatetime='".time()."'");
		}
	}

	if(isset($_POST['changeAssetState'])){
		$id 		= $_POST['id'];
		$asset		= $_POST['asset'];
		$value 		= $_POST['value'];
		$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$id."'");
		$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);
		$player['player_user'] != '' ? $user = $player['player_user'] : $user = false;
		$player['player_password'] != '' ? $pass = $player['player_password'] : $pass = false;
		$data = callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, false, $user, $pass, false);
		if($data['is_enabled'] == 1 AND $data['is_active'] == 1){
			$data['is_enabled'] = "0";
			$data['is_active'] = "0";
		}
		else {
			$data['is_enabled'] = "1";
			$data['is_active'] = "1";
		}
		if(callURL('PUT', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, $data, $user, $pass, false)){
			header('HTTP/1.1 200 OK');
			exit();
		} else {
			header('HTTP/1.1 404 Not Found');
			exit();
		}
	}
?>
