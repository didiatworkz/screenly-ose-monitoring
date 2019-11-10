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
	________________________________________
			  Screenly OSE Monitor
		   Version 3.0 - November 2019
	________________________________________
	*/


	ini_set('display_errors',1);
	error_reporting(E_ALL|E_STRICT);


/* _______________________________ */
	include_once('assets/php/database.php');
	global $loggedIn;
	$logedout = FALSE;
	$firstSetup = 0;
	if(isset($_GET['site'])){
		$site = $_GET['site'];
	} else $site = NULL;

	function redirect($url, $time = 1){
		echo '<meta http-equiv="refresh" content="'.$time.';URL='.$url.'">';
	}

	if(isset($_POST['Login']) && md5($_POST['passwort']) == $loginPassword && $_POST['user'] == $loginUsername){
		$_SESSION['user'] 			= $_POST['user'];
		$_SESSION['passwort'] 	= $loginPassword;
		redirect('index.php');
	}

	if(isset($_GET['action']) && $_GET['action'] == 'logout'){
		if(session_destroy()){
			$logedout = true;
			$_SESSION['passwort'] = '';
		}
	}

	if(isset($_SESSION['passwort']) AND $_SESSION['passwort'] == $loginPassword && $_SESSION['user'] == $loginUsername) {
	  $loggedIn = TRUE;
	}
	else $loggedIn = FALSE;

	function sysinfo($status, $message, $refresh = false){
		echo '<script>$.notify({icon: "tim-icons icon-bell-55",message: "'.$message.'"},{type: "'.$status.'",timer: 1000,placement: {from: "top",align: "center"}});</script>';
		if($refresh) echo'<meta http-equiv="refresh" content="2;URL=index.php">';
	}

include_once('assets/php/player.php');
include_once('assets/php/update.php');
include_once('assets/php/actions.php');

if($loginUsername == 'demo' AND $loginPassword == 'fe01ce2a7fbac8fafaed7c982a04e229'){
	setcookie('firstSetup', true, time() + (86400 * 999), '/');
	$firstSetup = 1;
}
else if (isset($_COOKIE['firstSetup']) AND $playerCount == 0) {
	$firstSetup = 2;
}
else if($playerCount >= 1) {
	setcookie('firstSetup',  null, -1, '/');
}
