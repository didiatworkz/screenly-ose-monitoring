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
           Image Function
_______________________________________
*/

include_once('curl.php');

if(isset($_GET['image']) AND isset($_GET['ip'])){
    $ip       = $_GET['ip'];
    (isset($_GET['active']) && $_GET['active'] == 0) ? $active = 0 : $active = 1;
    header("HTTP/1.1 200 OK");
    $path     = playerImage($ip, $active);
    $type     = pathinfo($path, PATHINFO_EXTENSION);
    $data     = file_get_contents($path);
    $base64   = 'data:image/'.$type.';base64,'.base64_encode($data);
    echo json_encode($base64);
} else {
    header("HTTP/1.1 404 Not Found");
    die("No IP Address!");
}
