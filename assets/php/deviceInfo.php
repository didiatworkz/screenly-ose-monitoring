<?php

include_once('curl.php');

function deviceInfoInstalled($ip){
  if(checkAddress($ip)){
      $ip = $ip.':9021/check';
      $output = getApiData($ip);
      if($output == 'true') return TRUE;
  }
  return FALSE;
}

function getDeviceInfoData($ip, $data){
  $ip = $ip.':9021/'.$data;
  $output = getApiData($ip);
  if($output != '') return $output;
  else return FALSE;
}

function colorProgrss($value){
  if($value >= 50 && $value <= 64) return '#fab005';
  else if($value >= 65 && $value <= 80) return '#ff922b';
  else if($value >= 81 && $value <= 100) return '#cd201f';
  else return '#5eba00';
}

function timeago($timestamp) {

   $strTime = array("second", "minute", "hour", "day", "month", "year");
   $length = array("60","60","24","30","12","10");

   $currentTime = time();
   if($currentTime >= $timestamp) {
		$diff     = time()- $timestamp;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
		$diff = $diff / $length[$i];
		}
		$diff = round($diff);
		return $diff . " " . $strTime[$i] . "(s) ago ";
   }
}

if(isset($_GET['deviceInfo']) AND isset($_GET['ip'])){
    $ip       = $_GET['ip'];
    header("HTTP/1.1 200 OK");
    $cpu              = round(getDeviceInfoData($ip, 'cpu'), 0);
    $cpu_frequency    = getDeviceInfoData($ip, 'cpu_frequency');
    $memory           = round(getDeviceInfoData($ip, 'memory'), 0);
    $memory_total     = round(getDeviceInfoData($ip, 'memory_total'), 0);
    $memory_progress  = round($memory/$memory_total*100, 0);
    $temp             = getDeviceInfoData($ip, 'temp');
    $disk             = getDeviceInfoData($ip, 'disk');
    $disk_total       = getDeviceInfoData($ip, 'disk_total');
    $disk_progress    = round($disk/$disk_total*100, 0);
    $platform         = getDeviceInfoData($ip, 'platform');
    $platform         = explode(',', $platform);
    $platformName     = $platform['0'];
    $platformName     = str_replace("'", "", $platformName);
    $platformName     = str_replace("(", "", $platformName);
    $platformVersion  = $platform['1'];
    $platformVersion  = str_replace("'", "", $platformVersion);
    $uptime           = getDeviceInfoData($ip, 'uptime');
    $uptimeDifferent  = timeago($uptime);
    $version          = getDeviceInfoData($ip, 'version');
    $hostname         = json_encode(getDeviceInfoData($ip, 'hostname'));
    $hostname         = str_replace('"', "", $hostname);

    $output = array();
    $output['cpu']['value']       = $cpu;
    $output['cpu']['color']       = colorProgrss($cpu);
    $output['cpu']['progress']    = $cpu;
    $output['cpu']['frequency']   = $cpu_frequency;
    $output['memory']['value']    = $memory;
    $output['memory']['color']    = colorProgrss($memory_progress);
    $output['memory']['progress'] = $memory_progress;
    $output['memory']['total']    = $memory_total;
    $output['temp']['value']      = $temp;
    $output['temp']['color']      = colorProgrss($temp);
    $output['temp']['progress']   = $temp;
    $output['disk']['value']      = $disk;
    $output['disk']['color']      = colorProgrss($disk_progress);
    $output['disk']['progress']   = $disk_progress;
    $output['disk']['total']      = $disk_total;
    $output['version']            = $version;
    $output['platform']['name']   = $platformName;
    $output['platform']['version']   = $platformVersion;
    $output['hostname']           = $hostname;
    $output['uptime']['stamp']    = date('d.m.Y H:i:s', $uptime);
    $output['uptime']['now']      = $uptimeDifferent;

    echo json_encode($output);
}
