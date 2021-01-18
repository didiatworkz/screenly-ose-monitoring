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
    Version 4.0  -  January 2021
_______________________________________
*/

$apiVersion	= 'v1.2';

$_modules = array(
		'addon',
		'dashboard',
		'groupmanagement',
		'multiuploader',
		'players',
		'settings',
		'usermanagement',
);


/* _______________________________ */

$_loadMessureStart  = array_sum(explode(' ',  microtime()));
$backLink						= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
$firstSetup 				= 0;
$loadingImage 			= 'assets/img/spinner.gif';

if(isset($_GET['site'])){
	$site = $_GET['site'];
} else $site = NULL;

define('ROOT_DIR', realpath(__DIR__));
include_once('assets/php/database.php');

if($set['debug'] == 1){
	include_once('assets/php/error_handler.php');
	ini_set('display_errors', 1);
	set_error_handler('somo_error_handler');
	error_reporting(E_ALL|E_STRICT);
} else ini_set('display_errors', 0);

session_start();
setcookie(session_name('somo_session'),session_id(),time()+$set['sessionTime'], "/");
date_default_timezone_set($set['timezone']);

include_once('assets/php/functions.php');
include_once('assets/php/user.php');
include_once('assets/php/curl.php');
include_once('assets/php/deviceInfo.php');
include_once('assets/php/player.php');
include_once('assets/php/update.php');
include_once('assets/php/actions.php');

$runnerTime 		= getRunnerTime();
$uploadMaxSize 	= $set['uploadMaxSize'];
$_cryptKey 			= str_replace('.db', '', $db_cryproKey);

if($set['name'] != 'SOMO'){
	define('_SYSTEM_NAME', $set['name'].' - SOMO');
}
else define('_SYSTEM_NAME', $set['name']);

if($set['design'] == 1) $body_theme = ' theme-dark';
else $body_theme = '';

function encrypting($action, $string) {
	global $dbase_file;
	$output = false;
	if(isset($dbase_file)) $secret_key = $dbase_file;
	else $secret_key = '3a4eb9105c4505898b173e784d6d6cc56';

  $encrypt_method = "AES-256-CBC";
  $secret_iv = 'c0a64fcbb9885901a91625f1514536b987d24e441afc4dbb585f150742633af1';
  $key = hash('sha256', $secret_key);

  $iv = substr(hash('sha256', $secret_iv), 0, 16);
  if ( $action == 'encrypt' ) {
      $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
      $output = base64_encode($output);
  } else if( $action == 'decrypt' ) {
      $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
  }
  return $output;
}
