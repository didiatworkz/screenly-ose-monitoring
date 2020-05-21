<?php
	session_set_cookie_params(36000, '/' );
	session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="description" content="Manage all Screenly players in one place." />
	<meta name="author" content="didiatworkz" />
	<title>Screenly OSE Monitoring</title>
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
	<?php require_once('_functions.php'); ?>
  <div class="wrapper">
    <div class="main-panel">
	<?php
		if($loggedIn){
			if(isset($_POST['saveSettings']) && getGroupID($loginUserID) == 1){
				$refreshscreen = $_POST['refreshscreen'];
				$duration			 = $_POST['duration'];
		    $end_date 		 = $_POST['end_date'];

				if($duration && $end_date){
					$db->exec("UPDATE settings SET end_date='".$end_date."', duration='".$duration."', refreshscreen='".$refreshscreen."' WHERE userID='".$loginUserID."'");
					sysinfo('success', 'Settings saved!', 0);
				}	else sysinfo('danger', 'Error!');
				redirect($backLink, 2);
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
	        sysinfo('success', 'Token generated! - wait....', 0);
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
					sysinfo('success', $name.' added successfully');
				}	else sysinfo('danger', 'Error! - Can \'t add the Player');
				redirect($backLink, 2);
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
					sysinfo('success', 'Player successfully updated!');
				}	else sysinfo('danger', 'Error! - Can \'t update the Player');
				redirect($backLink, 2);
			}

			// GET: action:delete - Delete player from database
			if(isset($_GET['action']) && $_GET['action'] == 'delete'){
				$playerID = $_GET['playerID'];

				if(isset($playerID)){
					$db->exec("DELETE FROM player WHERE playerID='".$playerID."'");
					sysinfo('success', 'Player successfully removed!');
				} else sysinfo('danger', 'Error! - Can \'t remove the Player');
				redirect($backLink, 2);
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
						redirect($backLink, 0);
					}	else sysinfo('danger', 'Error! - Can \'t delete the Asset');
				}
			}

			// POST: updateAsset - Update Asset information from a player via API
			if(isset($_POST['updateAsset'])){
				$id 				= $_POST['id'];
				$asset 			= $_POST['asset'];
				$name 			= $_POST['name'];
				$start 			= date("Y-m-d", strtotime($_POST['start_date']));
				$start_time	= $_POST['start_time'];
				$end 				= date("Y-m-d", strtotime($_POST['end_date']));
				$end_time		= $_POST['end_time'];
				$duration 	= $_POST['duration'];

				$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$id."'");
				$player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
				$data 			= callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, false, $id, false);

				if($data['name'] != $name) $data['name'] = $name;

				if($data['duration'] != $duration && $duration > 1) $data['duration'] = $duration;
				else $data['duration'] = 30;
				$data['start_date'] = $start.'T'.$start_time.':00.000Z';
				$data['end_date'] = $end.'T'.$end_time.':00.000Z';

				if(callURL('PUT', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, $data, $id, false)){
					sysinfo('success', 'Asset updated successfully');
				}	else sysinfo('danger', 'Error! - Can \'t update the Asset');
			  redirect($backLink, 2);
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
					redirect($backLink, 0);
				} else sysinfo('danger', 'Error! - Can \'t delete the Asset');
			}

			// INCLUDE: Top menubar
			include('assets/php/menu.php');

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

					$player['name'] != '' ? $playerName = $player['name'] : $playerName = 'Unkown Name';
					$player['location'] != '' ? $playerLocation = $player['location'] : $playerLocation = '';

					if(checkAddress($player['address'].'/api/'.$apiVersion.'/assets')){
						$playerAPI = callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets', false, $playerID, false);
						$db->exec("UPDATE player SET sync='".time()."' WHERE playerID='".$playerID."'");
						$monitor	 = checkAddress($player['address'].':9020/screen/screenshot.png');
						$playerAPICall = TRUE;

						if($monitor == true){
							$monitorInfo = '<span class="badge badge-success">  installed  </span>';
						} else $monitorInfo = '<a href="#" title="What does that mean?"><span class="badge badge-info">not installed</span></a>';

						$status		 		= 'online';
						$statusColor 	= 'success';
						$newAsset			= '<a href="#" data-toggle="modal" data-target="#newAsset" class="btn btn-success btn-sm btn-block"><i class="tim-icons icon-simple-add"></i> New Asset</a>';
						$bulkDelete		= '<a href="#" data-toggle="modal" data-target="#confirmDeleteAssets" data-href="index.php?action=view&playerID=21&action2=deleteAllAssets&playerID='.$player['playerID'].'" class="btn btn-block btn-danger" title="delete"><i class="tim-icons icon-simple-remove"></i> Clean Assets</a>';
						$navigation 	= '<div class="row"><div class="col-xs-12 col-md-6 mb-2"><button data-playerID="'.$player['playerID'].'" data-order="previous" class="changeAsset btn btn-sm btn-block btn-info" title="Previous asset"><i class="tim-icons icon-double-left"></i> Asset</button></div> <div class="col-xs-12 col-md-6 mb-2"> <button data-playerID="'.$player['playerID'].'" data-order="next" class="changeAsset btn btn-sm btn-block btn-info" title="Next asset">Asset <i class="tim-icons icon-double-right"></i></button></div></div>';
						$management		= '<a href="http://'.$player['address'].'" target="_blank" class="btn btn-primary btn-block"><i class="tim-icons icon-spaceship"></i> Open Player Management</a>';
						$reboot				= '<button data-playerid="'.$player['playerID'].'" class="btn btn-block btn-info reboot" title="Reboot Player"><i class="tim-icons icon-refresh-01"></i> Reboot Player</button>';
						$script 			= '
						<tr>
							<td>Monitor-Script:</td>
							<td>'.$monitorInfo.'</td>
						</tr>
						';
					}
					else {
						$playerAPICall 	= FALSE;
						$playerAPI 			= NULL;
						$status 				= 'offline';
						$statusColor 		= 'danger';
						$navigation 		= '';
						$script 				= '';
						$newAsset				= '';
						$bulkDelete			= '';
						$management			= '';
						$reboot 				= '';

						if(checkAddress($player['address'])){
							$status		 		= 'online';
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
													<a href="#" data-playerid="'.$player['playerID'].'" class="dropdown-item editPlayerOpen" title="edit">Edit</a>
													<a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="index.php?action=delete&playerID='.$player['playerID'].'" class="dropdown-item" title="delete">Delete</a>
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
													<td>Status:</td>
													<td><span class="badge badge-'.$statusColor.'">'.$status.'</span></td>
												</tr>
												<tr>
													<td>IP Address:</td>
													<td>'.$player['address'].'</td>
												</tr>
												<tr>
													<td>Location:</td>
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
										  <h5 class="title">Assets</h5>
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
												<th data-priority="1">Name</th>
												<th data-priority="3">Status</th>
												<th>Date</th>
												<th class="d-none">Show</th>
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
							$end							= date('d.m.Y', strtotime($endAsset['0']));
							$end_date					= date('Y-m-d', strtotime($endAsset['0']));
							$end_time					= $endAssetTimeHM['0'].':'.$endAssetTimeHM['1'];
							$yes 							= '<span class="badge badge-success m-2" data-asset_id="'.$playerAPI[$i]['asset_id'].'">  active  </span>';
							$no 							= '<span class="badge badge-danger m-2" data-asset_id="'.$playerAPI[$i]['asset_id'].'">  inactive  </span>';
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
								$shown = 'Shown';
								$shown_class = '';
							} else {
								$shown = 'Hidden';
								$shown_class = 'class="asset-hidden"';
							}
							echo '
											<tr id="'.$playerAPI[$i]['asset_id'].'" data-playerID="'.$player['playerID'].'"'.$shown_class.'>
												<td>'.$player['playerID'].'</td>
												<td>'.$mimetypeIcon.' '.$playerAPI[$i]['name'].'</td>
												<td>'.$active.'</td>
												<td><span class="d-block d-sm-none"><br /></span>Start: '.$start.'<br />End:&nbsp;&nbsp;&nbsp;'.$end.'</td>
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
				            <span><b> No Screenly API detected! - </b> No data could be collected...</span>
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
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<ul class="nav nav-tabs" role="tablist">
									  <li class="nav-item">
									    <a class="nav-link active" href="#url" role="tab" data-toggle="tab">URL</a>
									  </li>
									  <li class="nav-item">
									    <a class="nav-link" href="#upload" role="tab" data-toggle="tab">Upload</a>
									  </li>
									</ul>

									<div class="tab-content">
									  <div role="tabpanel" class="tab-pane active" id="url">
											<form id="assetNewForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST">
												<div class="form-group">
													<label for="InputNewAssetUrl">Asset URL</label>
													<input name="url" type="text" pattern="^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&\'\(\)\*\+,;=.]+$" class="form-control" id="InputNewAssetUrl" placeholder="http://www.example.com" autofocus>
												</div>
												<div class="form-group text-right">
													<input name="id" type="hidden" value="'.$player['playerID'].'" />
													<input name="mimetype" type="hidden" value="webpage" />
													<input name="newAsset" type="hidden" value="1" />
													<button type="submit" name="saveAsset" class="btn btn-success btn-sm">Send</button>
													<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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
												<button type="button" class="btn btn-secondary btn-sm close_modal" data-close="#newAsset">Close</button>
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
									<h5 class="modal-title" id="editAssetModalLabel">Edit Asset</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<form id="assetEditForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST">
										<div class="form-group">
											<label for="InputAssetName">Name</label>
											<input name="name" type="text" class="form-control" id="InputAssetName" placeholder="Name" value="Name" />
										</div>
										<div class="form-group">
											<label for="InputAssetUrl">URL</label>
											<input name="name" type="text" class="form-control" id="InputAssetUrl" disabled="disabled" value="url" />
										</div>
										<div class="form-group">
											<label for="InputAssetStart">Start</label>
											<input name="start_date" type="date" class="form-control" id="InputAssetStart" placeholder="Start-Date" value="'.date('Y-m-d', strtotime('now')).'" />
											<input name="start_time" type="time" class="form-control" id="InputAssetStartTime" placeholder="Start-Time" value="12:00" />
										</div>
										<div class="form-group">
											<label for="InputAssetEnd">End</label>
											<input name="end_date" type="date" class="form-control" id="InputAssetEnd" placeholder="End-Date" value="'.date('Y-m-d', strtotime('+1 week')).'" />
											<input name="end_time" type="time" class="form-control" id="InputAssetEndTime" placeholder="End-Time" value="12:00" />
										</div>
										<div class="form-group">
											<label for="InputAssetDuration">Duration in sec.</label>
											<input name="duration" type="number" class="form-control" id="InputAssetDuration" value="30" />
										</div>
										<div class="form-group text-right">
											<input name="updateAsset" type="hidden" value="1" />
											<input name="asset" id="InputAssetId"type="hidden" value="1" />
											<input name="id" id="InputSubmitId" type="hidden" value="'.$player['playerID'].'" />
											<button type="submit" class="btn btn-warning btn-sm">Update</button>
											<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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
									<h5 class="modal-title">Attention!</h5>
								</div>
								<div class="modal-body">
									Do you really want to reboot the Player now?
									<div class="form-group text-right">
										<button class="exec_reboot btn btn-sm btn-danger" title="Reboot now">Reboot now</button>
										<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
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
									<h5 class="modal-title">Attention!</h5>
								</div>
								<div class="modal-body">
									Do you really want to clean all assets on this player?
									<div class="form-group text-right">
										<a class="btn btn-danger btn-ok btn-sm">Delete</a>
										<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				';
				}
				else {
					sysinfo('danger', 'No Player submitted!');
					redirect('index.php', 3);
				}
			}
			else if(isset($_GET['site'])){
				$moduleName = $_GET['site'];

				if (@file_get_contents('assets/php/'.$moduleName.'.php', 0, NULL, 0, 1)) {
					if(in_array(basename($moduleName), $_modules)){
						include('assets/php/'.basename($moduleName).'.php');
					}	else sysinfo('danger', 'Module not allowed');
				}	else sysinfo('danger', 'Module not exits');
			}
			else {
				$playerSQL = $db->query("SELECT * FROM player ORDER BY name ASC");

				if($playerCount > 0){
					echo'
				<div class="row">
					';
					while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
						if($player['name'] == ''){
							$name	 		= 'No Player Name';
							$imageTag = 'No Player Name '.$player['playerID'];
						}
						else {
							$name 		= $player['name'];
							$imageTag = $player['name'];
						}
						echo'
					<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
						<div class="card">
							<div class="card-header">
								<h4 class="d-inline">'.$name.'</h4>
								<div class="dropdown d-inline pull-right">
									<button type="button" class="btn btn-link dropdown-toggle btn-icon" data-toggle="dropdown">
										<i class="tim-icons icon-settings-gear-63"></i>
									</button>
									<div class="dropdown-menu dropdown-menu-right dropdown-black" aria-labelledby="dropdownMenuLink">
										<a href="index.php?action=view&playerID='.$player['playerID'].'" class="dropdown-item" title="view"><i class="tim-icons icon-tablet-2"></i> details</a>
										<a href="#" data-playerid="'.$player['playerID'].'" class="dropdown-item editPlayerOpen" title="edit"><i class="tim-icons icon-pencil"></i> edit</a>
										<a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="index.php?action=delete&playerID='.$player['playerID'].'" class="dropdown-item" title="delete"><i class="tim-icons icon-trash-simple"></i> delete</a>
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
					if(firstStart() == 1){
						echo '
						<div class="row">
							<div class="col-sm-8 offset-sm-2">
								<div class="card">
									<div class="card-header ">
										<div class="row">
											<div class="col-sm-12 text-left">
												<h2 class="card-title">First Setup Wizard</h2>
												<h4 class="text-right">Step 1</h4>
											</div>
										</div>
									</div>
									<div class="card-body">
										<p class="lead">Thank you for using Screenly OSE Monitoring.<br />
												To get started, you need to change your username and password.
										</p>
										<hr />
										<form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
											<div class="form-group">
												<label for="InputUsername">Change Username</label>
												<input name="username" type="text" class="form-control" id="InputUsername" placeholder="New Username" autofocus required />
												<div class="help-block with-errors"></div>
											</div>
											<div class="form-group">
												<label for="InputPassword1">Change Password</label>
												<input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="New Password" required />
											</div>
											<div class="form-group">
												<input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="Confirm Password" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" required />
												<div class="help-block with-errors"></div>
											</div>
											<div class="form-group">
												<br />
												<input name="mode" type="hidden" value="firstStep"/>
												<button type="submit" name="saveAccount" class="btn btn-primary btn-lg btn-block">Next Step</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						';
					}
					else if(firstStart() == 2 && checkAddress($_SERVER['SERVER_ADDR'])){
						firstStart('set', 3);
						echo '
						<div class="row">
							<div class="col-sm-8 offset-sm-2">
								<div class="card">
									<div class="card-header ">
										<div class="row">
											<div class="col-sm-12 text-left">
												<h2 class="card-title">First Setup Wizard</h2>
												<h4 class="text-right">Step 2</h4>
											</div>
										</div>
									</div>
									<div class="card-body">
										<p class="lead">Do you want to add this Screenly OSE Player to your monitoring?</p>
										<hr />
										<form id="playerForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
											<div class="form-group">
												<label for="InputPlayerName">Enter the Screenly Player name</label>
												<input name="name" type="text" class="form-control" id="InputPlayerName" placeholder="Player-Name" autofocus />
											</div>
											<div class="form-group">
												<label for="InputLocation">Enter the Player location</label>
												<input name="location" type="text" class="form-control" id="InputLocation" placeholder="Player-Location" />
											</div>
											<hr />
											<div class="form-group">
												<input name="address" type="hidden" id="InputAdress" value="'.$_SERVER['SERVER_ADDR'].'" />
												<button type="submit" name="saveIP" class="btn btn-primary btn-lg btn-block">Yes</button>
												<button type="text" onClick="window.location.reload();" class="btn btn-danger btn-lg btn-block">No</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						';
					}
					else {
						echo '
					<div class="row">
						<div class="col-sm-8 offset-sm-2">
							<div class="card">
								<div class="card-header ">
									<div class="row">
										<div class="col-sm-12 text-left">
											<h2 class="card-title">Welcome</h2>
										</div>
									</div>
								</div>
								<div class="card-body">
									<p class="lead">With Screenly OSE Monitoring you can set up an unlimited number of players and manage them at a single screen. <br />
										Additionally there is the possibility to install addons on the players to get even more information in Screenly OSE Monitoring.<br />
										<br />
										Add your first Screenly OSE Player and discover how easy it can be to work with.
									</p>
									<br />
									<a href="#" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#newPlayer">Add your first Screenly OSE Player</a>
								</div>
							</div>
						</div>
					</div>
					';
					}
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
						<h5 class="modal-title" id="newPlayerModalLabel">Add Player</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" href="#manual" role="tab" data-toggle="tab">Manual</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#auto" role="tab" data-toggle="tab">Auto</a>
							</li>
						</ul>

						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="manual">
								<form id="playerForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
									<div class="form-group">
										<label for="InputPlayerName">Enter the Screenly Player name</label>
										<input name="name" type="text" class="form-control" id="InputPlayerName" placeholder="Player-Name" autofocus />
									</div>
									<div class="form-group">
										<label for="InputLocation">Enter the Player location</label>
										<input name="location" type="text" class="form-control" id="InputLocation" placeholder="Player-Location" />
									</div>
									<div class="form-group">
										<label for="InputAdress">Enter the IP address of the Screenly Player</label>
										<input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="No valid IPv4 address" type="text" class="form-control" id="InputAdress" placeholder="192.168.1.100" required />
										<div class="help-block with-errors"></div>
									</div>
									<hr />
									<div class="form-group">
										<label for="InputUser">Player authentication </label>
										<input name="user" type="text" class="form-control" id="InputUser" placeholder="Username" />
									</div>
									<div class="form-group">
										<input name="pass" type="password" class="form-control" id="InputPassword" placeholder="Password" />
									</div>
									<div class="form-group text-right">
										<button type="submit" name="saveIP" class="btn btn-success btn-sm">Save</button>
										<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
									</div>
								</form>
							</div>
							<div role="tabpanel" class="tab-pane" id="auto">
								<form id="newPlayerDiscover" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
									<div class="form-group">
										<label for="InputCIDR">Enter the IP Range</label>
										<input name="range" pattern="^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(3[0-2]|[1-2][0-9]|[0-9]))$" data-error="No valid IPv4 address with CIDR" type="text" class="form-control" id="InputCIDR" placeholder="192.168.1.0/24" required />
										<div class="help-block with-errors"></div>
									</div>
									<div class="form-group">
										<label for="discoverStatus">Status</label>
										<hr />
										<div id="discoverStatus"></div>
									</div>
									<div class="form-group text-right">
										<input name="userID" type="hidden" value="'.$loginUserID.'" />
										<button type="submit" name="startDiscover" class="btn btn-primary btn-sm start_discovery">Discover</button>
										<button type="button" class="btn btn-secondary btn-sm close_modal" data-close="#newPlayer">Close</button>
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
						<h5 class="modal-title" id="editPlayerModalLabel">Edit <span id="playerNameTitle"></span></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form id="playerFormEdit" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
							<div class="form-group">
								<label for="InputPlayerNameEdit">Enter the Screenly Player name</label>
								<input name="name" type="text" class="form-control" id="InputPlayerNameEdit" placeholder="Player-Name" autofocus />
							</div>
							<div class="form-group">
								<label for="InputLocationEdit">Enter the Player location</label>
								<input name="location" type="text" class="form-control" id="InputLocationEdit" placeholder="Player-Location" />
							</div>
							<div class="form-group">
								<label for="InputAdressEdit">Enter the IP address of the Screenly Player</label>
								<input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="No valid IPv4 address" type="text" class="form-control" id="InputAdressEdit" placeholder="192.168.1.100" required />
								<div class="help-block with-errors"></div>
							</div>
							<hr />
							<div class="form-group">
								<label for="InputUserEdit">Player authentication </label>
								<input name="user" type="text" class="form-control" id="InputUserEdit" placeholder="Username" />
							</div>
							<div class="form-group">
								<input name="pass" type="password" class="form-control" id="InputPasswordEdit" placeholder="Password" />
							</div>
							<div class="form-group text-right">
								<input name="playerID" id="playerIDEdit" type="hidden" value="" />
								<input name="mimetype" id="playerMimetype" type="hidden" value="" />
								<button type="submit" name="updatePlayer" class="btn btn-sm btn-warning">Update</button>
								<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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
		        <h5 class="modal-title">Account</h5>
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
								<label for="InputFirstname">Firstname</label>
								<input name="firstname" type="text" class="form-control" id="InputFirstname" placeholder="John" value="'.$loginFirstname.'" />
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group">
								<label for="InputName">Name</label>
								<input name="name" type="text" class="form-control" id="InputName" placeholder="Doe" value="'.$loginName.'" />
								<div class="help-block with-errors"></div>
							</div>
							<hr />
							<div class="form-group">
								<label for="InputUsername">Change Username</label>
								<input name="username" type="text" class="form-control" id="InputUsername" placeholder="New Username" value="'.$loginUsername.'" />
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group">
								<label for="InputPassword1">Change Password</label>
								<input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="New Password" />
							</div>
							<div class="form-group">
								<input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="Confirm Password" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" />
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group text-right">
								<button type="submit" name="saveAccount" class="btn btn-sm btn-primary">Update</button>
								<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
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
						<h5 class="modal-title" id="newAddonModalLabel">Addon</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<img src="assets/img/addon.png" class="img-fluid mx-auto d-block" alt="addon" style="height: 180px" />
						The Screenly OSE Monitoring addon allows you to retrieve even more data from the Screenly Player and process it in the monitor. <br />
						You have the possibility to get a "live" image of the player\'s output.<br /><br />
						To install, you have to log in to the respective Screenly Player via SSH (How it works: <a href="https://www.raspberrypi.org/documentation/remote-access/ssh/" target="_blank">here</a>) <br />and execute this command:<br />
						<input type="text" class="form-control" id="InputBash" onClick="this.select();" value="bash <(curl -sL https://git.io/fjg5e)">
						After that the player restarts and the addon has been installed.<br />
						<button type="button" class="btn btn-secondary btn-sm pull-right" data-dismiss="modal">Close</button>
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
			        <h5 class="modal-title">Settings</h5>
			      </div>
						<div class="modal-body">
								<form id="settingsForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
									<div class="form-group">
										<label for="InputSetRefresh">Refresh time for Screenshot add-on</label>
										<input name="refreshscreen" type="text" class="form-control" id="InputSetRefresh" placeholder="5" value="'.$loginRefreshTime.'" required />
									</div>
									<div class="form-group">
										<label for="InputSetDuration">Default Duration for Assets</label>
										<input name="duration" type="text" class="form-control" id="InputSetDuration" placeholder="30" value="'.$set['duration'].'" required />
									</div>
									<div class="form-group">
										<label for="InputSetEndDate">Delay of weeks for the end date</label>
										<input name="end_date" type="text" class="form-control" id="InputSetEndDate" placeholder="1" value="'.$set['end_date'].'" required />
									</div>
									<div class="form-group text-right">
										<button type="submit" name="saveSettings" class="btn btn-primary btn-sm">Update</button>
										<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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
		        <h5 class="modal-title">Screenly OSE Monitor</h5>
		      </div>
					<div class="modal-body">
					  <a href="https://atworkz.de" target="_blank"><img src="assets/img/atworkz-logo.png" class="img-fluid mx-auto d-block" /></a>
						<table class="table table-sm">
						  <tr>
						    <td>Monitor Version:</td>
						    <td>'.$systemVersion.'</td>
						  </tr>
							<tr>
						    <td>Used Screenly API:</td>
						    <td>'.$apiVersion.'</td>
						  </tr>
						  <tr>
						    <td>Server IP:</td>
						    <td>'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'</td>
						  </tr>
							<tr>
						    <td>PHP Version:</td>
						    <td>'.phpversion().'</td>
						  </tr>
							<tr>
						    <td>&nbsp;</td>
						    <td>&nbsp;</td>
						  </tr>
						  <tr>
						    <td>Project:</td>
						    <td><a href="https://github.com/didiatworkz/screenly-ose-monitor" target="_blank">GitHub</a></td>
						  </tr>
							<tr>
						    <td>Copyright:</td>
						    <td><a href="https://atworkz.de" target="_blank">atworkz.de</a></td>
						  </tr>
						  <tr>
						    <td>Design:</td>
						    <td><a href="https://github.com/creativetimofficial/black-dashboard" target="_blank">Black Dashboard</a></td>
						  </tr>
						  <tr>
						    <td>Scripts:</td>
						    <td>
							  <a href="https://datatables.net" target="_blank">DataTables</a><br />
							  <a href="https://www.dropzonejs.com/" target="_blank">dropzoneJS</a>
							</td>
						  </tr>
						</table>
						<button type="button" class="btn btn-sm btn-secondary pull-right" data-dismiss="modal">Close</button>
	        </div>
				</div>
			</div>
		</div>

		<!-- publicLink -->
		<div class="modal fade" id="publicLink" tabindex="-1" role="dialog" aria-labelledby="publicLinkModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Public Link</h5>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="InputSetToken">Public link that can be used without authentication!</label>
							<input type="text" class="form-control" id="InputSetToken" onClick="this.select();" value="http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/index.php?monitoring=1&key='.$set['token'].'" />
						</div>
						<div class="form-group text-right">
							<a href="index.php?generateToken=yes" class="btn btn-info btn-sm">Generate new token</a>
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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
						<h5 class="modal-title">Attention!</h5>
					</div>
					<div class="modal-body">
						Do you really want to delete this entry?
						<div class="form-group text-right">
							<a class="btn btn-danger btn-ok btn-sm">Delete</a>
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
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
				sysinfo('success', '<i class="fa fa-check"></i> You have been successfully logged out.');
			}
			echo '
				<div class="content">
					<div class="col-xs-12 col-md-4 offset-md-4 text-center p-3 mb-5">
						<h2>Sceenly OSE Monitoring</h2>
						<p>Please log in</p>
						<form id="Login" action="'.$_SERVER['PHP_SELF'].'" method="POST">
							<div class="form-group">
								<input name="user" type="text" class="form-control" placeholder="Username" autofocus>
							</div>
							<div class="form-group">
								<input name="password" type="password" class="form-control" placeholder="Password">
							</div>
							<button type="submit" name="Login" class="btn btn-primary btn-block" value="1">Login</button>
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
			&copy '.date('Y'); ?> by <a href="https://www.atworkz.de" target="_blank">atworkz.de</a>  <?php if(!(isset($_GET['monitoring']) OR !$loggedIn)) echo '|  <a href="https://www.github.com/didiatworkz" target="_blank">Github</a> | <a href="javascript:void(0)" data-toggle="modal" data-target="#info">Information</a>';
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
