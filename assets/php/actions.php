<?php
/*
                            _
   ____                    | |
  / __ \__      _____  _ __| | __ ____
 / / _` \ \ /\ / / _ \| '__| |/ /|_  /
| | (_| |\ V  V / (_) | |  |   <  / /
 \ \__,_| \_/\_/ \___/|_|  |_|\_\/___|
  \____/

        http://www.atworkz.de
           info@atworkz.de
_______________________________________

       Screenly OSE Monitoring
          Actions Functions
_______________________________________
*/

// Translation DONE

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

if(isset($_POST['newAsset'])){
	$id 	 			= isset($_POST['id']) ? $_POST['id'] : array();
	$now				= strtotime("-10 minutes");
	$id[] 			= isset($_POST['id']) ? $_POST['id'] : '';
	$url 				= isset($_POST['url']) ? $_POST['url'] : '';
	$name 			= isset($_POST['name']) ? $_POST['name'] : $_POST['url'];
	$mimetype		= $_POST['mimetype'];
	$start 			= isset($_POST['start_date']) ? $_POST['start_date'] : date("Y-m-d", $now);
	$start_time	= isset($_POST['start_time']) ? $_POST['start_time'] : '00:00';
	$end 				= isset($_POST['end_date']) ? $_POST['end_date'] : date("Y-m-d", strtotime("+".$set['end_date']." week"));
	$end_time		= isset($_POST['end_time']) ? $_POST['end_time'] : '00:00';
	$duration 	= isset($_POST['duration']) ? $_POST['duration'] : $set['duration'];
	$active 		= isset($_POST['active']) ? 1 : 0;


	if($name == '') $name = $url;

	if(isset($_POST['multidrop'])){
		$dzuuid							= isset($_POST['dzuuid']) ? $_POST['dzuuid'] : '';
		$dzchunkindex				= isset($_POST['dzchunkindex']) ? $_POST['dzchunkindex'] : '';
		$dztotalfilesize		= isset($_POST['dztotalfilesize']) ? $_POST['dztotalfilesize'] : '';
		$dzchunksize				= isset($_POST['dzchunksize']) ? $_POST['dzchunksize'] : '';
		$dztotalchunkcount 	= isset($_POST['dztotalchunkcount']) ? $_POST['dztotalchunkcount'] : '';
		$dzchunkbyteoffset	= isset($_POST['dzchunkbyteoffset']) ? $_POST['dzchunkbyteoffset'] : '';
		$image							= curl_file_create($_FILES['file']['tmp_name'],$_FILES['file']['type'],$_FILES['file']['name']);
		$data3								= array(
			'file_upload' 			=> $image,
			'dzuuid' 						=> $dzuuid,
			'dzchunkindex' 			=> $dzchunkindex,
			'dztotalfilesize' 	=> $dztotalfilesize,
			'dzchunksize' 			=> $dzchunksize,
			'dztotalchunkcount' => $dztotalchunkcount,
			'dzchunkbyteoffset' => $dzchunkbyteoffset
		);
		$ids 			= $_POST['playerID'];
		$id				= explode(',', $ids);
	}

	for ($i=0; $i < count($id); $i++) {
		$output			= NULL;
		$send 			= TRUE;
		$playerSQL 	= $db->query("SELECT * FROM `player` WHERE playerID='".$id[$i]."'");
		$player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);

		$assetLogName = strlen($name) > 35 ? substr($name,0,32)."..." : $name;
		systemLog('Player', 'Upload asset: '.$assetLogName.' to player '.$player['name'], $loginUserID, 1);


		if(isset($_POST['multidrop'])){
			//print_r($images);
			//print_r();
			$url	= NULL;
			$send	= FALSE;
			if($set['debug'] == 1) echo 'Send to: '.$player['address'].'/api/v1/file_asset<br />';
			if($set['debug'] == 1) print_r($data3);


			$url = callURL('POST3', $player['address'].'/api/v1/file_asset', $data3, $id[$i], false);
			if($set['debug'] == 1) echo 'Response: '.$url.'<br />';
			if (strpos($url, '/home/pi/screenly_assets') === false) {
				if($set['debug'] == 1) echo' Error !<br />';
				$output .= $player['name'];
				continue;
			}
			else {
				if($dzchunkindex == ($dztotalchunkcount-1)){
					if($set['debug'] == 1) echo'Chunking...DONE<br />';
					$send = TRUE;
				}
				else {
					if($set['debug'] == 1) echo'Chunking...'.$dzchunkindex.' of '.($dztotalchunkcount-1).'<br />';
				}
			}
		}

		if($send){
			$data 										= array();
			$data['mimetype'] 				= $mimetype;
			$data['is_enabled'] 			= $active;
			$data['is_active'] 				= $active;
			$data['name'] 						= $name;
			$data['start_date'] 			= $start.'T'.$start_time.':00.000Z';
			$data['end_date'] 				= $end.'T'.$end_time.':00.000Z';
			$data['duration'] 				= $duration;
			$data['play_order']				= 0;
			$data['nocache'] 					= 0;
			$data['uri'] 							= $url;
			$data['skip_asset_check'] = 1;

			//print_r($data);
			if($set['debug'] == 1) echo 'ID: '.$id[$i].'<br />';
			if($set['debug'] == 1) echo 'Send to API <br />';
			if($out = callURL('POST', $player['address'].'/api/'.$apiVersion.'/assets', $data, $id[$i], false)){
				if($set['debug'] == 1) echo 'API Call: '.$out.'<br />';
				if(strpos($out, '201') === false){
					$output .= $player['name'].'
					';
				} else if(!isset($_POST['multidrop'])) echo Translation::of('msg.asset_added_successfully_player', ['name' => $player['name']]);
			}
			else {
				header('HTTP/1.1 404 Not Found');
				$output .= Translation::of('msg.cant_delete_asset');
			}
		}

		if($output == NULL){
			header('HTTP/1.1 200 OK');
		} else {
			header('HTTP/1.1 500 Internal Server Error');
			echo Translation::of('msg.cant_upload_to').'
			'.$output;
		}
	}
}

