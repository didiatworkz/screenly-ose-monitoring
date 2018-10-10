<?php
session_set_cookie_params(36000, '/' );
session_start();
require_once("_config.php");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="Manage all Screenly players in one place.">
		<meta name="author" content="didiatworkz">
		<title>Screenly OSE Monitor</title>
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="assets/css/sm.css" rel="stylesheet">
		<link href="assets/css/all.min.css" rel="stylesheet">
		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/jquery.toaster.js"></script>
		<script src="assets/js/validator.js"></script>
		<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png">
		<link rel="manifest" href="assets/img/site.webmanifest">
		<link rel="mask-icon" href="assets/img/safari-pinned-tab.svg" color="#343a40">
		<link rel="shortcut icon" href="assets/img/favicon.ico">
		<meta name="apple-mobile-web-app-title" content="Screenly OSE Monitor">
		<meta name="application-name" content="Screenly OSE Monitor">
		<meta name="msapplication-TileColor" content="#eeeeee">
		<meta name="msapplication-config" content="assets/img/browserconfig.xml">
		<meta name="theme-color" content="#eeeeee">
	</head>

	<body>

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

		if(isset($_GET['action']) && $_GET['action'] == 'order'){
			if(isset($_GET['playerID'])){
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
				redirect('index.php?action=view&playerID='.$playerID, 1);
			}
			else {
				sysinfo('danger', 'No Player submitted!');
				redirect('index.php', 3);
			}
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
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
			<div class="container">
				<a class="navbar-brand" href="index.php">Screenly OSE Monitor</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarResponsive">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
							<a class="nav-link" href="#" onclick="location.reload(true); return false;"><i class="fas fa-sync"></i></a>
						</li>
						'.(update($systemVersion) == 1 ? ' 
						<li class="nav-item bg-warning">
							<a class="nav-link text-secondary" href="https://github.com/didiatworkz/screenly-ose-monitor" target="_blank"><i class="fas fa-external-link-alt"></i> Update available</a>
						</li>' : '').'
						<li class="nav-item">
							<a class="nav-link" href="#" data-toggle="modal" data-target="#newPlayer"><i class="fa fa-plus"></i> Add Player</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#" data-toggle="modal" data-target="#settings"><i class="fa fa-cog"></i> Settings</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="index.php?action=logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
			';
		if(isset($_GET['action']) && $_GET['action'] == 'view'){
			if(isset($_GET['playerID'])){
				$playerID 	= $_GET['playerID'];
				$playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$playerID."'");
				$player 	= $playerSQL->fetchArray(SQLITE3_ASSOC);
				$monitor 	= 0;
				
				$player['name'] != '' ? $playerName = $player['name'] : $playerName = 'No Player Name';
				$player['location'] != '' ? $playerLocation = '<p><i class="fas fa-map-marker"></i> '.$player['location'].'</p>' : $playerLocation = '';
				$player['player_user'] != '' ? $user = $player['player_user'] : $user = false;
				$player['player_password'] != '' ? $pass = $player['player_password'] : $pass = false;

				if(pingAddress($player['address'])){
					$playerAPI = callURL('GET', $player['address'].'/api/v1.1/assets', false, $user, $pass, false);
					$db->exec("UPDATE player SET sync='".time()."' WHERE playerID='".$playerID."'");
					$monitor = callURL('GET', $player['address'].'/static/monitor.txt', false, $user, $pass, false);
					
					if($monitor == 1){
						$monitorInfo = '<span class="badge badge-success">  installed  </span>';
					} else $monitorInfo = '<a href="index.php?action=extension&playerID='.$player['playerID'].'" title="What does that mean?"><span class="badge badge-info">not installed</span></a>';
					
					$status		 	= 'online';
					$statusColor 	= 'success';
					$navigation 	= '<li class="list-group-item"><div class="row"><div class="col-xs-12 col-md-6"><a href="index.php?action=order&playerID='.$player['playerID'].'&orderD=previous" class="btn btn-block btn-sm btn-info"><i class="fas fa-angle-double-left"></i> Previous asset</a></div> <div class="col-xs-12 col-md-6"> <a href="index.php?action=order&playerID='.$player['playerID'].'&orderD=next" class="btn btn-block btn-sm btn-info">Next asset <i class="fas fa-angle-double-right"></i></a></div></div></li>';
					$script 		= '<li class="list-group-item">Monitor-Script: '.$monitorInfo.'</li>';
					$assets 		= '<li class="list-group-item">Assets: '.sizeof($playerAPI).'</li>';
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
		<div class="container mb-5">
			<header class="jumbotron my-4 text-center">
				<h1 class="display-5"><i class="fas fa-hashtag"></i> '.$playerName.'</h1>
				'.$playerLocation.'
			</header>
			<div class="row">
				<div class="col-lg-8">
					<div class="card mb-4 shadow-sm">
						<div class="card-body">
							<ul class="list-group">
								<li class="list-group-item">Status: <span class="badge badge-'.$statusColor.'">'.$status.'</span></li>
								<li class="list-group-item">IP Address: '.$player['address'].'</li>
								'.$script.'
								'.$assets.'
								'.$navigation.'
								<li class="list-group-item"><a href="http://'.$player['address'].'" target="_blank" class="btn btn-primary btn-block"><i class="fas fa-external-link-alt"></i> Open Player management</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card mb-4 shadow-sm">
						<div class="card-body">
							<img class="card-img player" src="'.monitorScript($player['address']).'" alt="player">
							<hr />
							<a href="index.php" class="btn btn-secondary btn-block"><i class="fas fa-arrow-left"></i> back</a>
						</div>
					</div>
				</div>
			</div>
			<hr />
			<div class="row">
						';
						if($status == 'online'){
							for($i=0; $i < sizeof($playerAPI); $i++)  {
								$start_date	= date('d.m.Y H:m:s', strtotime($playerAPI[$i]['start_date']));
								$end_date 	= date('d.m.Y H:m:s', strtotime($playerAPI[$i]['end_date']));
								$yes 		= '<span class="badge badge-success">  Yes  </span>';
								$no 		= '<span class="badge badge-danger">  No  </span>';
								
								$playerAPI[$i]['is_enabled'] == 1 ? $enable = $yes : $enable = $no;
								$playerAPI[$i]['is_active'] == 1 ? $active = $yes : $active = $no;
								
								if($playerAPI[$i]['mimetype'] == 'webpage'){
									$mimetypeIcon = '<i class="fas fa-globe fa-10x"></i>';
									$mimetypeOutput = '<li class="list-group-item">URL: '.$playerAPI[$i]['uri'].'</li>';
								}
								else if($playerAPI[$i]['mimetype'] == 'video'){
									$mimetypeIcon = '<i class="fas fa-video fa-10x"></i>';
									$mimetypeOutput = '';
								}
								else {
									$mimetypeIcon = '<i class="fas fa-image fa-10x"></i>';
									$mimetypeOutput = '';
								}
								echo '
								<div class="col-lg-4 col-md-4 mb-4">
									<div class="card shadow-sm">
									<div class="card-header text-center">'.$mimetypeIcon.'</div>
										<div class="card-body">
											<h4 class="card-title">'.$playerAPI[$i]['name'].'</h4>
											<ul class="list-group list-group-flush">
												<li class="list-group-item">Start: '.$start_date.'</li>
												<li class="list-group-item">End: '.$end_date.'</li>
												<li class="list-group-item">Type: '.$playerAPI[$i]['mimetype'].'</li>
												'.$mimetypeOutput.'
												<li class="list-group-item">Enable: '.$enable.'</li>
												<li class="list-group-item">Active: '.$active.'</li>
												<li class="list-group-item">Duration: '.$playerAPI[$i]['duration'].'</li>
											</ul>
										</div>
									</div>
								</div>
								';
							}
						}
				echo '

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
					<div class="col-lg-8">
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
					<div class="col-lg-4">
						<div class="card mb-4 shadow-sm">
							<div class="card-body">
								<img class="card-img player" src="'.monitorScript($player['address']).'" alt="player">
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
								<kbd>curl -sL http://'.$_SERVER['SERVER_ADDR'].':9000/assets/tools/extension.sh | sudo bash</kbd>
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

			echo '
		<div class="container">

			';
			if($playerCount['counter'] > 0){
				echo'
			<header class="jumbotron my-4 text-center">
				<h1 class="display-4">Screenly OSE Monitor</h1>
				<p class="lead">Manage all Screenly players in one place.</p>
			</header>

			<div class="row text-center">
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
					<div class="col-xl-3 col-lg-4 col-md-6 mb-4">
						<div class="card">
							<a href="index.php?action=view&playerID='.$player['playerID'].'"><img class="card-img-top player" src="'.monitorScript($player['address']).'" alt="'.$imageTag.'"></a>
							<div class="card-body">
								<h4 class="card-title">'.$name.'</h4>
								<p class="card-text">'.$player['address'].'</p>
							</div>
							<div class="card-footer">
								<a href="index.php?action=view&playerID='.$player['playerID'].'" class="btn btn-primary" title="view"><i class="far fa-eye"></i></a> <a href="index.php?action=edit&playerID='.$player['playerID'].'" class="btn btn-warning" title="edit"><i class="far fa-edit"></i></a> <a href="index.php?action=delete&playerID='.$player['playerID'].'" class="btn btn-danger" title="delete"><i class="far fa-trash-alt"></i></a>
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
				<header class="jumbotron my-4 text-center">
					<h1 class="display-4">Screenly OSE Monitor</h1>
					<p class="lead">Welcome to Screenly OSE Monitor.<br />First of all you need to add a player.</p>
					<a href="#" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#newPlayer">Add a new Player</a>
				</header>
			';
			}
			echo'
		</div>
		';
		}
		echo '
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
			<div class="container">
				<div class="col-xs-12 col-md-6 offset-md-3 text-center p-3 mb-5">
							<h2>Sceenly OSE Monitor</h2>
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
		<script>
			setInterval("reloadPlayerImage();",5000);
			function reloadPlayerImage(){
				$('img.player').each(function(){
					var url = $(this).attr('src').split('?')[0];
					$(this).attr('src', url + '?' + Math.random());
				})
			}
			$('.modal').on('shown.bs.modal', function(){
				$(this).find('[autofocus]').focus();
			});
		</script>
		<footer class="py-5 bg-dark">
			<div class="container">
				<p class="m-0 text-center text-white">&copy; <?php echo date('Y') ?> by <a href="https://www.atworkz.de" target="_blank">atworkz.de</a>  |  <a href="https://www.github.com/didiatworkz" target="_blank"><i class="fab fa-github"></i></a></p>
			</div>
		</footer>
	</body>
</html>
