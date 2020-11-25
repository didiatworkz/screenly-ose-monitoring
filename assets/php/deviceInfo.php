<?php

include_once('functions.php');
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

if(isset($_GET['deviceInfo']) AND isset($_GET['ip'])){
    $ip       = $_GET['ip'];
    header("HTTP/1.1 200 OK");
    $cpu              = round(getDeviceInfoData($ip, 'cpu'), 0);
    $cpu_frequency    = getDeviceInfoData($ip, 'cpu_frequency');
    $memory           = round(getDeviceInfoData($ip, 'memory'), 0);
    $memory_total     = round(getDeviceInfoData($ip, 'memory_total'), 0);
    $memory_progress  = round($memory/$memory_total*100, 0);
    $temp             = getDeviceInfoData($ip, 'temp');
    $disk_free        = getDeviceInfoData($ip, 'disk');
    $disk_total       = getDeviceInfoData($ip, 'disk_total');
    $disk             = round($disk_total - $disk_free, 2);
    $disk_progress    = round(getDeviceInfoData($ip, 'disk_percent'), 0);
    $platform         = getDeviceInfoData($ip, 'platform');
    
    list($platformName, $platformVersion) = explode(',', $platform);
    $platformName     = str_replace("'", "", $platformName);
    $platformName     = str_replace("(", "", $platformName);
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
