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
             Players Site
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');


// POST: saveIP - Auto discovery function
if(isset($_POST['saveIP'])){
  $name 		= isset($_POST['name']) ? $_POST['name'] : '';
  $address 	= isset($_POST['address']) ? $_POST['address'] : '';
  $location = isset($_POST['location']) ? $_POST['location'] : '';
  $user 		= isset($_POST['user']) ? $_POST['user'] : '';
  $pass 		= isset($_POST['pass']) ? $_POST['pass'] : '';

  if($address){
    $db->exec("INSERT INTO player (name, address, location, player_user, player_password, userID) values('".$name."', '".$address."', '".$location."', '".$user."', '".$pass."', '".$loginUserID."')");
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
  redirect($backLink);
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

// GET: action:startup - Skip firstStart screen
if((isset($_GET['action']) && $_GET['action'] == 'startup')){
  firstStart('set', 3);
  redirect($backLink);
}

// GET: action:view - Player detail overview
if(isset($_GET['action']) && $_GET['action'] == 'view'){
  if(isset($_GET['playerID'])){
    $playerID 	= $_GET['playerID'];
    $playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$playerID."'");
    $player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
    $monitor 		= 0;

    $player['name'] != '' ? $playerName = $player['name'] : $playerName = Translation::of('unkown_name');
    $player['location'] != '' ? $playerLocation = $player['location'] : $playerLocation = '';

    if(checkAddress($player['address'].'/api/'.$apiVersion.'/assets')){
      $playerAPI = callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets', false, $playerID, false);
      $db->exec("UPDATE player SET sync='".time()."' WHERE playerID='".$playerID."'");
      $monitor	 = checkAddress($player['address'].':9020/screen/screenshot.png');
      $playerAPICall = TRUE;

      if($monitor == true){
        $monitorInfo = '<span class="badge badge-success">  '.strtolower(Translation::of('installed')).'  </span>';
      } else $monitorInfo = '<a href="#" title="'.Translation::of('what_does_that_mean').'"><span class="badge badge-info">'.strtolower(Translation::of('not_installed')).'</span></a>';

      $status		 		= strtolower(Translation::of('online'));
      $statusColor 	= 'success';
      $newAsset			= '<a href="#" data-toggle="modal" data-target="#newAsset" class="btn btn-success btn-sm btn-block"><i class="tim-icons icon-simple-add"></i> '.Translation::of('new_asset').'</a>';
      $bulkDelete		= '<a href="#" data-toggle="modal" data-target="#confirmDeleteAssets" data-href="index.php?action=view&playerID=21&action2=deleteAllAssets&playerID='.$player['playerID'].'" class="btn btn-block btn-danger" title="delete"><i class="tim-icons icon-simple-remove"></i> '.Translation::of('clean_assets').'</a>';
      $navigation 	= '<div class="row"><div class="col-xs-12 col-md-6 mb-2"><button data-playerID="'.$player['playerID'].'" data-order="previous" class="changeAsset btn btn-sm btn-block btn-info" title="'.Translation::of('previous_asset').'"><i class="tim-icons icon-double-left"></i> '.Translation::of('asset').'</button></div> <div class="col-xs-12 col-md-6 mb-2"> <button data-playerID="'.$player['playerID'].'" data-order="next" class="changeAsset btn btn-sm btn-block btn-info" title="'.Translation::of('next_asset').'">'.Translation::of('asset').' <i class="tim-icons icon-double-right"></i></button></div></div>';
      $management		= '<a href="http://'.$player['address'].'" target="_blank" class="btn btn-primary btn-block"><i class="tim-icons icon-spaceship"></i> '.Translation::of('open_management').'</a>';
      $reboot				= '<button data-playerid="'.$player['playerID'].'" class="btn btn-block btn-info reboot" title="'.Translation::of('reboot_player').'"><i class="tim-icons icon-refresh-01"></i> '.Translation::of('reboot_player').'</button>';
      $script 			= '
      <tr>
        <td>'.Translation::of('monitor_addon').':</td>
        <td>'.$monitorInfo.'</td>
      </tr>
      ';
    }
    else {
      $playerAPICall 	= FALSE;
      $playerAPI 			= NULL;
      $status 				= strtolower(Translation::of('offline'));
      $statusColor 		= 'danger';
      $navigation 		= '';
      $script 				= '';
      $newAsset				= '';
      $bulkDelete			= '';
      $management			= '';
      $reboot 				= '';

      if(checkAddress($player['address'])){
        $status		 		= strtolower(Translation::of('online'));
        $statusColor 	= 'success';
      }
    }

    echo '
    <div class="row">
      <div class="col-xl-3 col-lg-4 col-md-5 order-sm-1">
        <div class="card card-user">
          <div class="card-body">
            <div class="author">
              <div class="block block-monitor"></div>
              <div class="playerImageDiv">
                <img class="img-fluid player" src="'.$loadingImage.'" data-src="'.$player['address'].'" alt="'.$playerName.'" />
                <div class="dropdown detailOptionMenu">
                  <button class="btn btn-secondary btn-block btn-sm dropdown-toggle btn-icon" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="tim-icons icon-settings-gear-63"></i>
                  </button>
                  <div class="dropdown-menu dropdown-black dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <a href="#" data-playerid="'.$player['playerID'].'" class="dropdown-item editPlayerOpen" title="edit">'.Translation::of('edit').'</a>
                    <a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="index.php?action=delete&playerID='.$player['playerID'].'" class="dropdown-item" title="delete">'.Translation::of('delete').'</a>
                  </div>
                </div>
              </div>
              <h3 class="mt-3">'.$playerName.'</h3>
            </div>

            <div class="card-description">
              <table class="table tablesorter tableTransparency" id="playerInfo">
                <tbody>
                  <tr>
                    <td colspan="2">'.$navigation.'</td>
                  </tr>
                  <tr>
                    <td>'.Translation::of('status').':</td>
                    <td><span class="badge badge-'.$statusColor.'">'.$status.'</span></td>
                  </tr>
                  <tr>
                    <td>'.Translation::of('ip_address').':</td>
                    <td>'.$player['address'].'</td>
                  </tr>
                  <tr>
                    <td>'.Translation::of('location').':</td>
                    <td>'.$playerLocation.'</td>
                  </tr>
                  '.$script.'
                </tbody>
              </table>
              <hr />
              '.$management.'
              '.$reboot.'
              '.$bulkDelete.'
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-9 col-lg-8 col-md-7 order-sm-0">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-md-10">
                <h5 class="title">'.Translation::of('assets').'</h5>
              </div>
              <div class="col-md-2 float-right">
                '.$newAsset.'
              </div>
            </div>
          </div>
          <div class="card-body">
            ';
    if($playerAPICall && $playerAPI != 'authentication error 401'){
      echo '
            <table class="table" id="assets">
              <thead class="text-primary">
                <tr>
                  <th></th>
                  <th data-priority="1">'.Translation::of('name').'</th>
                  <th data-priority="3">'.Translation::of('status').'</th>
                  <th>Date</th>
                  <th class="d-none">'.Translation::of('show').'</th>
                  <th data-priority="2"> </th>
                </tr>
              </thead>
              <tbody>
                  ';
      for($i=0; $i < sizeof($playerAPI); $i++)  {
        $startAsset				= explode("T", $playerAPI[$i]['start_date']);
        $startAssetTime		= explode("+", $startAsset['1']);
        $startAssetTimeHM	= explode(":", $startAssetTime['0']);
        $start						= date('d.m.Y', strtotime($startAsset['0']));
        $start_date				= date('Y-m-d', strtotime($startAsset['0']));
        $start_time				= $startAssetTimeHM['0'].':'.$startAssetTimeHM['1'];
        $endAsset					= explode("T", $playerAPI[$i]['end_date']);
        $endAssetTime			= explode("+", $endAsset['1']);
        $endAssetTimeHM		= explode(":", $endAssetTime['0']);
        $end_time					= $endAssetTimeHM['0'].':'.$endAssetTimeHM['1'];

        if (strpos($endAsset['0'], '9999') === false) {
          $end				= date('d.m.Y', strtotime($endAsset['0']));
          $end_date		= date('Y-m-d', strtotime($endAsset['0']));
        } else {
          $end				= Translation::of('forever');
          $end_date		= $endAsset['0'];
        }

        $yes 							= '<span class="badge badge-success m-2" data-asset_id="'.$playerAPI[$i]['asset_id'].'">  '.strtolower(Translation::of('active')).'  </span>';
        $no 							= '<span class="badge badge-danger m-2" data-asset_id="'.$playerAPI[$i]['asset_id'].'">  '.strtolower(Translation::of('details')).'  </span>';
        $playerAPI[$i]['is_enabled'] == 1 ? $active = $yes : $active = $no;
        if($playerAPI[$i]['mimetype'] == 'webpage'){
          $mimetypeIcon = '<i class="tim-icons icon-world"></i>';
        }
        else if($playerAPI[$i]['mimetype'] == 'video'){
          $mimetypeIcon = '<i class="tim-icons icon-video-66"></i>';
        }
        else {
          $mimetypeIcon = '<i class="tim-icons icon-image-02"></i>';
        }

        if($playerAPI[$i]['is_active'] == 1){
          $shown = Translation::of('shown');
          $shown_class = '';
        } else {
          $shown = Translation::of('hidden');
          $shown_class = 'class="asset-hidden"';
        }
        // TODO: add title to buttons
        echo '
                <tr id="'.$playerAPI[$i]['asset_id'].'" data-playerID="'.$player['playerID'].'"'.$shown_class.'>
                  <td>'.$player['playerID'].'</td>
                  <td>'.$mimetypeIcon.' '.$playerAPI[$i]['name'].'</td>
                  <td>'.$active.'</td>
                  <td><span class="d-block d-sm-none"><br /></span>'.Translation::of('start').': '.$start.'<br />'.Translation::of('end').':&nbsp;&nbsp;&nbsp;'.$end.'</td>
                  <td class="d-none">'.$shown.'</td>
                  <td>
                    <button class="changeState btn btn-info btn-sm mb-1" data-asset_id="'.$playerAPI[$i]['asset_id'].'" data-player_id="'.$player['playerID'].'" title="switch on/off"><i class="tim-icons icon-button-power"></i></button>
                    <button class="options btn btn-warning btn-sm mb-1" data-asset="'.$playerAPI[$i]['asset_id'].'" data-player_id="'.$player['playerID'].'" data-name="'.$playerAPI[$i]['name'].'" data-start-date="'.$start_date.'" data-start-time="'.$start_time.'" data-end-date="'.$end_date.'" data-end-time="'.$end_time.'" data-duration="'.$playerAPI[$i]['duration'].'"
                    data-uri="'.$playerAPI[$i]['uri'].'" title="edit"><i class="tim-icons icon-pencil"></i></button>
                    <a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="index.php?action=view&playerID='.$player['playerID'].'&action2=deleteAsset&id='.$player['playerID'].'&asset='.$playerAPI[$i]['asset_id'].'" class="btn btn-danger btn-sm mb-1" title="delete"><i class="tim-icons icon-simple-remove"></i></a>
                  </td>
                </tr>
        ';
      }
      echo '
              </tbody>
            </table>
      ';
    }
    else {
      echo  '
            <div class="alert alert-danger">
              <span><b>'.Translation::of('msg.no_screenly_api').' - </b> '.Translation::of('msg.no_data_collected').'</span>
            </div>
      ';
    }
    echo '
          </div>
        </div>
      </div>
    </div>

    <!-- newAsset -->
    <div class="modal fade" id="newAsset" tabindex="-1" role="dialog" aria-labelledby="newAssetModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newAssetModalLabel">New Asset</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" href="#url" role="tab" data-toggle="tab">'.Translation::of('url').'</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#upload" role="tab" data-toggle="tab">'.Translation::of('upload').'</a>
              </li>
            </ul>

            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="url">
                <form id="assetNewForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST">
                  <div class="form-group">
                    <label for="InputNewAssetUrl">'.Translation::of('asset_url').'</label>
                    <input name="url" type="text" pattern="^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&\'\(\)\*\+,;=.]+$" class="form-control" id="InputNewAssetUrl" placeholder="http://www.example.com" autofocus>
                  </div>
                  <div class="form-group text-right">
                    <input name="id" type="hidden" value="'.$player['playerID'].'" />
                    <input name="mimetype" type="hidden" value="webpage" />
                    <input name="newAsset" type="hidden" value="1" />
                    <button type="submit" name="saveAsset" class="btn btn-success btn-sm">'.Translation::of('save').'</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('close').'</button>
                  </div>
                </form>
              </div>
              <div role="tabpanel" class="tab-pane" id="upload">
                <form action="http://'.$player['address'].'/api/v1/file_asset" class="dropzone drop">
                  <div class="form-group">
                    <input type="file" multiple />
                  </div>
                </form>
                <div class="form-group text-right">
                  <br />
                  <button type="button" class="btn btn-secondary btn-sm close_modal" data-close="#newAsset">'.Translation::of('close').'</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- editAsset -->
    <div class="modal fade" id="editAsset" tabindex="-1" role="dialog" aria-labelledby="editAssetModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editAssetModalLabel">'.Translation::of('edit_asset').'</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="assetEditForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST">
              <div class="form-group">
                <label for="InputAssetName">'.Translation::of('name').'</label>
                <input name="name" type="text" class="form-control" id="InputAssetName" placeholder="'.Translation::of('name').'" value="Name" />
              </div>
              <div class="form-group">
                <label for="InputAssetUrl">'.Translation::of('url').'</label>
                <input name="name" type="text" class="form-control" id="InputAssetUrl" disabled="disabled" value="url" />
              </div>
              <div class="form-group">
                <label for="InputAssetStart">'.Translation::of('start').'</label>
                <input name="start_date" type="date" class="form-control" id="InputAssetStart" placeholder="'.Translation::of('start_date').'" value="'.date('Y-m-d', strtotime('now')).'" />
                <input name="start_time" type="time" class="form-control" id="InputAssetStartTime" placeholder="'.Translation::of('start_time').'" value="12:00" />
              </div>
              <div class="form-group">
                <label for="InputAssetEnd">'.Translation::of('end').'</label>
                <input name="end_date" type="date" class="form-control" id="InputAssetEnd" placeholder="'.Translation::of('end_date').'" value="'.date('Y-m-d', strtotime('+1 week')).'" />
                <input name="end_time" type="time" class="form-control" id="InputAssetEndTime" placeholder="'.Translation::of('end_time').'" value="12:00" />
              </div>
              <div class="form-group">
                <label for="InputAssetDuration">'.Translation::of('duration_in_sec').'</label>
                <input name="duration" type="number" class="form-control" id="InputAssetDuration" value="30" />
              </div>
              <div class="form-group text-right">
                <input name="updateAsset" type="hidden" value="1" />
                <input name="asset" id="InputAssetId"type="hidden" value="1" />
                <input name="id" id="InputSubmitId" type="hidden" value="'.$player['playerID'].'" />
                <button type="submit" class="btn btn-warning btn-sm">'.Translation::of('update').'</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('close').'</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- confirmReboot -->
    <div class="modal fade" id="confirmReboot" tabindex="-1" role="dialog" aria-labelledby="confirmRebootModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">'.Translation::of('attention').'!</h5>
          </div>
          <div class="modal-body">
            '.Translation::of('msg.reboot_really_player').'
            <div class="form-group text-right">
              <button class="exec_reboot btn btn-sm btn-danger" title="'.Translation::of('reboot_now').'">'.Translation::of('reboot_now').'</button>
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('cancel').'</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- confirmDeleteAssets -->
    <div class="modal fade" id="confirmDeleteAssets" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteAssets" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">'.Translation::of('attention').'!</h5>
          </div>
          <div class="modal-body">
            '.Translation::of('msg.clean_all_assets').'
            <div class="form-group text-right">
              <a class="btn btn-danger btn-ok btn-sm">'.Translation::of('delete').'</a>
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('cancel').'</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  ';
  }
  else {
    sysinfo('danger', Translation::of('msg.no_player_submitted'));
    redirect('index.php');
  }
}
else {
  $playerSQL = $db->query("SELECT * FROM player ORDER BY name ASC");

  if($playerCount > 0){
    echo'
    <div class="page-header">
              <div class="row align-items-center">
                <div class="col-auto">
                  <h2 class="page-title">
                    Player Overview
                  </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ml-auto d-print-none">
                  <a href="javascript:void(0)" data-toggle="modal" data-target="#newPlayer" class="btn btn-primary ml-3 d-none d-sm-inline-block" data-toggle="modal" data-target="#modal-report">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                    Add Player
                  </a>
                  <a href="javascript:void(0)" data-toggle="modal" data-target="#newPlayer" class="btn btn-primary ml-3 d-sm-none btn-icon" data-toggle="modal" data-target="#modal-report" aria-label="Create new report">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                  </a>
                </div>
              </div>
            </div>
  <div class="row">
    ';
    while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
      if($player['name'] == ''){
        $name	 		= Translation::of('no_player_name');
        $imageTag = Translation::of('no_player_name').' '.$player['playerID'];
      }
      else {
        $name 		= $player['name'];
        $imageTag = $player['name'];
      }
      echo'
      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6" data-string="'.$name.'">
        <div class="card card-sm">
          <a href="index.php?site=players&action=view&playerID='.$player['playerID'].'" class="d-block"><img src="'.$loadingImage.'" data-src="'.$player['address'].'" alt="'.$imageTag.'" class="player card-img-top"></a>
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="lh-sm">
                <div>'.$name.'</div>
              </div>
              <div class="ml-auto">
                <a href="index.php?site=players&action=view&playerID='.$player['playerID'].'" class="text-muted">
                  '.$player['address'].'
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      ';
    }
    echo '
  </div>

  <!-- newPlayer -->
  <div class="modal fade" id="newPlayer" tabindex="-1" role="dialog" aria-labelledby="newPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newPlayerModalLabel">'.Translation::of('add_player').'</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" href="#manual" role="tab" data-toggle="tab">'.Translation::of('manual').'</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#auto" role="tab" data-toggle="tab">'.Translation::of('auto').'</a>
            </li>
          </ul>

          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="manual">
              <form id="playerForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
                <div class="form-group">
                  <label for="InputPlayerName">'.Translation::of('enter_player_name').'</label>
                  <input name="name" type="text" class="form-control" id="InputPlayerName" placeholder="'.Translation::of('player_name').'" autofocus />
                </div>
                <div class="form-group">
                  <label for="InputLocation">'.Translation::of('enter_player_location').'</label>
                  <input name="location" type="text" class="form-control" id="InputLocation" placeholder="'.Translation::of('player_location').'" />
                </div>
                <div class="form-group">
                  <label for="InputAdress">'.Translation::of('enter_player_ip').'</label>
                  <input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="'.Translation::of('no_valid_ip').'" type="text" class="form-control" id="InputAdress" placeholder="192.168.1.100" required />
                  <div class="help-block with-errors"></div>
                </div>
                <hr />
                <div class="form-group">
                  <label for="InputUser">'.Translation::of('player_authentication').' </label>
                  <input name="user" type="text" class="form-control" id="InputUser" placeholder="'.Translation::of('username').'" />
                </div>
                <div class="form-group">
                  <input name="pass" type="password" class="form-control" id="InputPassword" placeholder="'.Translation::of('password').'" />
                </div>
                <div class="form-group text-right">
                  <button type="submit" name="saveIP" class="btn btn-success btn-sm">'.Translation::of('save').'</button>
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('close').'</button>
                </div>
              </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="auto">
              <form id="newPlayerDiscover" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
                <div class="form-group">
                  <label for="InputCIDR">'.Translation::of('enter_ip_range').'</label>
                  <input name="range" pattern="^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(3[0-2]|[1-2][0-9]|[0-9]))$" data-error="No valid IPv4 address with CIDR" type="text" class="form-control" id="InputCIDR" placeholder="192.168.1.0/24" required />
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                  <label for="discoverStatus">'.Translation::of('status').'</label>
                  <hr />
                  <div id="discoverStatus"></div>
                </div>
                <div class="form-group text-right">
                  <input name="userID" type="hidden" value="'.$loginUserID.'" />
                  <button type="submit" name="startDiscover" class="btn btn-primary btn-sm start_discovery">'.Translation::of('discovery').'</button>
                  <button type="button" class="btn btn-secondary btn-sm close_modal" data-close="#newPlayer">'.Translation::of('close').'</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- editPlayer -->
  <div class="modal fade" id="editPlayer" tabindex="-1" role="dialog" aria-labelledby="newPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPlayerModalLabel">'.Translation::of('edit_name', ['name' => '<span id="playerNameTitle"></span>']).'</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="playerFormEdit" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
            <div class="form-group">
              <label for="InputPlayerNameEdit">'.Translation::of('enter_player_name').'</label>
              <input name="name" type="text" class="form-control" id="InputPlayerNameEdit" placeholder="'.Translation::of('player_name').'" autofocus />
            </div>
            <div class="form-group">
              <label for="InputLocationEdit">'.Translation::of('enter_player_location').'</label>
              <input name="location" type="text" class="form-control" id="InputLocationEdit" placeholder="'.Translation::of('player_location').'" />
            </div>
            <div class="form-group">
              <label for="InputAdressEdit">'.Translation::of('enter_player_ip').'</label>
              <input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="'.Translation::of('no_valid_ip').'" type="text" class="form-control" id="InputAdressEdit" placeholder="192.168.1.100" required />
              <div class="help-block with-errors"></div>
            </div>
            <hr />
            <div class="form-group">
              <label for="InputUserEdit">'.Translation::of('player_authentication').'</label>
              <input name="user" type="text" class="form-control" id="InputUserEdit" placeholder="'.Translation::of('username').'" />
            </div>
            <div class="form-group">
              <input name="pass" type="password" class="form-control" id="InputPasswordEdit" placeholder="'.Translation::of('password').'" />
            </div>
            <div class="form-group text-right">
              <input name="playerID" id="playerIDEdit" type="hidden" value="" />
              <input name="mimetype" id="playerMimetype" type="hidden" value="" />
              <button type="submit" name="updatePlayer" class="btn btn-sm btn-warning">'.Translation::of('update').'</button>
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('close').'</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  ';
  }
  else {
    include('assets/php/firstStart.php');
  }
}
