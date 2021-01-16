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
        Device Info Functions
_______________________________________
*/

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

function getMonitorInfoData($ip, $data){
  $ip = $ip.':9020/'.$data;
  $output = getApiData($ip);
  if($output != '') return $output;
  else return FALSE;
}

function colorProgress($value){
  if($value >= 50 && $value <= 64) return '#fab005';
  else if($value >= 65 && $value <= 80) return '#ff922b';
  else if($value >= 81 && $value <= 100) return '#cd201f';
  else return '#5eba00';
}

if(isset($_GET['deviceInfo']) AND isset($_GET['ip'])){
    header("HTTP/1.1 200 OK");
    $ip               = $_GET['ip'];
    $cpu              = round(getDeviceInfoData($ip, 'cpu'), 0);
    $cpu_frequency    = getDeviceInfoData($ip, 'cpu_frequency');
    $memory_avail     = round(getDeviceInfoData($ip, 'memory'), 0);
    $memory_total     = round(getDeviceInfoData($ip, 'memory_total'), 0);
    $memory           = $memory_total - $memory_avail-50;
    $memory_progress  = round($memory/$memory_total*100, 0);
    $temp             = getDeviceInfoData($ip, 'temp');
    $disk_free        = getDeviceInfoData($ip, 'disk');
    $disk_total       = getDeviceInfoData($ip, 'disk_total');
    $disk             = round($disk_total - $disk_free, 2);
    $disk_progress    = round(getDeviceInfoData($ip, 'disk_percent'), 0);
    $platform         = getDeviceInfoData($ip, 'platform');
    $platform         = str_replace('\'', '"', $platform);

    $platformArr      = json_decode($platform, true);
    $platformName     = ucfirst($platformArr['id']);
    $platformVersion  = $platformArr['codename'];
    $uptime           = getDeviceInfoData($ip, 'uptime');
    $uptimeDifferent  = timeago($uptime);
    $versionDev       = getDeviceInfoData($ip, 'version');
    $versionMon       = getMonitorInfoData($ip, 'version');
    $versionDesc      = '';
    if($versionDev < $devInfVersion || $versionMon < $monitorInfo) $versionDesc = '<a href="index.php?site=addon" class="text-muted">>> Update available</a>';
    $hostname         = json_encode(getDeviceInfoData($ip, 'hostname'));
    $hostname         = str_replace('"', "", $hostname);

    $output = array();
    $output['cpu']['value']        = $cpu;
    $output['cpu']['color']        = colorProgress($cpu);
    $output['cpu']['progress']     = $cpu;
    $output['cpu']['frequency']    = $cpu_frequency;
    $output['memory']['value']     = $memory;
    $output['memory']['color']     = colorProgress($memory_progress);
    $output['memory']['progress']  = $memory_progress;
    $output['memory']['total']     = $memory_total;
    $output['temp']['value']       = $temp;
    $output['temp']['color']       = colorProgress($temp);
    $output['temp']['progress']    = $temp;
    $output['disk']['value']       = $disk;
    $output['disk']['color']       = colorProgress($disk_progress);
    $output['disk']['progress']    = $disk_progress;
    $output['disk']['total']       = $disk_total;
    $output['versiondev']          = $versionDev;
    $output['versionmon']          = $versionMon;
    $output['versiondesc']         = $versionDesc;
    $output['platform']['name']    = $platformName;
    $output['platform']['version'] = $platformVersion;
    $output['hostname']            = $hostname;
    $output['uptime']['stamp']     = date('d.m.Y H:i:s', $uptime);
    $output['uptime']['now']       = $uptimeDifferent;

    echo json_encode($output);


}
