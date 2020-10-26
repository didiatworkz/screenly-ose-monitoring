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
    Version 4.0  -  November 2020
_______________________________________
*/

	$_DEBUG 		= 'YES';
	$_TIMEZONE 	= 'Europe/Berlin';
	$apiVersion	= 'v1.2';

	$_modules = array(
					'players',
					'settings',
					'tester',
					'usermanagement',
					'multiuploader',
	);


/* _______________________________ */

	if($_DEBUG == 'YES'){
		ini_set('display_errors', 1);
		error_reporting(E_ALL|E_STRICT);
	}
	else ini_set('display_errors', 0);

	date_default_timezone_set($_TIMEZONE);

	$backLink			= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
	$firstSetup 	= 0;
	$loadingImage = 'assets/img/spinner.gif';

	$_loadMessureStart = array_sum(explode(' ',  microtime()));

	if(isset($_GET['site'])){
		$site = $_GET['site'];
	} else $site = NULL;



	function redirect($url, $time = 0){
		echo '<meta http-equiv="refresh" content="'.$time.';URL='.$url.'">';
	}

	function sysinfo($style, $message, $refresh = false){
		echo '
		<script>
			localStorage.setItem("notification_style", "'.$style.'");
			localStorage.setItem("notification_message", "'.$message.'");
			localStorage.setItem("notification_counter", "1");
		</script>';
	}


	include_once('assets/php/database.php');
	include_once('assets/php/user.php');
	include_once('assets/php/player.php');
	include_once('assets/php/update.php');
	include_once('assets/php/actions.php');

	function firstStart($mode = 'get', $value = null){
		global $db;
		if($mode == 'set' AND $value != NULL){
			$db->exec("UPDATE `settings` SET firstStart='".$value."' WHERE settingsID=1");
			return true;
		}
		else {
			$SQL = $db->query("SELECT firstStart FROM settings");
			$fetch = $SQL->fetchArray(SQLITE3_ASSOC);
			return $fetch['firstStart'];
		}
	}

	if($loginUsername == 'demo' && $loginPassword == 'fe01ce2a7fbac8fafaed7c982a04e229'){
		setcookie('firstSetup', true, time() + (86400 * 999), '/');
		firstStart('set', 1);
	}
	else if (isset($_COOKIE['firstSetup']) && $playerCount == 0 && firstStart() <= 2) {
		firstStart('set', 2);
	}
	else if($playerCount >= 1 OR firstStart() == 3) {
		setcookie('firstSetup',  null, -1, '/');
	}

if($set['name'] != 'Screenly OSE Monitoring'){
	define('_SYSTEM_NAME', $set['name'].' - Screenly OSE Monitoring');
}
else define('_SYSTEM_NAME', $set['name']);
