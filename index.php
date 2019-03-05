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
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <title>
    Screenly OSE Monitoring
  </title>
  <!--     Fonts and icons     -->
  <link href="assets/css/fonts.css" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link href="assets/css/black-dashboard.css?v=1.0.0" rel="stylesheet" />
  <link href="assets/css/monitor.css" rel="stylesheet" />
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

		if(isset($_POST['saveSettings'])){
			$user = $_POST['username'];
			if($_POST['password2'] !== ''){
				$pass = md5($_POST['password2']);
			}
			else $pass = $set['password'];

			if($user AND $pass){
				$db->exec("UPDATE settings SET username='".$user."', password='".$pass."' WHERE userID='".$loginUserID."'");
				sysinfo('success', 'Settings saved!', 0);
			}
			else sysinfo('danger', 'Error!');
		}

		if(isset($_POST['saveIP'])){
			$name 		= $_POST['name'];
			$address 	= $_POST['address'];
			$location 	= $_POST['location'];
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
					<li class="nav-item">
						<a href="javascript:void(0)" onclick="location.reload(true); return false;" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Refresh">
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

				if(ifOnline($player['address'])){
					$playerAPI = callURL('GET', $player['address'].'/api/v1.1/assets', false, $user, $pass, false);
					$db->exec("UPDATE player SET sync='".time()."' WHERE playerID='".$playerID."'");
					$monitor = callURL('GET', $player['address'].':9020/monitor.txt', false, $user, $pass, false);
					
					if($monitor == 1){
						$monitorInfo = '<span class="badge badge-success">  installed  </span>';
					} else $monitorInfo = '<a href="index.php?action=extension&playerID='.$player['playerID'].'" title="What does that mean?"><span class="badge badge-info">not installed</span></a>';
					
					$status		 	= 'online';
					$statusColor 	= 'success';
					$navigation 	= '<div class="row"><div class="col-xs-12 col-md-6"><a href="index.php?action=view&set=order&playerID='.$player['playerID'].'&orderD=previous" class="btn btn-block btn-sm btn-info"><i class="fas fa-angle-double-left"></i> Previous asset</a></div> <div class="col-xs-12 col-md-6"> <a href="index.php?action=view&set=order&playerID='.$player['playerID'].'&orderD=next" class="btn btn-block btn-sm btn-info">Next asset <i class="fas fa-angle-double-right"></i></a></div></div>';
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
						<table class="table">
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
						$start_date	= date('d.m.Y H:m:s', strtotime($playerAPI[$i]['start_date']));
						$end_date 	= date('d.m.Y H:m:s', strtotime($playerAPI[$i]['end_date']));
						$yes 		= '<span class="badge badge-success">  active  </span>';
						$no 		= '<span class="badge badge-danger">  inactive  </span>';
						
						$playerAPI[$i]['is_active'] == 1 ? $active = $yes : $active = $no;
						
						if($playerAPI[$i]['mimetype'] == 'webpage'){
							$mimetypeIcon = '<i class="fas fa-globe fa-10x"></i>';
							$mimetypeOutput = $playerAPI[$i]['uri'];
						}
						else if($playerAPI[$i]['mimetype'] == 'video'){
							$mimetypeIcon = '<i class="fas fa-video fa-10x"></i>';
							$mimetypeOutput = 'local';
						}
						else {
							$mimetypeIcon = '<i class="fas fa-image fa-10x"></i>';
							$mimetypeOutput = 'local';
						}
						echo '
								<!--<tr>
									<td>'.$playerAPI[$i]['name'].'</td>
									<td>Start: '.$start_date.'<br />End: '.$end_date.'</td>
									<td>'.$playerAPI[$i]['mimetype'].'</td>
									<td>'.$mimetypeOutput.'</td>
									<td>'.$active.'</td>
									<td>'.$playerAPI[$i]['duration'].'</td>
								</tr>-->
								<tr>
									<td>'.$playerAPI[$i]['name'].'</td>
									<td>Start: '.$start_date.'<br />End: '.$end_date.'</td>
									<td>'.$active.'</td>
									<td></td>
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
									<div class="block block-one"></div>
									<div class="block block-two"></div>
									<div class="block block-three"></div>
									<div class="block block-four"></div>
									<img class="img-fluid" src="'.monitorScript($player['address']).'" alt="player">
									<p class="description">
									  '.$playerName.'
									</p>
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
								'.$navigation.'
								<a href="http://'.$player['address'].'" target="_blank" class="btn btn-primary btn-block"><i class="tim-icons icon-components"></i> Open Player Management</a>
								<a href="index.php?action=edit&playerID='.$player['playerID'].'" class="btn btn-warning btn-block" title="edit"><i class="tim-icons icon-pencil"></i> Edit</a> 
								<a href="index.php?action=delete&playerID='.$player['playerID'].'" class="btn btn-danger btn-block" title="delete"><i class="tim-icons icon-trash-simple"></i> Delete</a>
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
		else if(isset($_GET['action']) && $_GET['action'] == 'edit'){
			if(isset($_GET['playerID'])){
				$playerID 	= $_GET['playerID'];
				$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$playerID."'");
				$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);

				echo '
			<div class="container mb-5">
				<header class="jumbotron my-4 bg-warning">
					<h2>Edit Player: '.$player['name'].'</h2>
				</header>
				<div class="row">
					<div class="col-lg-12"> 
						<div class="card">
							<div class="card-body">
								<form id="playerForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
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
										<a href="index.php" class="btn btn-secondary">Close</a>
										<button type="submit" name="updatePlayer" class="btn btn-warning">Update</button>
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
		else if(isset($_GET['action']) && $_GET['action'] == 'extension'){
			if(isset($_GET['playerID'])){
				$playerID = $_GET['playerID'];
				echo '
			<div class="container mt-5 mb-5">
				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-body">
							<p>The Screenly OSE Monitor extension allows you to retrieve even more data from the Screenly Player and process it in the monitor. <br />
								You have the possibility to get a "live" image of the player\'s output. </p>
								<p>To install, you have to log in to the respective Screenly Player via SSH (How it works: here) and execute this command:</p>
								<kbd>bash <(curl -sL http://'.$_SERVER['SERVER_ADDR'].':9000/assets/tools/extension.sh)</kbd>
								<p>Then the player restarts and the extension has been installed.</p>
							</div>
							<div class="card-footer text-right">
								<a class="btn btn-secondary" href="index.php?action=view&playerID='.$playerID.'"><i class="fas fa-arrow-left"></i> back</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			';
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
				<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-4">
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
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							<button type="submit" name="saveIP" class="btn btn-success">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- settings -->
	<div class="modal fade" id="settings" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="settingsModalLabel">Settings</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="account-tab" data-toggle="tab" href="#account" role="tab" aria-controls="account" aria-selected="true">Account</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="false">Info</a>
						</li>
					</ul>
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active mt-3" id="account" role="tabpanel" aria-labelledby="account-tab">
							<form id="settingsForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
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
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" name="saveSettings" class="btn btn-primary">Update</button>
								</div>
							</form>
						</div>
						<div class="tab-pane fade mt-3" id="info" role="tabpanel" aria-labelledby="info-tab">
							<h2>Screenly OSE Monitor</h2>
							<p>Version '.$systemVersion.' '.(update($systemVersion) == 1 ? ' - <a href="https://github.com/didiatworkz/screenly-ose-monitor" target="_blank"><span class="badge badge-warning">Update available</span></a>' : '').'</p>
							<p>Server IP: '.$_SERVER['SERVER_ADDR'].':9000</p>
							<p>Support: <a href="https://github.com/didiatworkz/screenly-ose-monitor" target="_blank">GitHub</a></p>
						</div>
					</div>
					

				</div>
			</div>
		</div>
	</div>
		';
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
  <!--   Core JS Files   -->
  <script src="assets/js/core/jquery.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="assets/js/plugins/bootstrap-notify.js"></script>
  <script src="assets/js/black-dashboard.min.js?v=1.0.0"></script>
  <script>
	$(function () {
	  $('[data-tooltip="tooltip"]').tooltip();
	  $("[data-tooltip=tooltip]").hover(function(){
	$('.tooltip').css('top',parseInt($('.tooltip').css('left')) + 10 + 'px')
});
	})
  </script>
</body>

</html>