if(isset($_POST['changeAssetState'])){
	$playerID 			= $_POST['id'];
	$asset					= $_POST['asset'];
	$playerSQL 			= $db->query("SELECT * FROM `player` WHERE playerID='".$playerID."'");
  $player 				= $playerSQL->fetchArray(SQLITE3_ASSOC);
	$playerAddress 	= $player['address'];
	systemLog('Player', 'Change asset state: '.$asset.' on player '.$player['name'], $loginUserID, 1);
	$data = callURL('GET', $playerAddress.'/api/'.$apiVersion.'/assets/'.$asset, false, $playerID, false);
	if($data['is_enabled'] == 1){
		$data['is_enabled'] = '0';
		$data['is_active'] 	= '0';
	}
	else {
		$data['is_enabled'] = '1';
		$data['is_active'] 	= '1';
	}
	if($data['mimetype'] == 'video') $data['duration'] = 0;
	if(callURL('PUT', $playerAddress.'/api/'.$apiVersion.'/assets/'.$asset, $data, $playerID, false)){
		header('HTTP/1.1 200 OK');
		exit();
	} else {
		header('HTTP/1.1 404 Not Found');
		exit();
	}
}

if(isset($_POST['changeAsset'])){
	$playerID 			= $_POST['playerID'];
	$orderD 				= $_POST['order'];
	$playerSQL 			= $db->query("SELECT * FROM `player` WHERE playerID='".$playerID."'");
  $player 				= $playerSQL->fetchArray(SQLITE3_ASSOC);
	$playerAddress 	= $player['address'];
	$result = callURL('GET', $playerAddress.'/api/v1/assets/control/'.$orderD.'', false, $playerID, false);
	$db->exec("UPDATE `player` SET sync='".time()."' WHERE playerID='".$playerID."'");
	if($result != ''){
		header('HTTP/1.1 200 OK');
		echo $result;
	}
	else header('HTTP/1.1 404 Not Found');
}

