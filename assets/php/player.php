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
            Player Module
_______________________________________
*/


require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');
	

	// POST: saveIP - Auto discovery function
	if(isset($_POST['saveIP'])){
	  $name 			= isset($_POST['name']) ? $_POST['name'] : '';
	  $address 		= isset($_POST['address']) ? $_POST['address'] : '';
	  $location 	= isset($_POST['location']) ? $_POST['location'] : '';
	  $user 			= isset($_POST['user']) ? $_POST['user'] : '';
	  $pass 			= isset($_POST['pass']) ? $_POST['pass'] : '';
	  $firstStart = isset($_POST['firstStartPlayer']) ? $_POST['firstStartPlayer'] : '';

	  if($address){
	    $db->exec("INSERT INTO player (name, address, location, player_user, player_password, userID) values('".$name."', '".$address."', '".$location."', '".$user."', '".$pass."', '".$loginUserID."')");
			if($firstStart == 1){
				$db->exec("UPDATE settings SET firstStart='3' WHERE settingsID='1'");
			}
	    sysinfo('success', Translation::of('msg.player_added_successfully', ['name' => $name]));
	  }	else sysinfo('danger', Translation::of('msg.cant_add_player'));
	  redirect($backLink);
	}

	// POST: updatePlayer - Update player data in database
	if(isset($_POST['updatePlayer'])){
	  $name 		= $_POST['name'];
	  $address	= $_POST['address'];
	  $location = $_POST['location'];
	  $user 		= $_POST['user'];
	  $pass 		= $_POST['pass'];
	  $playerID = $_POST['playerID'];


	  if($address){
	    $db->exec("UPDATE player SET name='".$name."', address='".$address."', location='".$location."', player_user='".$user."', player_password='".$pass."' WHERE playerID='".$playerID."'");
	    sysinfo('success', Translation::of('msg.player_update_successfully'));
	  }	else sysinfo('danger', Translation::of('msg.cant_update_player'));
	  redirect($backLink);
	}

	// GET: action:delete - Delete player from database
	if(isset($_GET['action']) && $_GET['action'] == 'delete'){
	  $playerID = $_GET['playerID'];

	  if(isset($playerID)){
	    $db->exec("DELETE FROM player WHERE playerID='".$playerID."'");
	    sysinfo('success', Translation::of('msg.player_delete_successfully'));
	  } else sysinfo('danger', Translation::of('msg.cant_delete_player'));
	  redirect('index.php?site=players');
	}

	// GET: action2:deleteAllAssets - Delete all assets from a player via API
	if((isset($_GET['action2']) && $_GET['action2'] == 'deleteAllAssets')){
	  $id 				= $_GET['playerID'];
	  $playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$id."'");
	  $player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
	  $data 			= NULL;
	  $playerAPI = callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets', false, $id, false);

	  foreach ($playerAPI as $value) {
	    if(callURL('DELETE', $player['address'].'/api/'.$apiVersion.'/assets/'.$value['asset_id'], $data, $id, false)){
	      //sysinfo('success', 'Asset deleted successfully');
	      redirect($backLink);
	    }	else sysinfo('danger', Translation::of('msg.cant_delete_asset'));
	  }
	}

	// POST: updateAsset - Update Asset information from a player via API
	if(isset($_POST['updateAsset'])){
	  $id 				= $_POST['id'];
	  $asset 			= $_POST['asset'];
	  $name 			= $_POST['name'];
	  $start 			= date("Y-m-d", strtotime($_POST['start_date']));
	  $start_time	= $_POST['start_time'];
	  $end 				= $_POST['end_date'];
	  $end_time		= $_POST['end_time'];
	  $duration 	= $_POST['duration'];

	  if (strpos($end, '9999') === false) {
	    $end 				= date("Y-m-d", strtotime($end));
	  } else {
	    $end				= '9999-01-01';
	  }

	  $playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$id."'");
	  $player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
	  $data 			= callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, false, $id, false);

	  if($data['name'] != $name) $data['name'] = $name;

	  if($data['duration'] != $duration && $duration > 1) $data['duration'] = $duration;
	  else $data['duration'] = 30;
	  $data['start_date'] = $start.'T'.$start_time.':00.000Z';
	  $data['end_date'] = $end.'T'.$end_time.':00.000Z';

	  if(callURL('PUT', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, $data, $id, false)){
	    sysinfo('success', Translation::of('msg.asset_update_successfully'));
	  }	else sysinfo('danger', Translation::of('msg.cant_update_asset'));
	  redirect($backLink);
	}

	// GET: action2:deleteAsset - Delete asset from a player via API
	if((isset($_GET['action2']) && $_GET['action2'] == 'deleteAsset')){
	  $id 				= $_GET['id'];
	  $asset 			= $_GET['asset'];
	  $playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$id."'");
	  $player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
	  $data 			= NULL;

	  if(callURL('DELETE', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, $data, $id, false)){
	    //sysinfo('success', 'Asset deleted successfully');
	    redirect($backLink);
	  } else sysinfo('danger', Translation::of('msg.cant_delete_asset'));
	}
