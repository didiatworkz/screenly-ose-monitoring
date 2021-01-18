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

require_once("_functions.php");
require_once(__DIR__.'/assets/php/translation.php');
use Translation\Translation;
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
<html lang="'.Translation::of('lang_tag').'">
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
    <link href="assets/css/tabler.min.css?t='.$set['updatecheck'].'" rel="stylesheet"/>
    <!-- Tabler Plugins -->
    <link href="assets/css/tabler-buttons.min.css?t='.$set['updatecheck'].'" rel="stylesheet"/>
    <link href="assets/css/monitor.css?t='.$set['updatecheck'].'" rel="stylesheet"/>

		<!-- Libs JS -->
    <script src="assets/js/jquery.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/js/jquery-ui.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/libs/DataTables/datatables.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/libs/dropzone/dropzone.min.js?t='.$set['updatecheck'].'"></script>

		<!-- Tabler Core -->
		<script src="assets/js/tabler.min.js?t='.$set['updatecheck'].'"></script>
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

    $pagination = Translation::of('page').' '.$current_site.' '.Translation::of('of').' '.$total_site.' - ';

    redirect($site, 30);

    echo '
      <div class="row">';

    $playerSQL 		= $db->query("SELECT * FROM player ORDER BY name LIMIT ".$next.",".$_boxes);

    while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
      $displayPower = '';
      $displayColor = '#d63939';
      if($player['name'] == ''){
        $name	 			= Translation::of('no_player_name');
        $imageTag 	= Translation::of('no_player_name').' '.$player['playerID'];
      }
      else {
        $name 			= $player['name'];
        $imageTag 	= $player['name'];
      }
      $displayAPI = callURL('GET', $player['address'].'/api/v1/info', false, $player['playerID'], false);
      if(is_array($displayAPI)){
        $displayPower = $displayAPI['display_power'];
        if($displayPower == 'On') $displayColor = '#2fb344';
      }

      $display = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="52" height="52" viewBox="0 0 24 24" stroke-width="3" stroke="'.$displayColor.'" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <rect x="3" y="7" width="18" height="13" rx="2" />
        <polyline points="16 3 12 7 8 3" />
      </svg>';

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
                    '.$player['address'].' '.$display.'
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
        <p class="empty-title h3">'.Translation::of('no_player_found').'</p>
        <p class="empty-subtitle text-muted">
          '.Translation::of('msg.cant_find_player_in_database').'
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
      <p class="empty-title h3">'.Translation::of('invalid_token').'!</p>
      <p class="empty-subtitle text-muted">
        '.Translation::of('msg.check_link').'
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

  var scriptPlayerAuth    = "0";
  var settingsRefreshRate = "5000";
  var settingsRunerTime   = "FALSE";
  var userAddonActive		  = "1";
  var playerAssetsOrder 	= "asc";

</script>
<script src="assets/js/monitor.js?t='.$set['updatecheck'].'"></script>
<script>
  document.body.style.display = "block"
</script>';
$totalTime = array_sum(explode(' ',  microtime())) - $_loadMessureStart; echo '<script>console.log("Loaded in: '.$totalTime.'")</script>
</body>
</html>
';
