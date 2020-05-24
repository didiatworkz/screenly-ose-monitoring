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
        Discover Module
________________________________________
*/
//  discover.php?range=192.168.178.0/24

  if(isset($_GET['range']) AND isset($_GET['userID'])){
    list($ip, $mask) = explode('/', $_GET['range']);
    if(filter_var($ip, FILTER_VALIDATE_IP) AND $mask <= 30){
      $ipaddress = $_GET['range'];
    } else die("No Valid IP Address!");
  } else die("No IP Address!");

  $rootPath = '/var/www/html/monitor';
  $dbase_key = $rootPath.'/assets/tools/key.php';
  include_once($dbase_key);
  $db = new SQLite3($rootPath.'/'.$db_cryproKey);

  function getIpRange($cidr) {
      list($ip, $mask) = explode('/', $cidr);
      $maskBinStr =str_repeat("1", $mask ) . str_repeat("0", 32-$mask );
      $inverseMaskBinStr = str_repeat("0", $mask ) . str_repeat("1",  32-$mask );
      $ipLong = ip2long( $ip );
      $ipMaskLong = bindec( $maskBinStr );
      $inverseIpMaskLong = bindec( $inverseMaskBinStr );
      $netWork = $ipLong & $ipMaskLong;
      $start = $netWork+1;
      $end = ($netWork | $inverseIpMaskLong) -1 ;
      return array('firstIP' => $start, 'lastIP' => $end );
  }

  function getEachIpInRange ( $cidr) {
      $ips = array();
      $range = getIpRange($cidr);
      for ($ip = $range['firstIP']; $ip <= $range['lastIP']; $ip++) {
          $ips[] = long2ip($ip);
      }
      return $ips;
  }

  function checkAddress($ip){
		$ch = curl_init($ip);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER[ 'HTTP_USER_AGENT' ] );
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if(($httpcode>=200 && $httpcode<300) || $httpcode==401) return true;
		else return false;
	}

  function getPlayerName($ip){
    $ch = curl_init($ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    if(preg_match_all("/<title>(.*)<\/title>/", $output, $result)){
      $name = $result['1']['0'];
      if(!strpos($name, ' - ') === false){
        $name = str_replace("Screenly OSE", "", $name);
        $name = str_replace(" - ", "", $name);
      }
    }
    else $name = '[AUTO] '.$ip;

    return $name;
  }

  // All players
  $players = array();
  $sqlPlayer = $db->query("SELECT address FROM player");
  while($player = $sqlPlayer->fetchArray(SQLITE3_ASSOC)){
    $players[] = $player['address'];
  }

  $range = getIpRange($ipaddress);

  echo 'Start Discovery <br />';
  echo 'IP Addresses: '.long2ip($range['firstIP']).' - '.long2ip($range['lastIP']).'<br />';
  $j = 0;
  $k = 0;
  $ip = getEachIpInRange ($ipaddress);
  for ($i=0; $i < sizeof($ip); $i++) {
    $now = $ip[$i];
    if(checkAddress($now.'/api/v1.2/assets')){
      echo " [Found] ".$now.'<br />';
      $j++;
      if(array_search($now, $players) == 0){
        echo " [CREATE] ".$now.'<br />';


        $name = getPlayerName($now);
				$address 	= $now;
				$location = $ipaddress;
        $userID   = $_GET['userID'];

				if($address){
					$db->exec("INSERT INTO player (name, address, location, userID) values('".$name."', '".$address."', '".$location."', '".$userID."')");
          $k++;
          echo " [ADDED] ".$now.'<br />';
				}
      }
      echo '<br />';
    }
  }
  echo 'End Discovery <br /><br /><br />';
  echo '['.$i.'] IP Addresses scanned<br />';
  echo '['.$j.'] Player found<br />';
  echo '['.$k.'] Player added<br />';



 ?>
