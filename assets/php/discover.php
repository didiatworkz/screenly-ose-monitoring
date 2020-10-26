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

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

  if(isset($_GET['range']) AND isset($_GET['userID'])){
    list($ip, $mask) = explode('/', $_GET['range']);
    if(filter_var($ip, FILTER_VALIDATE_IP) AND $mask <= 30){
      $ipaddress = $_GET['range'];
    } else die(Translation::of('no_valid_ip'));
  } else die(Translation::of('no_ip_address'));

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

  function checkAddressData($site, $search, $return_data=false){
    $ch = curl_init($site);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER[ 'HTTP_USER_AGENT' ] );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);
    if (strpos($data, $search) !== false) {
      if($return_data == false) return true;
      else return $data;
    }
    return false;
  }

  function checkAddress($ip){
		$ch = curl_init($ip);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 40);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER[ 'HTTP_USER_AGENT' ] );
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($httpcode>=200 && $httpcode<400) {
      if ($httpcode == 301) {
        $ip = str_replace("http://", "https://", $ip);
      }
      if(checkAddressData($ip, '<title>Screenly API</title>')){
        return true;
      }
    }
		return false;
	}

  function getPlayerName($ip){
    $output = checkAddressData($ip, '<title>', true);

    $nameIP = explode('.', $ip);
    $name = '[AUTO] '.$nameIP['3'];

    if(strpos($output, 'Screenly OSE') !== false){
      if(preg_match_all("/<title>(.*)<\/title>/", $output, $result)){
        $name = $result['1']['0'];
        if(!strpos($name, ' - ') === false){
          $name = str_replace("Screenly OSE", "", $name);
          $name = str_replace(" - ", "", $name);
        }
      }
    }

    return $name;
  }

  // All players
  $players = array();
  $sqlPlayer = $db->query("SELECT address FROM player");
  while($player = $sqlPlayer->fetchArray(SQLITE3_ASSOC)){
    $players[] = $player['address'];
  }

  $range = getIpRange($ipaddress);

  $logDetail = Translation::of('start_discovery').' <br />';
  $logDetail .= Translation::of('ip_addresses').': '.long2ip($range['firstIP']).' - '.long2ip($range['lastIP']).'<br />';
  $j = 0;
  $k = 0;
  $ip = getEachIpInRange ($ipaddress);
  for ($i=0; $i < sizeof($ip); $i++) {
    $now = $ip[$i];
    if(checkAddress('http://'.$now.'/api/docs/')){
      $logDetail .=  $now.' - '.strtoupper(Translation::of('found'));
      $j++;
      if(array_search($now, $players) == 0){
        $logDetail .=  ' - '.strtoupper(Translation::of('created'));

        $name = getPlayerName($now);
				$address 	= $now;
				$location = $ipaddress;
        $userID   = $_GET['userID'];

				if($address){
					$db->exec("INSERT INTO player (name, address, location, userID) values('".$name."', '".$address."', '".$location."', '".$userID."')");
          $k++;
          $logDetail .=  ' - '.strtoupper(Translation::of('added'));
				}
      }
      $logDetail .=  '<br />';
    }
  }
  $logDetail .=  Translation::of('end_discovery').' <br /><br /><br />';
  echo '
    <ul class="list-group">
      <li class="list-group-item d-flex justify-content-between align-items-center">
        '.Translation::of('scanned_ips').'
        <span class="badge bg-info badge-pill"> '.$i.' </span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        '.Translation::of('player_found').'
        <span class="badge bg-info badge-pill"> '.$j.' </span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        '.Translation::of('player_added').'
        <span class="badge bg-orange badge-pill"> '.$k.' </span>
      </li>
    </ul>

    <br /><br />
    <p>
      <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#details" aria-expanded="false" aria-controls="details">
        '.Translation::of('detailed_report').'
      </button>
    </p>
    <div class="collapse" id="details">
      <div class="card card-body">
        '.$logDetail.'
      </div>
    </div>
    ';

 ?>
