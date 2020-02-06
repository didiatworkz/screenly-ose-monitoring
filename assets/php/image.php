<?php

function checkAddress($ip){
  $ch = curl_init($ip);
  curl_setopt($ch, CURLOPT_TIMEOUT, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 200);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $data = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if(($httpcode>=200 && $httpcode<300) || $httpcode==401) return true;
  else return false;
}

function playerImage($url){
  if(checkAddress($url)) {
    if(checkAddress($url.':9020/screen/screenshot.png')) return 'http://'.$url.':9020/screen/screenshot.png?t='.time();
    else return 'http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/assets/img/online.png';
  }
  else return 'http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/assets/img/offline.png';
}

if(isset($_GET['image']) AND isset($_GET['ip'])){
    $ip       = $_GET['ip'];
    header("HTTP/1.1 200 OK");
    $path     = playerImage($ip);
    $type     = pathinfo($path, PATHINFO_EXTENSION);
    $data     = file_get_contents($path);
    $base64   = 'data:image/'.$type.';base64,'.base64_encode($data);
    echo json_encode($base64);
} else {
    header("HTTP/1.1 404 Not Found");
    die("No IP Address!");
}
