<?php

	if(isset($_POST['newAsset'])){
		$now				= strtotime("-10 minutes");
		$id 				= $_POST['id'];
		$url 				= $_POST['url'];
		$name 			= (isset($_POST['name']) ? $_POST['name'] : $_POST['url']);
		$mimetype		= $_POST['mimetype'];
		$start 			= date("Y-m-d", $now);
		$start_time	= "00:00";
		$end 				= date("Y-m-d", strtotime("+".$set['end_date']." week"));
		$end_time		= $start_time;
		$duration 	= $set['duration'];

		if($name == '') $name = $url;

		$playerSQL 	= $db->query("SELECT * FROM `player` WHERE playerID='".$id."'");
		$player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);

		$data 										= array();
		$data['mimetype'] 				= $mimetype;
		$data['is_enabled'] 			= 1;
		$data['name'] 						= $name;
		$data['start_date'] 			= $start.'T'.$start_time.':00.000Z';
		$data['end_date'] 				= $end.'T'.$end_time.':00.000Z';
		$data['duration'] 				= $duration;
		$data['play_order']				= 0;
		$data['nocache'] 					= 0;
		$data['uri'] 							= $url;
		$data['skip_asset_check'] = 1;

		if(callURL('POST', $player['address'].'/api/'.$apiVersion.'/assets', $data, $id, false)){
			header('HTTP/1.1 200 OK');
			echo 'Asset added successfully';
			die();
		}
		else {
			header('HTTP/1.1 404 Not Found');
			echo 'Error! - Can \'t add the Asset';
			die();
		}
	}

	if(isset($_POST['changeAssetState'])){
		$playerID 			= $_POST['id'];
		$asset					= $_POST['asset'];
		$playerSQL 			= $db->query("SELECT * FROM `player` WHERE playerID='".$playerID."'");
    $player 				= $playerSQL->fetchArray(SQLITE3_ASSOC);
		$playerAddress 	= $player['address'];
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
		header('HTTP/1.1 200 OK');
		echo 'Reboot command send!';
		$result = callURL('POST', $playerAddress.'/api/v1/reboot_screenly', false, $playerID, false);
	}

	if(isset($_POST['editInformation'])){
		$playerID 	= $_POST['playerID'];
    $playerSQL 	= $db->query("SELECT * FROM `player` WHERE playerID='".$playerID."'");
    $player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
		if($playerID != ''){
			header('HTTP/1.1 200 OK');
			header('Content-Type: application/json');
			$return_arr = array("player_name" => $player['name'], "player_address" => $player['address'], "player_location" => $player['location'], "player_user" => $player['player_user'], "player_password" => $player['player_password']);
			echo json_encode($return_arr);
		}
		else header('HTTP/1.1 404 Not Found');
	}
