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
        Public Access Module
_______________________________________
*/

include_once("_functions.php");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$_boxes = 18;
$_key   = $_GET['key'];
$_dark  = isset($_GET['dark']) ? $_GET['dark'] : '0';
$_site  = '_public.php?key='.$_key.'&dark='.$_dark;

if(isset($_GET['dark']) && $_GET['dark'] == 1) $body_theme = 'theme-dark';
else $body_theme = '';

echo '

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>'._SYSTEM_NAME.'</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <meta name="robots" content="noindex,nofollow,noarchive"/>
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/fav/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="assets/img/fav/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="assets/img/fav/favicon-16x16.png">
		<link rel="manifest" href="assets/img/fav/site.webmanifest">
		<link rel="mask-icon" href="assets/img/fav/safari-pinned-tab.svg" color="#2f3949">
		<link rel="shortcut icon" href="assets/img/fav/favicon.ico">
		<meta name="msapplication-TileColor" content="#f5f7fb">
		<meta name="msapplication-config" content="assets/img/fav/browserconfig.xml">
		<meta name="theme-color" content="#f5f7fb">

    <!-- Tabler Core -->
    <link href="assets/css/tabler.min.css" rel="stylesheet"/>
    <!-- Tabler Plugins -->
    <link href="assets/css/tabler-buttons.min.css" rel="stylesheet"/>
    <link href="assets/css/monitor.css" rel="stylesheet"/>

		<!-- Libs JS -->
    <script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/jquery-ui.min.js"></script>
		<script src="assets/libs/DataTables/datatables.min.js"></script>
		<script src="assets/libs/dropzone/dropzone.min.js"></script>

		<!-- Tabler Core -->
		<script src="assets/js/tabler.min.js?1588343458"></script>
    <style>
      body {
      	display: none;
      }
    </style>
  </head>

<body class="'.$body_theme.'">
<div class="page">
  <header class="navbar navbar-expand-md navbar-light">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a href="#" class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pr-0 pr-md-3">
        SOMO
      </a>
    </div>
  </header>
  <div class="content">
    <div class="container-fluid">';

if($_key == $securityToken){
  if(getPlayerCount() > 0){
    if (isset($_GET['next'])) $next = (int) $_GET['next'];
		else $next = 0;

    $para = $next + $_boxes;
    $n = 0;

    $playerSQL 		= $db->query("SELECT * FROM player");
    while($count = $playerSQL->fetchArray(SQLITE3_ASSOC)) $n++;

    if($para < $n) $site = $_site.'&next='.$para;
		else $site = $_site;

    $current_site = $para / $_boxes;
    $total_site = ceil($n / $_boxes);

    $pagination = 'Page '.$current_site.' of '.$total_site.' - ';

    redirect($site, 30);

    echo '
      <div class="row">';

    $playerSQL 		= $db->query("SELECT * FROM player ORDER BY name LIMIT ".$next.",".$_boxes);

    while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
      if($player['name'] == ''){
        $name	 			= 'No Player Name';
        $imageTag 	= 'No Player Name '.$player['playerID'];
      }
      else {
        $name 			= $player['name'];
        $imageTag 	= $player['name'];
      }
      echo'
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
          <div class="card card-sm">
            <a href="#" class="d-block"><img class="player card-img-top" src="'.$loadingImage.'" data-src="'.$player['address'].'" alt="'.$imageTag.'" /></a>
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="lh-sm">
                  <div>'.$name.'</div>
                </div>
                <div class="ml-auto">
                  <a href="index.php?site=players&action=view&playerID=2" class="text-muted">
                    '.$player['address'].'
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>';
    }
    echo'
      </div>';
  }
  else {
    echo '
    <div class="container-xl d-flex flex-column justify-content-center">
      <div class="empty">
        <div class="empty-icon">
          <img src="assets/img/undraw_empty_xct9.svg" height="256" class="mb-4"  alt="">
        </div>
        <p class="empty-title h3">No players</p>
        <p class="empty-subtitle text-muted">
          Can \'t find any player in Database
        </p>
      </div>
    </div>
    ';
  }
}
else {
  echo '
  <div class="container-xl d-flex flex-column justify-content-center">
    <div class="empty">
      <div class="empty-icon">
        <img src="assets/img/undraw_server_down_s4lk.svg" height="256" class="mb-4"  alt="">
      </div>
      <p class="empty-title h3">Invalid Token!</p>
      <p class="empty-subtitle text-muted">
        Check the URL or regenerate a new Link!
      </p>
    </div>
  </div>
  ';
}

echo '
    </div>
    <footer class="footer footer-transparent">
      <div class="container">
        <div class="row text-center align-items-center flex-row-reverse">';
          echo'<div class="col-12 col-lg-auto mt-3 mt-lg-0">';
            if(isset($pagination)) echo $pagination; echo '
            &copy; '.date('Y').' by <a href="https://www.atworkz.de" target="_blank">atworkz.de</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</div>

<script>

  var scriptPlayerAuth = "0";
  var settingsRefreshRate = "'.($loggedIn ? $loginRefreshTime : '5').'000";
  var settingsRunerTime = "FALSE";

</script>
<script src="assets/js/monitor.js"></script>
<script>
  document.body.style.display = "block"
</script>';
$totalTime = array_sum(explode(' ',  microtime())) - $_loadMessureStart; echo '<script>console.log("Loaded in: '.$totalTime.'")</script>
</body>
</html>
';
