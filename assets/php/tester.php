<?php

while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
$name = $player['name'];
$ip   = $player['address'];

$offline = '<span class="badge bg-dark">offline</span>';
$not = '<span class="badge bg-danger">Not installed</span>';
if(checkAddress($ip)){
if(checkAddress($ip.':9020/screen/screenshot.png')){
$screenS = '<span class="badge bg-success">Version 1</span>';
} else $screenS = $not;

if(checkAddress($ip.':9020/index.php?get=version')){
$apiV = callURL('GET', $player['address'].':9020/index.php?get=version', false, false, false);
$apiV = json_decode($apiV, true);
if($apiV['screenshotversion'] != ''){
  $screenS = '<span class="badge bg-success">Version '.$apiV['screenshotversion'].'</span>';
}
$apiS = '<span class="badge bg-success">Version '.$apiV['apiversion'].'</span>';
} else $apiS = $not;
}
else {
$screenS = $offline;
$apiS = $offline;
}
