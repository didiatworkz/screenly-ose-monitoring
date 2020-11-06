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
            Runnser Script
_______________________________________
*/


$_TEST = FALSE;  # TRUE / FALSE


chdir('../../');
include_once("_functions.php");

$now = time();

$playerSQL = $db->query("SELECT * FROM player");
while($player	= $playerSQL->fetchArray(SQLITE3_ASSOC)){
  $id = $player['playerID'];
  $name = $player['name'];
  $ip   = $player['address'];

  // SET Status offline
  $db->exec("UPDATE `player` SET status='0' WHERE playerID='".$id."'");
  $db->exec("UPDATE `player` SET bg_sync='".$now."' WHERE playerID='".$id."'");

  if(checkAddress($ip)){
    if($_TEST) echo $name.'<br />';

    // SET Status online
    $db->exec("UPDATE `player` SET status='1' WHERE playerID='".$id."'");

    // GET Assets
    $playerAssets = getApiData($ip.'/api/'.$apiVersion.'/assets', $id);
    if(strpos($playerAssets, 'error') === false) {
      $db->exec("UPDATE `player` SET assets='".$playerAssets."' WHERE playerID='".$id."'");
      if($_TEST) echo $playerAssets.'<br />';
    }

    // GET monitorOutput Version
    if(checkAddress($ip.':9020/screen/screenshot.png')){
      $db->exec("UPDATE `player` SET monitorOutput='1.0' WHERE playerID='".$id."'");
      if($_TEST) echo '1.0 <br />';
    }

    if(checkAddress($ip.':9020/version')){
      $apiVM = getApiData($ip.':9020/version', $id);
      if($apiVM != '')$db->exec("UPDATE `player` SET monitorOutput='".$apiVM."' WHERE playerID='".$id."'");
      if($_TEST) echo $apiVM.'<br />';
    }


    // GET deviceInfo Version
    if(checkAddress($ip.':9021/version')){
      $apiVD = getApiData($ip.':9021/version', $id);
      if($apiVD != '')$db->exec("UPDATE `player` SET deviceInfo='".$apiVD."' WHERE playerID='".$id."'");
      if($_TEST) echo $apiVD.'<br />';
    }
  }
}
