<?php
	// SESSION OPTIONS
	session_set_cookie_params(36000, '/' );
	session_start();
	// TRANSLATION CLASS
	require_once(__DIR__.'/assets/php/translation.php');
	use Translation\Translation;
	// FUNCTIONS
	require_once('_functions.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="description" content="Manage all Screenly players in one place." />
	<meta name="author" content="didiatworkz" />
	<title><?php echo _SYSTEM_NAME ?></title>
	<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png" />
	<link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png" />
	<link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png" />
	<link rel="manifest" href="assets/img/site.webmanifest" />
	<link rel="mask-icon" href="assets/img/safari-pinned-tab.svg" color="#1e1e2f" />
	<link rel="shortcut icon" href="assets/img/favicon.ico" />
	<meta name="msapplication-TileColor" content="#1e1e2f" />
	<meta name="msapplication-config" content="assets/img/browserconfig.xml" />
	<meta name="theme-color" content="#1e1e2f" />
	<link href="assets/css/fonts.css" rel="stylesheet" />
	<link href="assets/css/nucleo-icons.css" rel="stylesheet" />
	<link href="assets/css/black-dashboard.css?v=1.0.0" rel="stylesheet" />
	<link rel="stylesheet" href="assets/tools/DataTables/datatables.min.css" />
	<link rel="stylesheet" href="assets/tools/dropzone/dropzone.min.css">
	<link href="assets/css/monitor.css" rel="stylesheet" />
	<script src="assets/js/core/jquery.min.js"></script>
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>
	<script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
	<script src="assets/js/plugins/bootstrap-notify.js"></script>
	<script src="assets/js/black-dashboard.min.js?v=1.0.0"></script>
	<script src="assets/js/jquery-ui.min.js"></script>
	<script src="assets/tools/DataTables/datatables.min.js"></script>
	<script src="assets/tools/dropzone/dropzone.min.js"></script>
</head>

<body>
	  <div class="wrapper">
    <div class="main-panel">
	<?php
		if($loggedIn){
			if(isset($_POST['saveSettings']) && getGroupID($loginUserID) == 1){
				$refreshscreen = $_POST['refreshscreen'];
				$duration			 = $_POST['duration'];
		    $end_date 		 = $_POST['end_date'];
		    $name 		 		 = $_POST['name'];

				if($duration AND $end_date AND $refreshscreen){
					if($db->exec("UPDATE settings SET end_date='".$end_date."', name='".$name."', duration='".$duration."' WHERE settingsID='1'")){
						if($db->exec("UPDATE users SET refreshscreen='".$refreshscreen."' WHERE userID='".$loginUserID."'")){
							sysinfo('success', Translation::of('msg.settings_saved'));
						} else sysinfo('danger', Translation::of('msg.cant_update_user'));
					} else sysinfo('danger', Translation::of('msg.cant_update_settings'));
				}	else sysinfo('danger', Translation::of('msg.no_valid_data'));
				redirect($backLink);
			}

			// Player authentication
			$scriptAuthUsername = 'dummy';
			$scriptAuthPassword = 'dummy';

			if(isset($_GET['playerID']) && $_GET['playerID'] != ''){
				$scriptAuth = playerAuthentication($_GET['playerID']);

				if($scriptAuth["username"] != '' && $scriptAuth["password"] != ''){
					$scriptAuthUsername = $scriptAuth["username"];
					$scriptAuthPassword = $scriptAuth["password"];
				}
			}
			$scriptPlayerAuth = base64_encode($scriptAuthUsername.':'.$scriptAuthPassword);

	    if(isset($_GET['generateToken']) && $_GET['generateToken'] == 'yes'){
	      $now 	 = time();
	      $token = md5($loginUsername.$loginPassword.$now);

				if($token){
	        $db->exec("UPDATE settings SET token='".$token."' WHERE userID='".$loginUserID."'");
	        sysinfo('success', Translation::of('msg.new_token_generated'));
	        redirect('index.php?showToken=1');
	      } else sysinfo('danger', 'Error!');
	    }

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

			// INCLUDE: Top menubar
			include_once('assets/php/menu.php');

			// START CONTENT
			echo'
			<div class="content">
				';

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
			else if(isset($_GET['site'])){
				$moduleName = $_GET['site'];

				if (@file_get_contents('assets/php/'.$moduleName.'.php', 0, NULL, 0, 1)) {
					if(in_array(basename($moduleName), $_modules)){
						include('assets/php/'.basename($moduleName).'.php');
					}	else sysinfo('danger', Translation::of('msg.module_not_allowed'));
				}	else sysinfo('danger', Translation::of('msg.module_not_exists'));
			}
			else {
				$playerSQL = $db->query("SELECT * FROM player ORDER BY name ASC");

				if($playerCount > 0){
					echo'
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
						<div class="card">
							<div class="card-header">
								<h4 class="d-inline">'.$name.'</h4>
								<div class="dropdown d-inline pull-right">
									<button type="button" class="btn btn-link dropdown-toggle btn-icon" data-toggle="dropdown">
										<i class="tim-icons icon-settings-gear-63"></i>
									</button>
									<div class="dropdown-menu dropdown-menu-right dropdown-black" aria-labelledby="dropdownMenuLink">
										<a href="index.php?action=view&playerID='.$player['playerID'].'" class="dropdown-item" title="'.strtolower(Translation::of('details')).'"><i class="tim-icons icon-tablet-2"></i> '.strtolower(Translation::of('details')).'</a>
										<a href="#" data-playerid="'.$player['playerID'].'" class="dropdown-item editPlayerOpen" title="'.strtolower(Translation::of('edit')).'"><i class="tim-icons icon-pencil"></i> '.strtolower(Translation::of('edit')).'</a>
										<a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="index.php?action=delete&playerID='.$player['playerID'].'" class="dropdown-item" title="'.strtolower(Translation::of('delete')).'"><i class="tim-icons icon-trash-simple"></i> '.strtolower(Translation::of('delete')).'</a>
									</div>
								</div>
								<h5 class="card-category">'.$player['address'].'</h5>
							</div>
							<div class="card-body ">
								<a href="index.php?action=view&playerID='.$player['playerID'].'"><img class="player" src="'.$loadingImage.'" data-src="'.$player['address'].'" alt="'.$imageTag.'"></a>
							</div>
						</div>
					</div>
						';
					}
					echo '
				</div>
				';
				}
				else {
					include('assets/php/firstStart.php');
				}
			}
			echo '

		</div>
		<!-- END CONTENT -->

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

		<!-- account -->
		<div class="modal fade" id="account" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
		        <h5 class="modal-title">'.Translation::of('account').'</h5>
		      </div>
					<div class="modal-body">
            <div class="card card-user">
              <div class="card-body">
                <p class="card-text">
                  <div class="author">
                    <div class="block block-one"></div>
                    <div class="block block-two"></div>
                    <div class="block block-three"></div>
                    <div class="block block-four"></div>
  									<i class="tim-icons icon-single-02 account-icon"></i><br /><br />
                    <h4 class="title">'.$loginFullname.'</h4>
                    <span class="badge badge-secondary" title="Usergroup">'.$loginGroupName.'</span>
                  </div>
                </p>
	            </div>
						</div>
						<form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
							<div class="form-group">
								<label for="InputFirstname">'.Translation::of('firstname').'</label>
								<input name="firstname" type="text" class="form-control" id="InputFirstname" placeholder="John" value="'.$loginFirstname.'" />
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group">
								<label for="InputName">'.Translation::of('name').'</label>
								<input name="name" type="text" class="form-control" id="InputName" placeholder="Doe" value="'.$loginName.'" />
								<div class="help-block with-errors"></div>
							</div>
							<hr />
							<div class="form-group">
								<label for="InputUsername">'.Translation::of('change_username').'</label>
								<input name="username" type="text" class="form-control" id="InputUsername" placeholder="'.Translation::of('new_username').'" value="'.$loginUsername.'" />
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group">
								<label for="InputPassword1">'.Translation::of('change_password').'</label>
								<input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="'.Translation::of('new_password').'" />
							</div>
							<div class="form-group">
								<input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="'.Translation::of('confirm_password').'" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" />
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group text-right">
								<button type="submit" name="saveAccount" class="btn btn-sm btn-primary">'.Translation::of('update').'</button>
								<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">'.Translation::of('close').'</button>
							</div>
						</form>
	        </div>
				</div>
			</div>
		</div>

		<!-- addon -->
		<div class="modal fade" id="addon" tabindex="-1" role="dialog" aria-labelledby="newAddonModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="newAddonModalLabel">'.Translation::of('addon').'</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<img src="assets/img/addon.png" class="img-fluid mx-auto d-block" alt="addon" style="height: 180px" />
						The Screenly OSE Monitoring addon allows you to retrieve even more data from the Screenly Player and process it in the monitor. <br />
						You have the possibility to get a "live" image of the player\'s output.<br /><br />
						To install, you have to log in to the respective Screenly Player via SSH (How it works: <a href="https://www.raspberrypi.org/documentation/remote-access/ssh/" target="_blank">here</a>) <br />and execute this command:<br />
						<input type="text" class="form-control" id="InputBash" onClick="this.select();" value="bash <(curl -sL https://git.io/Jf900)">
						After that the player restarts and the addon has been installed.<br />
						<button type="button" class="btn btn-secondary btn-sm pull-right" data-dismiss="modal">'.Translation::of('close').'</button>
					</div>
				</div>
			</div>
		</div>

		';
		if($loginGroupID == 1){
			echo '
			<!-- settings -->
			<div class="modal fade" id="settings" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
			        <h5 class="modal-title">'.Translation::of('settings').'</h5>
			      </div>
						<div class="modal-body">
								<form id="settingsForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
									<div class="form-group">
										<label for="InputSetName">'.Translation::of('somo_name').'</label>
										<input name="name" type="text" class="form-control" id="InputSetName" placeholder="'.Translation::of('somo').'" value="'.$set['name'].'" required />
									</div>
									<div class="form-group">
										<label for="InputSetRefresh">'.Translation::of('refresh_time_player').'</label>
										<input name="refreshscreen" type="text" class="form-control" id="InputSetRefresh" placeholder="5" value="'.$loginRefreshTime.'" required />
									</div>
									<div class="form-group">
										<label for="InputSetDuration">'.Translation::of('assets_duration').'</label>
										<input name="duration" type="text" class="form-control" id="InputSetDuration" placeholder="30" value="'.$set['duration'].'" required />
									</div>
									<div class="form-group">
										<label for="InputSetEndDate">'.Translation::of('delay_of_weeks').'</label>
										<input name="end_date" type="text" class="form-control" id="InputSetEndDate" placeholder="1" value="'.$set['end_date'].'" required />
									</div>
									<div class="form-group text-right">
										<button type="submit" name="saveSettings" class="btn btn-primary btn-sm">'.Translation::of('update').'</button>
										<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('close').'</button>
									</div>
								</form>
		         </div>
					</div>
				</div>
			</div>
			';
		}

		echo '
		<!-- info -->
		<div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
		        <h5 class="modal-title">'._SYSTEM_NAME.'</h5>
		      </div>
					<div class="modal-body">
					  <a href="https://atworkz.de" target="_blank"><img src="assets/img/atworkz-logo.png" class="img-fluid mx-auto d-block" /></a>
						<table class="table table-sm">
						  <tr>
						    <td>'.Translation::of('monitor_version').':</td>
						    <td>'.$systemVersion.'</td>
						  </tr>
							<tr>
						    <td>'.Translation::of('screenly_api').':</td>
						    <td>'.$apiVersion.'</td>
						  </tr>
						  <tr>
						    <td>'.Translation::of('server_ip').':</td>
						    <td>'.$_SERVER['SERVER_ADDR'].($_SERVER['SERVER_PORT'] != '80' ? ':'.$_SERVER['SERVER_PORT'] : '').'</td>
						  </tr>
							<tr>
						    <td>'.Translation::of('php_version').':</td>
						    <td>'.phpversion().'</td>
						  </tr>
							<tr>
						    <td>&nbsp;</td>
						    <td>&nbsp;</td>
						  </tr>
						  <tr>
						    <td>'.Translation::of('project').':</td>
						    <td><a href="https://github.com/didiatworkz/screenly-ose-monitor" target="_blank">GitHub</a></td>
						  </tr>
							<tr>
						    <td>'.Translation::of('copyright').':</td>
						    <td><a href="https://atworkz.de" target="_blank">atworkz.de</a></td>
						  </tr>
						  <tr>
						    <td>'.Translation::of('design').':</td>
						    <td><a href="https://github.com/creativetimofficial/black-dashboard" target="_blank">Black Dashboard</a></td>
						  </tr>
						  <tr>
						    <td>'.Translation::of('scripts').':</td>
						    <td>
							  <a href="https://datatables.net" target="_blank">DataTables</a><br />
							  <a href="https://www.dropzonejs.com/" target="_blank">dropzoneJS</a>
							</td>
						  </tr>
						</table>
						<button type="button" class="btn btn-sm btn-secondary pull-right" data-dismiss="modal">'.Translation::of('close').'</button>
	        </div>
				</div>
			</div>
		</div>

		<!-- search -->
		<div class="modal modal-search fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="'.strtoupper(Translation::of('search_player')).'" autofocus>
              <button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
                <i class="tim-icons icon-simple-remove"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

		<!-- publicLink -->
		<div class="modal fade" id="publicLink" tabindex="-1" role="dialog" aria-labelledby="publicLinkModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">'.Translation::of('public_access_link').'</h5>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="InputSetToken">'.Translation::of('public_access_link_info').'</label>
							<input type="text" class="form-control" id="InputSetToken" onClick="this.select();" value="http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/index.php?monitoring=1&key='.$set['token'].'" />
						</div>
						<div class="form-group text-right">
							<a href="index.php?generateToken=yes" class="btn btn-info btn-sm">'.Translation::of('generate_token').'</a>
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('close').'</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- confirmDelete -->
		<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">'.Translation::of('attention').'!</h5>
					</div>
					<div class="modal-body">
						'.Translation::of('msg.delete_really_entry').'
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
	  else if((isset($_GET['monitoring']) && $_GET['monitoring'] == '1') && isset($_GET['key'])){
	    include('assets/php/publicLink.php');
	  }
		else {
			if (isset($logedout)){
				sysinfo('success', '<i class="fa fa-check"></i> '.Translation::of('msg.logged_out_successfully'));
			}
			echo '
				<div class="content">
					<div class="col-xs-12 col-md-4 offset-md-4 text-center p-3 mb-5">
						<h2>'._SYSTEM_NAME.'</h2>
						<p>'.Translation::of('please_log_in').'</p>
						<form id="Login" action="'.$_SERVER['PHP_SELF'].'" method="POST">
							<div class="form-group">
								<input name="user" type="text" class="form-control" placeholder="'.Translation::of('username').'" autofocus>
							</div>
							<div class="form-group">
								<input name="password" type="password" class="form-control" placeholder="'.Translation::of('password').'">
							</div>
							<button type="submit" name="Login" class="btn btn-primary btn-block" value="1">'.Translation::of('login').'</button>
						</form>
					</div>
				</div>
			';
		}
		$db->close();

		echo '
      <footer class="footer">
        <div class="container-fluid">
          <div class="copyright">';
            if(isset($pagination)) echo $pagination; echo '
			&copy '.date('Y'); ?> by <a href="https://www.atworkz.de" target="_blank">atworkz.de</a>  <?php if(!(isset($_GET['monitoring']) OR !$loggedIn)) echo '|  <a href="https://www.github.com/didiatworkz" target="_blank">Github</a> | <a href="javascript:void(0)" data-toggle="modal" data-target="#info">'.Translation::of('information').'</a>';
						$totalTime = array_sum(explode(' ',  microtime())) - $_loadMessureStart; echo '
			<script>console.log("Loaded in: '.$totalTime.'")</script>';
						echo'
		  </div>
        </div>
      </footer>
    </div>
  </div>
</div>
  <script type="text/javascript">

	var scriptPlayerAuth = "'.($loggedIn ? $scriptPlayerAuth : '10').'";
	var settingsRefreshRate = "'.($loggedIn ? $loginRefreshTime : '5').'000";

	if (!(localStorage.getItem("notification_style") === null && localStorage.getItem("notification_message") === null)) {
		if(localStorage.getItem("notification_counter") == "0"){
			$.notify({icon: "tim-icons icon-bell-55",message: localStorage.getItem("notification_message")},{type: localStorage.getItem("notification_style"),timer: 2000 ,placement: {from: "top",align: "center"}});
		 	console.log("unload");
		  localStorage.removeItem("notification_message");
			localStorage.removeItem("notification_style");
			localStorage.removeItem("notification_counter");
		}
		else {
			var num = localStorage.getItem("notification_counter"),
			num = parseInt(num, 10);
			num--;
			localStorage.setItem("notification_counter", num);
		}
	}

  </script>
<script type="text/javascript" src="assets/js/monitor.js"></script>
<script type="text/javascript" src="assets/js/validator.js"></script>';

	if(isset($_GET['showToken']) && $_GET['showToken'] == '1'){
		echo '
			<script>
				$(\'#publicLink\').modal(\'show\');
			</script>';
	}
	?>
</body>

</html>
