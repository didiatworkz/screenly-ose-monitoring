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

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

$_boxes = 18;
$_key   = $_GET['key'];
$_dark   = isset($_GET['dark']) ? $_GET['dark'] : '0';
$_site  = 'index.php?public=1&key='.$_key.'&dark='.$_dark;

if(isset($_GET['dark']) && $_GET['dark'] == 1) $body_theme = 'theme-dark';
else $body_theme = '';

echo '
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
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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
    <div class="row">

    ';


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
      </div>
      ';
    }
    echo'</div>';
  }
  else sysinfo('warning', 'No Player available!');
}
else sysinfo('danger', 'Token incorrect - Access denied!');

echo '
</div>
';