if(isset($_POST['exec_reboot'])){
	$playerID 			= $_POST['playerID'];
	$playerSQL 			= $db->query("SELECT * FROM `player` WHERE playerID='".$playerID."'");
	$player 				= $playerSQL->fetchArray(SQLITE3_ASSOC);
	$playerAddress 	= $player['address'];
	$db->exec("UPDATE `player` SET sync='".time()."' WHERE playerID='".$playerID."'");
	systemLog('Player', 'Execute Reboot on player: '.$player['name'], $loginUserID, 1);
	header('HTTP/1.1 200 OK');
	echo Translation::of('msg.reboot_command_send');
	$result = callURL('POST', $playerAddress.'/api/v1/reboot_screenly', false, $playerID, false);
}

if(isset($_POST['editInformation'])){
	$playerID 	= $_POST['playerID'];
  $playerSQL 	= $db->query("SELECT * FROM `player` WHERE playerID='".$playerID."'");
  $player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
	if($playerID != ''){
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json');
		$return_arr = array("player_name" => $player['name'], "player_address" => $player['address'], "player_location" => $player['location'], "player_user" => $player['player_user'], "player_password" => encrypting('decrypt', $player['player_password']));
		echo json_encode($return_arr);
	}
	else header('HTTP/1.1 404 Not Found');
}

if(isset($_POST['changeOrder'])){
	$playerID 			= $_POST['id'];
	$playerSQL 			= $db->query("SELECT * FROM `player` WHERE playerID='".$playerID."'");
	$player 				= $playerSQL->fetchArray(SQLITE3_ASSOC);
	$playerAddress 	= $player['address'];
	$data						= 'ids=';
	$i = 0;
	foreach ($_POST['order'] as $value) {
		$data .= $value.',';
	}
	$data = substr($data, 0, -1);
	$result = callURL('POST2', $playerAddress.'/api/v1/assets/order', $data, $playerID, false);
	$db->exec("UPDATE `player` SET sync='".time()."' WHERE playerID='".$playerID."'");
	if(json_encode($result) != ''){
		header('HTTP/1.1 200 OK');
		print_r(json_encode($result));
	}
	else header('HTTP/1.1 404 Not Found');
}

// Settings
if(isset($_POST['saveSettings']) && (getGroupID($loginUserID) == 1 || hasSettingsSystemRight($loginUserID))){
  $duration				= isset($_POST['duration']) ? $_POST['duration'] : $set['duration'];
  $end_date 			= isset($_POST['end_date']) ? $_POST['end_date'] : $set['end_date'];
  $name 		 			= isset($_POST['name']) ? $_POST['name'] : $set['name'];
  $design		 		 	= $_POST['color'] == '' ? '0' : $_POST['color'];
  $timezone	 		 	= isset($_POST['timezone']) ? $_POST['timezone'] : $set['timezone'];
  $uploadMaxSize 	= isset($_POST['uploadMaxSize']) ? $_POST['uploadMaxSize'] : $set['uploadMaxSize'];
  $debug	 		 		= isset($_POST['debug']) ? 1 : 0;
  $firstStart 		= isset($_POST['firstStartSettings']) ? $_POST['firstStartSettings'] : 0;


  if($duration AND $end_date){
    if($db->exec("UPDATE settings SET end_date='".$end_date."', name='".$name."', design='".$design."', uploadMaxSize='".$uploadMaxSize."', timezone='".$timezone."', duration='".$duration."', debug='".$debug."' WHERE settingsID='1'")){
      if($firstStart == 1){
        $db->exec("UPDATE settings SET firstStart='3' WHERE settingsID='1'");
      }
			sysinfo('success', 'Account data saved!', 0);
    } else sysinfo('danger', Translation::of('msg.cant_update_settings'));
  }	else sysinfo('danger', Translation::of('msg.no_valid_data'));
  redirect($backLink);
}

function checkboxState($value){
	if($value == 1) $output = ' checked="1"';
	else $output = '';
	return $output;
}

if((isset($_GET['action']) && $_GET['action'] == 'startup')){
  $db->exec("UPDATE settings SET firstStart='4' WHERE settingsID='1'");
  redirect($backLink);
}

if((isset($_GET['action']) && $_GET['action'] == 'endfirstInstaller')){
  $db->exec("UPDATE settings SET firstStart='99' WHERE settingsID='1'");
  redirect('index.php');
}
