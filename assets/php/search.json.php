<?php


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

$output = array();

$playerSQL = $db->query("SELECT * FROM player ORDER BY name ASC");
while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
  $assetString = NULL;
  $assets = json_decode($player['assets'], true);
  for ($i=0; $i < sizeof($assets); $i++) {
    $assetString .= ' '.$assets[$i]['asset_id'].' '.str_replace('/', ' ', $assets[$i]['name']);
  }

  $addon = NULL;
  if($player['monitorOutput'] != 0) $addon .= ' Monitor Output SOMA Add-on';
  if($player['deviceInfo'] != 0) $addon .= ' Device Info';

	$gen = array();
	$gen['title'] 		   = $player['name'];
	$gen['url'] 		     = 'index.php?site=players&action=view&playerID='.$player['playerID'];
	$gen['description']  = 'Location: '.$player['location'];
	$gen['tokens'] 		   = 'player monitor reboot clean assets user password ip '.$player['address'].' '.$player['name'].' '.$player['location'].' '.$player['player_user'].' '.$player['created'].' '.$assetString.' '.$addon.' OSE';
	array_push($output, $gen);
}

$gen                 = array();
$gen['title'] 		   = 'Player Overview';
$gen['url'] 		     = 'index.php?site=players';
$gen['description']  = 'Show the status of all Players';
$gen['tokens'] 		   = 'player status online offline ip overview screenshot';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = 'Settings';
$gen['url'] 		     = 'index.php?site=settings';
$gen['description']  = 'Change settings of SOMO';
$gen['tokens'] 		   = 'profile settings design login name password user api public access log user group';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = 'Profile Settings';
$gen['url'] 		     = 'index.php?site=settings&view=profile';
$gen['description']  = 'Change personal Profile Settings';
$gen['tokens'] 		   = 'profile settings design login name password user firstname lastname surname name';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = 'System Settings';
$gen['url'] 		     = 'index.php?site=settings&view=system';
$gen['description']  = 'Change SOMO System Settings';
$gen['tokens'] 		   = 'system settings design name API default refresh time duration delay weeks end date timezone titel';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = 'Public Access Settings';
$gen['url'] 		     = 'index.php?site=settings&view=publicaccess';
$gen['description']  = 'Create or change the linnk for public Access';
$gen['tokens'] 		   = 'public settings access open dark mode token new';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = 'Multi Uploader';
$gen['url'] 		     = 'index.php?site=multiuploader';
$gen['description']  = 'Upload Assets to multiple Players simultaneously';
$gen['tokens'] 		   = 'upload asset multiple multi dropzone url images video movie picture drag';
array_push($output, $gen);

$gen                 = array();
$gen['title'] 		   = 'Logout';
$gen['url'] 		     = 'index.php?action=logout';
$gen['description']  = 'Logout from SOMO';
$gen['tokens'] 		   = 'logout logoff';
array_push($output, $gen);

header('Content-Type: application/json');
echo json_encode($output);
