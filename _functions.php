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

	$_DEBUG 		= 'NO';
	$apiVersion	= 'v1.2';

	$_modules = array(
			'addon',
			'dashboard',
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

	$backLink			= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
	$firstSetup 	= 0;
	$loadingImage = 'assets/img/spinner.gif';

	$_loadMessureStart = array_sum(explode(' ',  microtime()));

	if(isset($_GET['site'])){
		$site = $_GET['site'];
	} else $site = NULL;

	include_once('assets/php/database.php');

	session_set_cookie_params($set['sessionTime'], '/' );
	session_name('somo_session');
	session_start();

	include_once('assets/php/functions.php');
	include_once('assets/php/user.php');
	include_once('assets/php/curl.php');
	include_once('assets/php/deviceInfo.php');
	include_once('assets/php/player.php');
	include_once('assets/php/update.php');
	include_once('assets/php/actions.php');

	$runnerTime = getRunnerTime();

	date_default_timezone_set($set['timezone']);

	if($set['name'] != 'SOMO'){
		define('_SYSTEM_NAME', $set['name'].' - SOMO');
	}
	else define('_SYSTEM_NAME', $set['name']);

	if($set['design'] == 1) $body_theme = ' theme-dark';
	else $body_theme = '';
