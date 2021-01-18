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
             Search JSON
_______________________________________
*/

// Translation DONE

// [
//   {
//   "title": "Accordions",
//   "url": "uikit/panel-accordion.html",
//   "description": "Responsive admin dashboard and web application ui kit. Accordion component allows you to toggle content on your pages with a few classes.",
//   "tokens": "panel accordion collapse"
//   },
//   {
//   "title": "Tabs",
//   "url": "uikit/panel-tab.html",
//   "description": "Responsive admin dashboard and web application ui kit. A tab keeps related content in a single container that is shown and hidden through navigation.",
//   "tokens": "panel tab"
//   },
// ]

chdir('../../');
include_once("_functions.php");

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;

$output = array();

$playerSQL = $db->query("SELECT * FROM player ORDER BY name ASC");
while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
  $assetString = NULL;
  $assets = json_decode($player['assets'], true);
  if(is_array($assets)){
    for ($i=0; $i < count($assets); $i++) {
      $start_date = str_replace(':00+00:00', '', $assets[$i]['end_date']);
      $start_date = str_replace('T', ' ', $start_date);
      $end_date = str_replace(':00+00:00', '', $assets[$i]['end_date']);
      $end_date = str_replace('T', ' ', $end_date);
      $assetString .= ' '.$assets[$i]['asset_id'].' '.str_replace('/', ' ', $assets[$i]['name']). ' '.$start_date.' '.$end_date;
    }
  }
  $addon = NULL;
  if($player['monitorOutput'] != 0) $addon .= ' '.Translation::of('monitorOutput').' SOMA '.Translation::of('addon');
  if($player['deviceInfo'] != 0) $addon .= ' '.Translation::of('device_info');

	$gen = array();
	$gen['title'] 		   = $player['name'];
	$gen['url'] 		     = 'index.php?site=players&action=view&playerID='.$player['playerID'];
	$gen['description']  = Translation::of('location').': '.$player['location'];
	$gen['tokens'] 		   = strtolower(Translation::of('player').' '.Translation::of('monitor').' '.Translation::of('reboot').' '.Translation::of('clean_assets').' '.Translation::of('user').' '.Translation::of('password').' '.Translation::of('ip_address').' '.$player['address'].' '.$player['name'].' '.$player['location'].' '.$player['player_user'].' '.$player['created'].' '.$assetString.' '.$addon.' OSE');
	array_push($output, $gen);
}

$gen                 = array();
$gen['title'] 		   = Translation::of('player');
$gen['url'] 		     = 'index.php?site=players';
$gen['description']  = Translation::of('player_search_desc');
$gen['tokens'] 		   = strtolower(Translation::of('player').' '.Translation::of('status').' '.Translation::of('online').' '.Translation::of('offline').' '.Translation::of('ip_address').' '.Translation::of('overview').' '.Translation::of('screenshot'));
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('settings');
$gen['url'] 		     = 'index.php?site=settings';
$gen['description']  = Translation::of('settings_search_desc');
$gen['tokens'] 		   = strtolower(Translation::of('profile_settings').' '.Translation::of('design').' '.Translation::of('login').' '.Translation::of('name').' '.Translation::of('password').' '.Translation::of('user').' '.Translation::of('api_settings').' '.Translation::of('public_access_link').' '.Translation::of('log_time').' '.Translation::of('group'));
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('profile_settings');
$gen['url'] 		     = 'index.php?site=settings&view=profile';
$gen['description']  = Translation::of('profile_search_desc');
$gen['tokens'] 		   = strtolower(Translation::of('profile_settings').' '.Translation::of('design').' '.Translation::of('login').' '.Translation::of('name').' '.Translation::of('password').' '.Translation::of('username').' '.Translation::of('firstname').' '.Translation::of('familyname'));
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('system_settings');
$gen['url'] 		     = 'index.php?site=settings&view=system';
$gen['description']  = Translation::of('settings_search_desc');
$gen['tokens'] 		   = strtolower(Translation::of('system_settings').' '.Translation::of('design').' '.Translation::of('name').' '.Translation::of('api_settings').' '.Translation::of('refresh').' '.Translation::of('default_settings').' '.Translation::of('duration_in_sec').' '.Translation::of('timezone'));
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('public_access_settings');
$gen['url'] 		     = 'index.php?site=settings&view=publicaccess';
$gen['description']  = Translation::of('public_access_search_desc');
$gen['tokens'] 		   = strtolower(Translation::of('public_access_settings').' '.Translation::of('dark_mode').' '.Translation::of('token'));
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('user_settings');
$gen['url'] 		     = 'index.php?site=usermanagement';
$gen['description']  = Translation::of('user_search_desc');
$gen['tokens'] 		   = strtolower(Translation::of('user_settings').' '.Translation::of('delete_user2').' '.Translation::of('edit_user2').' '.Translation::of('new_user').' '.Translation::of('account').' '.Translation::of('access').' '.Translation::of('groups').' '.Translation::of('status').' '.Translation::of('activated').' '.Translation::of('deactivated').' '.Translation::of('username').' '.Translation::of('firstname').' '.Translation::of('familyname').' '.Translation::of('login').' '.Translation::of('password'));
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('group_settings');
$gen['url'] 		     = 'index.php?site=groupmanagement';
$gen['description']  = Translation::of('group_search_desc');
$gen['tokens'] 		   = strtolower(Translation::of('group_settings').' '.Translation::of('delete_group').' '.Translation::of('edit_group').' '.Translation::of('create_group').' '.Translation::of('account').' '.Translation::of('access').' '.Translation::of('status').' '.Translation::of('restrict_individual_modules').' '.Translation::of('player').' '.Translation::of('assets'));
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('multi_uploader');
$gen['url'] 		     = 'index.php?site=multiuploader';
$gen['description']  = Translation::of('multi_uploader_search_desc');
$gen['tokens'] 		   = 'upload asset multiple multi dropzone url images video movie picture drag';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('addon');
$gen['url'] 		     = 'index.php?site=addon';
$gen['description']  = Translation::of('addon_search_desc');
$gen['tokens'] 		   = 'add on monitor output screenshot device info install remote soma beta reinstall manual installation bash pi ssh script';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = 'atWorkz';
$gen['url'] 		     = 'index.php?somo_link';
$gen['description']  = 'Creator of Screenly OSE Monitoring';
$gen['tokens'] 		   = 'creator copyright atworkz github somo soma script';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = Translation::of('logout');
$gen['url'] 		     = 'index.php?action=logout';
$gen['description']  = Translation::of('logout_search_desc');
$gen['tokens'] 		   = 'logout logoff';
array_push($output, $gen);

header('Content-Type: application/json');
echo json_encode($output);
// echo '<pre>';
// print_r($output);
// echo '</pre>';
