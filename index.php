<?php
session_set_cookie_params(36000, '/' );
session_start();
require_once("_config.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Manage all Screenly players in one place.">
	<meta name="author" content="didiatworkz">
	<title>
		Screenly OSE Monitoring
	</title>
	<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png">
	<link rel="manifest" href="assets/img/site.webmanifest">
	<link rel="mask-icon" href="assets/img/safari-pinned-tab.svg" color="#1e1e2f">
	<link rel="shortcut icon" href="assets/img/favicon.ico">
	<meta name="msapplication-TileColor" content="#1e1e2f">
	<meta name="msapplication-config" content="assets/img/browserconfig.xml">
	<meta name="theme-color" content="#1e1e2f">
	<link href="assets/css/fonts.css" rel="stylesheet" />
	<link href="assets/css/nucleo-icons.css" rel="stylesheet" />
	<link href="assets/css/black-dashboard.css?v=1.0.0" rel="stylesheet" />
	<link rel="stylesheet" href="assets/tools/DataTables/datatables.min.css"/>
	<link href="assets/css/monitor.css" rel="stylesheet" />
	<script src="assets/js/core/jquery.min.js"></script>
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>
	<script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
	<script src="assets/js/plugins/bootstrap-notify.js"></script>
	<script src="assets/js/black-dashboard.min.js?v=1.0.0"></script>
	<script src="assets/tools/DataTables/datatables.min.js"></script>
</head>

<body>
  <div class="wrapper">
    <div class="main-panel">
	<?php
	if(isset($_POST['Login']) && md5($_POST['passwort']) == $loginPassword && $_POST['user'] == $loginUsername){
		$_SESSION['user'] 		= $_POST['user'];
		$_SESSION['passwort'] 	= $loginPassword;
	}

	if(isset($_GET['action']) && $_GET['action'] == 'logout'){
		if(session_destroy()){
			$logedout = true;
			$_SESSION['passwort'] = '';
		}
		else $logedout = false;
	}

	if(isset($_SESSION['passwort']) AND $_SESSION['passwort'] == $loginPassword && $_SESSION['user'] == $loginUsername){

		if(isset($_POST['saveAccount'])){
			$user = $_POST['username'];
			if($_POST['password2'] !== ''){
				$pass = md5($_POST['password2']);
			}
			else $pass = $set['password'];

			if($user AND $pass){
				$db->exec("UPDATE settings SET username='".$user."', password='".$pass."' WHERE userID='".$loginUserID."'");
				sysinfo('success', 'Account data saved!', 0);
			}
			else sysinfo('danger', 'Error!');
		}

    if(isset($_POST['saveSettings'])){
			$duration = $_POST['duration'];
	    $end_date = $_POST['end_date'];

			if($duration AND $end_date){
				$db->exec("UPDATE settings SET end_date='".$end_date."', duration='".$duration."' WHERE userID='".$loginUserID."'");
				sysinfo('success', 'Settings saved!', 0);
			}
			else sysinfo('danger', 'Error!');
		}

    if(isset($_GET['generateToken']) && $_GET['generateToken'] == 'yes'){
      $now = time();
      $token = md5($loginUsername.$loginPassword.$now);
      if($token){
        $db->exec("UPDATE settings SET token='".$token."' WHERE userID='".$loginUserID."'");
        sysinfo('success', 'Token generated! - wait....', 0);
        redirect('index.php');
      }
      else sysinfo('danger', 'Error!');
    }

		if(isset($_POST['saveIP'])){
			$name 		= $_POST['name'];
			$address 	= $_POST['address'];
			$location = $_POST['location'];
			$user 		= $_POST['user'];
			$pass 		= $_POST['pass'];

			if($address){
				$db->exec("INSERT INTO player (name, address, location, player_user, player_password, userID) values('".$name."', '".$address."', '".$location."', '".$user."', '".$pass."', '".$loginUserID."')");
				sysinfo('success', 'Player added successfully');
			} else sysinfo('danger', 'Error! - Can \'t add the Player');
		}

		if(isset($_POST['updatePlayer'])){
			$name 		= $_POST['name'];
			$address	= $_POST['address'];
			$location 	= $_POST['location'];
			$user 		= $_POST['user'];
			$pass 		= $_POST['pass'];
			$playerID 	= $_POST['playerID'];

			if($address){
				$db->exec("UPDATE player SET name='".$name."', address='".$address."', location='".$location."', player_user='".$user."', player_password='".$pass."' WHERE playerID='".$playerID."'");
				sysinfo('success', 'Player successfully updated!');
			} else sysinfo('danger', 'Error! - Can \'t update the Player');
		}

		if(isset($_GET['action']) && $_GET['action'] == 'delete'){
			$playerID = $_GET['playerID'];
			if(isset($playerID)){
				$db->exec("DELETE FROM player WHERE playerID='".$playerID."'");
				sysinfo('success', 'Player successfully removed!');
			}
			else sysinfo('danger', 'Error! - Can \'t remove the Player');
		}

		if(isset($_POST['saveAsset'])){
			$id 		= $_POST['id'];
			$url 		= $_POST['url'];
			$start 		= date("Y-m-d", strtotime($_POST['start_date']));
			$start_time	= $_POST['start_time'];
			$end 		= date("Y-m-d", strtotime($_POST['end_date']));
			$end_time	= $_POST['end_time'];
			$duration 	= $_POST['duration'];
			$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$id."'");
			$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);
			$player['player_user'] != '' ? $user = $player['player_user'] : $user = false;
			$player['player_password'] != '' ? $pass = $player['player_password'] : $pass = false;
			$data 		= array();
			$data['mimetype'] = 'webpage';
			$data['is_enabled'] = 1;
			$data['name'] = $url;
			$data['start_date'] = $start.'T'.$start_time.':00.000Z';
			$data['end_date'] = $end.'T'.$end_time.':00.000Z';
			$data['duration'] = $duration;
			$data['play_order'] = 0;
			$data['nocache'] = 0;
			$data['uri'] = $url;
			$data['skip_asset_check'] = 1;

			if(callURL('POST', $player['address'].'/api/'.$apiVersion.'/assets', $data, $user, $pass, false)){
				sysinfo('success', 'Asset added successfully');
			} else sysinfo('danger', 'Error! - Can \'t add the Asset');
		}

		if(isset($_POST['updateAsset'])){
			$id 		= $_POST['id'];
			$asset 		= $_POST['asset'];
			$name 		= $_POST['name'];
			$start 		= date("Y-m-d", strtotime($_POST['start_date']));
			$start_time	= $_POST['start_time'];
			$end 		= date("Y-m-d", strtotime($_POST['end_date']));
			$end_time	= $_POST['end_time'];
			$duration 	= $_POST['duration'];

			$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$id."'");
			$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);
			$player['player_user'] != '' ? $user = $player['player_user'] : $user = false;
			$player['player_password'] != '' ? $pass = $player['player_password'] : $pass = false;
			$data = callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, false, $user, $pass, false);

			($data['name'] != $name) ? $data['name'] = $name : NULL;
			($data['duration'] != $duration) ? $data['duration'] = $duration : NULL;
			$data['start_date'] = $start.'T'.$start_time.':00.000Z';
			$data['end_date'] = $end.'T'.$end_time.':00.000Z';

			if(callURL('PUT', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, $data, $user, $pass, false)){
				sysinfo('success', 'Asset updated successfully');
			} else sysinfo('danger', 'Error! - Can \'t update the Asset');
		}

		if((isset($_GET['action2']) && $_GET['action2'] == 'deleteAsset')){
			$id 		= $_GET['id'];
			$asset 		= $_GET['asset'];
			$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$id."'");
			$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);
			$player['player_user'] != '' ? $user = $player['player_user'] : $user = false;
			$player['player_password'] != '' ? $pass = $player['player_password'] : $pass = false;

			if(callURL('DELETE', $player['address'].'/api/'.$apiVersion.'/assets/'.$asset, $data, $user, $pass, false)){
				sysinfo('success', 'Asset deleted successfully');
			} else sysinfo('danger', 'Error! - Can \'t delete the Asset');
		}
		
		echo'

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
        <div class="container-fluid">
			<div class="navbar-wrapper">
				<a class="navbar-brand" href="./index.php">Screenyl OSE Monitoring</a>
			</div>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-bar bar1"></span>
				<span class="navbar-toggler-bar bar2"></span>
				<span class="navbar-toggler-bar bar3"></span>
			</button>
			<div class="collapse navbar-collapse" id="navigation">
				<ul class="navbar-nav ml-auto">
					'.$update.'
					<li class="nav-item">
						<a href="'.$_SERVER['REQUEST_URI'].'" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Refresh">
							<i class="tim-icons icon-refresh-02"></i>
							<p class="d-lg-none">
								Refresh
							</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="javascript:void(0)" data-toggle="modal" data-target="#newPlayer" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Add player">
							<i class="tim-icons icon-simple-add"></i>
							<p class="d-lg-none">
								Add player
							</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="javascript:void(0)" data-toggle="modal" data-target="#extension" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Extension">
							<i class="tim-icons icon-puzzle-10"></i>
							<p class="d-lg-none">
								Extension
							</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="javascript:void(0)" data-toggle="modal" data-target="#settings" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Settings">
							<i class="tim-icons icon-settings"></i>
							<p class="d-lg-none">
								Settings
							</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="index.php?action=logout" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Logout">
							<i class="tim-icons icon-key-25"></i>
							<p class="d-lg-none">
								Logout
							</p>
						</a>
					</li>
					<li class="separator d-lg-none"></li>
				</ul>
			</div>
        </div>
    </nav>
    <!-- End Navbar -->
	
	<div class="content">
			';
		if(isset($_GET['action']) && $_GET['action'] == 'view'){
			if(isset($_GET['playerID'])){
				if(isset($_GET['set']) && $_GET['set'] == 'order'){
					$playerID 	= $_GET['playerID'];
					$orderD 	= $_GET['orderD'];
					$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$playerID."'");
					$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);

					$player['player_user'] != '' ? $user = $player['player_user'] : $user = false;
					$player['player_password'] != '' ? $pass = $player['player_password'] : $pass = false;
					$result = callURL('GET', $player['address'].'/api/v1/assets/control/'.$orderD.'', false, $user, $pass, false);
					$db->exec("UPDATE player SET sync='".time()."' WHERE playerID='".$playerID."'");
					if($result == 'Asset switched') sysinfo('success', 'Asset switchted!');
					else sysinfo('danger', 'Switch not possible!');
				}
				$playerID 	= $_GET['playerID'];
				$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$playerID."'");
				$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);
				$monitor 	= 0;

				$player['name'] != '' ? $playerName = $player['name'] : $playerName = 'Unkown Name';
				$player['location'] != '' ? $playerLocation = $player['location'] : $playerLocation = '';
				$player['player_user'] != '' ? $user = $player['player_user'] : $user = false;
				$player['player_password'] != '' ? $pass = $player['player_password'] : $pass = false;

				if(checkAddress($player['address'])){
					$playerAPI = callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets', false, $user, $pass, false);
					$db->exec("UPDATE player SET sync='".time()."' WHERE playerID='".$playerID."'");
					$monitor = callURL('GET', $player['address'].':9020/monitor.txt', false, $user, $pass, false);

					if($monitor == 1){
						$monitorInfo = '<span class="badge badge-success">  installed  </span>';
					} else $monitorInfo = '<a href="#" data-toggle="modal" data-target="#extension" title="What does that mean?"><span class="badge badge-info">not installed</span></a>';

					$status		 	= 'online';
					$statusColor 	= 'success';
					$navigation 	= '<div class="row"><div class="col-xs-12 col-md-6"><a href="index.php?action=view&set=order&playerID='.$player['playerID'].'&orderD=previous" class="btn btn-sm btn-block btn-info" title="Previous asset"><i class="tim-icons icon-double-left"></i> Asset</a></div> <div class="col-xs-12 col-md-6"> <a href="index.php?action=view&set=order&playerID='.$player['playerID'].'&orderD=next" class="btn btn-sm btn-block btn-info" title="Next asset">Asset <i class="tim-icons icon-double-right"></i></a></div></div>';
					$script 		= '
					<tr>
						<td>Monitor-Script:</td>
						<td>'.$monitorInfo.'</td>
					</tr>
					';
					$assets 		= '
					<tr>
						<td>Assets:</td>
						<td>'.sizeof($playerAPI).'</td>
					</tr>';
				}
				else {
					$playerAPI 		= NULL;
					$status 		= 'offline';
					$statusColor 	= 'danger';
					$navigation 	= '';
					$script 		= '';
					$assets 		= '';
				}

				echo '
				<div class="row">
					<div class="col-xl-9 col-lg-8 col-md-7">
						<div class="card">
							<div class="card-header">
								<h5 class="title">Assets</h5>
							</div>
						<div class="card-body">
                ';
				if($status == 'online'){
					echo '
						<table class="table" id="assets">
							<thead class="text-primary">
								<tr>
									<th>Name</th>
									<th>Date</th>
									<th>Status</th>
									<th>Options</th>
								</tr>
							</thead>
							<tbody>
                      ';
					for($i=0; $i < sizeof($playerAPI); $i++)  {
						$start			= date('d.m.Y', strtotime($playerAPI[$i]['start_date']));
						$start_date		= date('Y-m-d', strtotime($playerAPI[$i]['start_date']));
						$start_time		= date('H:m', strtotime($playerAPI[$i]['start_date']));
						$end 			= date('d.m.Y', strtotime($playerAPI[$i]['end_date']));
						$end_date 		= date('Y-m-d', strtotime($playerAPI[$i]['end_date']));
						$end_time 		= date('H:m', strtotime($playerAPI[$i]['end_date']));
						$default_start 	= date("Y-m-d", time());
						$default_end 	= date("Y-m-d", strtotime("+".$set['end_date']." week"));

						$yes 			= '<span class="badge badge-success" data-asset_id="'.$playerAPI[$i]['asset_id'].'">  active  </span>';
						$no 			= '<span class="badge badge-danger" data-asset_id="'.$playerAPI[$i]['asset_id'].'">  inactive  </span>';

						$playerAPI[$i]['is_active'] == 1 ? $active = $yes : $active = $no;

						if($playerAPI[$i]['mimetype'] == 'webpage'){
							$mimetypeIcon = '<i class="tim-icons icon-world"></i>';
						}
						else if($playerAPI[$i]['mimetype'] == 'video'){
							$mimetypeIcon = '<i class="tim-icons icon-video-66"></i>';
						}
						else {
							$mimetypeIcon = '<i class="tim-icons icon-image-02"></i>';
						}
						echo '
								<tr>
									<td>'.$mimetypeIcon.' '.$playerAPI[$i]['name'].'</td>
									<td>Start: '.$start.'<br />End: '.$end.'</td>
									<td>'.$active.'</td>
									<td>
										<button class="changeState btn btn-info btn-sm" data-asset_id="'.$playerAPI[$i]['asset_id'].'" data-player_id="'.$player['playerID'].'" title="switch on/off"><i class="tim-icons icon-button-power"></i></button>
										<button class="options btn btn-warning btn-sm" data-asset="'.$playerAPI[$i]['asset_id'].'" data-player_id="'.$player['playerID'].'" data-name="'.$playerAPI[$i]['name'].'" data-start-date="'.$start_date.'" data-start-time="'.$start_time.'" data-end-date="'.$end_date.'" data-end-time="'.$end_time.'" data-duration="'.$playerAPI[$i]['duration'].'" data-uri="'.$playerAPI[$i]['uri'].'" title="edit"><i class="tim-icons icon-pencil"></i></button>
										<a href="index.php?action=view&playerID='.$player['playerID'].'&action2=deleteAsset&id='.$player['playerID'].'&asset='.$playerAPI[$i]['asset_id'].'" class="btn btn-danger btn-sm" title="delete"><i class="tim-icons icon-simple-remove"></i></a>
									</td>
								</tr>
						';
					}
					echo '
							</tbody>
						</table>
					';
				}
				echo '
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-5">
					<div class="card card-user">
						<div class="card-body">
							<p class="card-text">
								<div class="author">
									<div class="block block-monitor"></div>
									<img class="img-fluid player" src="'.monitorScript($player['address']).'" alt="'.$playerName.'">
									<h3 class="mt-3">'.$playerName.'</h3>
								</div>
							</p>
							<div class="card-description">
								<table class="table tablesorter " id="">
									<tbody>
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
										'.$assets.'
									</tbody>
								</table>
								<hr />
								<a href="#" data-toggle="modal" data-target="#newAsset" class="btn btn-success btn-block"><i class="tim-icons icon-simple-add"></i> New Asset</a>
								'.$navigation.'
								<hr />
								<a href="http://'.$player['address'].'" target="_blank" class="btn btn-primary btn-block"><i class="tim-icons icon-components"></i> Open Player Management</a>
								<a href="index.php?action=edit&playerID='.$player['playerID'].'" class="btn btn-warning btn-block" title="edit"><i class="tim-icons icon-pencil"></i> Edit</a>
								<a href="index.php?action=delete&playerID='.$player['playerID'].'" class="btn btn-danger btn-block" title="delete"><i class="tim-icons icon-trash-simple"></i> Delete</a>
							</div>
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
							<form id="assetNewForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST">
								<div class="form-group">
									<label for="InputNewAssetUrl">URL</label>
									<input name="url" type="text" pattern="^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&\'\(\)\*\+,;=.]+$" class="form-control" id="InputNewAssetUrl" placeholder="http://www.example.com" autofocus>
								</div>
								<div class="form-group">
									<label for="InputNewStart">Start</label>
									<input name="start_date" type="date" class="form-control" id="InputNewStart" placeholder="Start-Date" value="'.$default_start.'">
									<input name="start_time" type="time" class="form-control" id="InputNewStartTime" placeholder="Start-Time" value="12:00">
								</div>
								<div class="form-group">
									<label for="InputNewEnd">End</label>
									<input name="end_date" type="date" class="form-control" id="InputNewEnd" placeholder="End-Date" value="'.$default_end.'">
									<input name="end_time" type="time" class="form-control" id="InputNewEndTime" placeholder="End-Time" value="12:00">
								</div>
								<div class="form-group">
									<label for="InputNewDuration">Duration in sec.</label>
									<input name="duration" type="number" class="form-control" id="InputNewDuration" value="'.$set['duration'].'">
								</div>
								<div class="form-group text-right">
									<input name="id" type="hidden" value="'.$player['playerID'].'">
									<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
									<button type="submit" name="saveAsset" class="btn btn-success btn-sm">Send</button>
								</div>
							</form>
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
									<input name="name" type="text" class="form-control" id="InputAssetName" placeholder="Name" value="Name">
								</div>
								<div class="form-group">
									<label for="InputAssetUrl">URL</label>
									<input name="name" type="text" class="form-control" id="InputAssetUrl" disabled="disabled" value="url">
								</div>
								<div class="form-group">
									<label for="InputAssetStart">Start</label>
									<input name="start_date" type="date" class="form-control" id="InputAssetStart" placeholder="Start-Date" value="01.01.1970">
									<input name="start_time" type="time" class="form-control" id="InputAssetStartTime" placeholder="Start-Time" value="12:00">
								</div>
								<div class="form-group">
									<label for="InputAssetEnd">End</label>
									<input name="end_date" type="date" class="form-control" id="InputAssetEnd" placeholder="End-Date" value="01.01.1970">
									<input name="end_time" type="time" class="form-control" id="InputAssetEndTime" placeholder="End-Time" value="12:00">
								</div>
								<div class="form-group">
									<label for="InputAssetDuration">Duration in sec.</label>
									<input name="duration" type="number" class="form-control" id="InputAssetDuration" value="30">
								</div>
								<div class="form-group text-right">
									<input name="asset" id="InputAssetId" type="hidden" value="1">
									<input name="id" id="InputAssetId" type="hidden" value="'.$player['playerID'].'">
									<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
									<button type="submit" name="updateAsset" class="btn btn-warning btn-sm">Update</button>
								</div>
							</form>
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
		else if(isset($_GET['action']) && $_GET['action'] == 'edit'){
			if(isset($_GET['playerID'])){
				$playerID 	= $_GET['playerID'];
				$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$playerID."'");
				$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);
				$action		= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
				echo '
			<div class="container mb-5">
				<header class="jumbotron my-4 bg-warning">
					<h2 class="h2_edit">Edit Player: '.$player['name'].'</h2>
				</header>
				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-body">
								<form id="playerForm" action="'.$action.'" method="POST" data-toggle="validator">
									<div class="form-group">
										<label for="InputName">Player name</label>
										<input name="name" type="text" class="form-control" id="InputName" value="'.$player['name'].'" placeholder="Player-Name">
									</div>
									<div class="form-group">
										<label for="InputLocation">Player location</label>
										<input name="location" type="text" class="form-control" id="InputLocation" value="'.$player['location'].'" placeholder="Player-Location">
									</div>
									<div class="form-group">
										<label for="InputAdress">IP address</label>
										<input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="No valid IPv4 address" type="text"  value="'.$player['address'].'" class="form-control" id="InputAdress" placeholder="192.168.1.100" required>
										<div class="help-block with-errors"></div>
									</div>
									<hr />
									<div class="form-group">
										<label for="InputUser">Player authentication </label>
										<input name="user" type="text" class="form-control" id="InputUser" value="'.$player['player_user'].'" placeholder="Username">
									</div>
									<div class="form-group">
										<input name="pass" type="password" class="form-control" id="InputPassword" value="'.$player['player_password'].'" placeholder="Password">
									</div>
									<div class="form-group text-right">
										<input name="playerID" type="hidden" value="'.$_GET['playerID'].'">
										<a href="'.$action.'" class="btn btn-secondary btn-sm">Close</a>
										<button type="submit" name="updatePlayer" class="btn btn-sm btn-warning">Update</button>
									</div>
								</form>
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
		else {
			$playerSQL 		= $db->query("SELECT * FROM player ORDER BY name");
			$playerCount 	= $db->query("SELECT COUNT(*) AS counter FROM player");
			$playerCount 	= $playerCount->fetchArray(SQLITE3_ASSOC);

			if($playerCount['counter'] > 0){
				echo'
			<div class="row">
				';
				while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
					if($player['name'] == ''){
						$name	 	= 'No Player Name';
						$imageTag 	= 'No Player Name '.$player['playerID'];
					}
					else {
						$name 		= $player['name'];
						$imageTag 	= $player['name'];
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
								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
									<a href="index.php?action=view&playerID='.$player['playerID'].'" class="dropdown-item" title="view"><i class="tim-icons icon-tablet-2"></i> details</a>
									<a href="index.php?action=edit&playerID='.$player['playerID'].'" class="dropdown-item" title="edit"><i class="tim-icons icon-pencil"></i> edit</a>
									<a href="index.php?action=delete&playerID='.$player['playerID'].'" class="dropdown-item" title="delete"><i class="tim-icons icon-trash-simple"></i> delete</a>
								</div>
							</div>
							<h5 class="card-category">'.$player['address'].'</h5>
						</div>
						<div class="card-body ">
							<a href="index.php?action=view&playerID='.$player['playerID'].'"><img class="player" src="'.monitorScript($player['address']).'" alt="'.$imageTag.'"></a>
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
								Additionally there is the possibility to install extensions on the players to get even more information in Screenly OSE Monitoring.<br />
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
		echo '

	</div> <!-- END CONTENT -->
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
					<form id="playerForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
						<div class="form-group">
							<label for="InputPlayerName">Enter the Screenly Player name</label>
							<input name="name" type="text" class="form-control" id="InputPlayerName" placeholder="Player-Name" autofocus>
						</div>
						<div class="form-group">
							<label for="InputLocation">Enter the Player location</label>
							<input name="location" type="text" class="form-control" id="InputLocation" placeholder="Player-Location">
						</div>
						<div class="form-group">
							<label for="InputAdress">Enter the IP address of the Screenly Player</label>
							<input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="No valid IPv4 address" type="text" class="form-control" id="InputAdress" placeholder="192.168.1.100" required>
							<div class="help-block with-errors"></div>
						</div>
						<hr />
						<div class="form-group">
							<label for="InputUser">Player authentication </label>
							<input name="user" type="text" class="form-control" id="InputUser" placeholder="Username">
						</div>
						<div class="form-group">
							<input name="pass" type="password" class="form-control" id="InputPassword" placeholder="Password">
						</div>
						<div class="form-group text-right">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<button type="submit" name="saveIP" class="btn btn-success btn-sm">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- extension -->
	<div class="modal fade" id="extension" tabindex="-1" role="dialog" aria-labelledby="newExtensionModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="newExtensionModalLabel">Extension</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<img src="assets/img/extension.png" class="img-fluid mx-auto d-block" alt="extension" style="height: 180px" />
					The Screenly OSE Monitoring extension allows you to retrieve even more data from the Screenly Player and process it in the monitor. <br />
					You have the possibility to get a "live" image of the player\'s output.<br /><br />
					To install, you have to log in to the respective Screenly Player via SSH (How it works: <a href="https://www.raspberrypi.org/documentation/remote-access/ssh/" target="_blank">here</a>) <br />and execute this command:<br />
					<input type="text" class="form-control" id="InputBash" onClick="this.select();" value="bash <(curl -sL http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/assets/tools/extension.sh)">
					After that the player restarts and the extension has been installed.<br />
					<button type="button" class="btn btn-secondary btn-sm pull-right" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<!-- settings -->
	<div class="modal fade" id="settings" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-3 col-md-4">
							<ul class="nav nav-pills nav-pills-primary nav-pills-icons flex-column">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#setting">
										Settings
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#account">
										Account
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#info">
										Info
									</a>
								</li>
							</ul>
						</div>
						<div class="col-md-8">
							<div class="tab-content">
								<div class="tab-pane active" id="setting">
									<form id="settingsForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
										<div class="form-group">
											<label for="InputSetDuration">Default Duration for Assets</label>
											<input name="duration" type="text" class="form-control" id="InputSetDuration" placeholder="30" value="'.$set['duration'].'" required>
										</div>
										<div class="form-group">
											<label for="InputSetEndDate">Delay of weeks for the end date</label>
											<input name="end_date" type="text" class="form-control" id="InputSetEndDate" placeholder="1" value="'.$set['end_date'].'" required>
										</div>
										<div class="form-group">
											<label for="InputSetToken">Monitoring URL</label>
											<input type="text" class="form-control" id="InputSetDuration" onClick="this.select();" value="http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/index.php?monitoring=yes&key='.$set['token'].'">
										</div>
										<div class="form-group text-right">
											<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
											<a href="index.php?generateToken=yes" class="btn btn-info btn-sm">Generate Key</a>
											<button type="submit" name="saveSettings" class="btn btn-primary btn-sm">Update</button>
										</div>
									</form>
								</div>
								<div class="tab-pane" id="account">
									<form id="accountForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
										<div class="form-group">
											<label for="InputUsername">Change Username</label>
											<input name="username" type="text" class="form-control" id="InputUsername" placeholder="New Username" value="'.$set['username'].'" required>
											<div class="help-block with-errors"></div>
										</div>
										<div class="form-group">
											<label for="InputPassword1">Change Password</label>
											<input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="New Password" required>
										</div>
										<div class="form-group">
											<input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="Confirm Password" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" required>
											<div class="help-block with-errors"></div>
										</div>
										<div class="form-group text-right">
											<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
											<button type="submit" name="saveAccount" class="btn btn-sm btn-primary">Update</button>
										</div>
									</form>
								</div>
								<div class="tab-pane" id="info">
									<h2>Screenly OSE Monitor</h2>
									Version '.$systemVersion.' <br />
									Server IP: '.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'<br />
									Project: <a href="https://github.com/didiatworkz/screenly-ose-monitor" target="_blank">GitHub</a><br />
									Design: <a href="https://github.com/creativetimofficial/black-dashboard" target="_blank">Black Dashboard</a><br />
									Copyright: <a href="https://atworkz.de" target="_blank">atworkz.de</a><br />
									<br />
									<br />
									<button type="button" class="btn btn-sm btn-secondary pull-right" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
                </div>
			</div>
		</div>
	</div>
		';
	}
  else if((isset($_GET['monitoring']) && $_GET['monitoring'] == 'yes') && isset($_GET['key'])){
    $key 		= $_GET['key'];
    echo '
    <nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
        <div class="container-fluid">
			     <div class="navbar-wrapper">
				       <a class="navbar-brand" href="./index.php">Screenyl OSE Monitoring</a>
			     </div>
        </div>
    </nav>
    <div class="content">';
    $playerSQL 		= $db->query("SELECT * FROM player ORDER BY name");
    $playerCount 	= $db->query("SELECT COUNT(*) AS counter FROM player");
    $playerCount 	= $playerCount->fetchArray(SQLITE3_ASSOC);
    header("refresh:100;url=".$_SERVER['REQUEST_URI']);
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if($playerCount['counter'] > 0 && $key == $securityToken){
      echo'
    <div class="row">
      ';
      while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
        if($player['name'] == ''){
          $name	 		= 'No Player Name';
          $imageTag 	= 'No Player Name '.$player['playerID'];
        }
        else {
          $name 		= $player['name'];
          $imageTag 	= $player['name'];
        }
        echo'
		<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
			<div class="card">
				<div class="card-header">
					<h4 class="d-inline">'.$name.'</h4>
					<h5>'.$player['address'].'</h5>
				</div>
				<div class="card-body card-monitor">
					<img class="player" src="'.monitorScript($player['address']).'" alt="'.$imageTag.'">
				</div>
			</div>
		</div>
        ';
      }
      echo '
    </div>
    ';
    }
    else sysinfo('danger', 'Token incorrect - Access denied!');


  }
	else {
		if (isset($logedout)){
			sysinfo('success', '<i class="fa fa-check"></i> You have been successfully logged out.');
		}
		if(isset($_POST['Login'])){
			sysinfo('danger', 'The entered login data are not correct!');
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
							<input name="passwort" type="password" class="form-control" placeholder="Password">
						</div>
						<button type="submit" name="Login" class="btn btn-primary btn-block">Login</button>
					</form>
				</div>
			</div>
		';
	}
	$db->close();
?>
      <footer class="footer">
        <div class="container-fluid">
          <div class="copyright">
            &copy; <?php echo date('Y') ?> by <a href="https://www.atworkz.de" target="_blank">atworkz.de</a>  |  <a href="https://www.github.com/didiatworkz" target="_blank">Github</a>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <script>
	$(function () {
	  $('[data-tooltip="tooltip"]').tooltip();
	  $("[data-tooltip=tooltip]").hover(function(){
		$('.tooltip').css('top',parseInt($('.tooltip').css('left')) + 10 + 'px')
	  });
	});
	$( ".changeState" ).on('click', function() {
	  var asset = $(this).data("asset_id");
	  var id = $(this).data("player_id");
	  var changeAssetState = 1;
	  $.ajax({
		url: "_config.php",
		type: "POST",
		data: {asset: asset, id: id, changeAssetState: changeAssetState},
		success: function(data){
			$("span[data-asset_id='"+asset+"'").toggle(function() {
				$(this).toggleClass("badge-success badge-danger").show();
				if($(this).hasClass("badge-danger")) $(this).text("inactive");
				else $(this).text("active");
			});
			$.notify({icon: "tim-icons icon-bell-55",message: "Asset status changed"},{type: "success",timer: 1000,placement: {from: "top",align: "center"}});
		},
		error: function(data){
			$.notify({icon: "tim-icons icon-bell-55",message: "Error! - Can \'t change the Asset"},{type: "danger",timer: 1000,placement: {from: "top",align: "center"}});
		}
	  });
	});
	$('#assets').DataTable({
		"order": [[ 2, "asc" ]],
	});
	var eA = $('#editAsset');
    $('button.options').on('click', function(){
        eA.find('#InputAssetName').val($(this).data("name"));
        eA.find('#InputAssetUrl').val($(this).data("uri"));
        eA.find('#InputAssetStart').val($(this).data("start-date"));
        eA.find('#InputAssetStartTime').val($(this).data("start-time"));
        eA.find('#InputAssetEnd').val($(this).data("end-date"));
        eA.find('#InputAssetEndTime').val($(this).data("end-time"));
        eA.find('#InputAssetDuration').val($(this).data("duration"));
        eA.find('#InputAssetId').val($(this).data("asset"));
        eA.modal('show');
        return false;
    });
	$(function(){ 
     var navMain = $(".navbar-collapse"); // avoid dependency on #id
     // "a:not([data-toggle])" - to avoid issues caused
     // when you have dropdown inside navbar
     navMain.on("click", "[data-toggle]", null, function () {
         navMain.collapse('hide');
     });
 });
  </script>
  <script>

	function reloadPlayerImage(){
		$('img.player').each(function(){
			var url = $(this).attr('src').split('?')[0];
			$(this).attr('src', url + '?' + Math.random());
		})
	}
	setInterval("reloadPlayerImage();",5000);
	$('.modal').on('shown.bs.modal', function(){
		$(this).find('[autofocus]').focus();
	});

  </script>
</body>

</html>
