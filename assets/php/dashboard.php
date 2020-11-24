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
         Player View Module
_______________________________________
*/

if((isset($_GET['action']) && $_GET['action'] == 'refresh')){
  shell_exec('curl http://localhost/assets/php/runner.php');
  redirect('index.php?site=dashboard');
}

$playerSQL = $db->query("SELECT * FROM player");
$playerCount = 0;
$assetCount = 0;
$assetShowCount = 0;
$assetHideCount = 0;

while($player	= $playerSQL->fetchArray(SQLITE3_ASSOC)){
  $playerCount++;
  $assets = $player['assets'];
  $assets = json_decode($assets, true);

  for ($i=0; $i < sizeof($assets); $i++) {
    $assetCount++;
    if($assets[$i]['is_enabled'] == 1) $assetShowCount++;
    else $assetHideCount++;
  }
  $lastSync = $player['bg_sync'];
}

echo '
<div class="container-xl">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-auto">
        <!-- Page pre-title -->
        <div class="page-pretitle">
          Overview
        </div>
        <h2 class="page-title">
          Dashboard
        </h2>
      </div>
      <div class="col-auto ml-auto text-muted">
        Last update '.timeago($lastSync).' <a href="index.php?site=dashboard&action=refresh" ><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path></svg></a>
      </div>

    </div>
  </div>
  <div class="row row-cards row-decks">
    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="subheader">Status</div>
          </div>
          <div class="h1 mb-3">'.$playerCount.'</div>
          <div class="d-flex mb-2">
            <div>Devices registered</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="subheader">Assets</div>
          </div>
          <div class="h1 mb-3">'.$assetCount.'</div>
          <div class="d-flex mb-2">
            <div>Of all Devices</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">

    <div class="col">
      <div class="card card-sm">
        <div class="card-body d-flex align-items-center">
          <div class="mr-3">
            <div class="chart-sparkline chart-sparkline-square" id="sparkline-7"></div>
          </div>
          <div class="mr-3 lh-sm">
            <div class="strong">
              1,352 Members
            </div>
            <div class="text-muted">163 registered today</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card card-sm">
        <div class="card-body d-flex align-items-center">
          <div class="mr-3">
            <div class="chart-sparkline chart-sparkline-square" id="sparkline-7"></div>
          </div>
          <div class="mr-3 lh-sm">
            <div class="strong">
              1,352 Members
            </div>
            <div class="text-muted">163 registered today</div>
          </div>
        </div>
      </div>
    </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body text-center">
          <div class="mb-3">
            '.getUserAvatar($loginUserID, 'avatar-xl avatar-rounded').'
          </div>
          <div class="card-title mb-1">'.$loginFullname.'</div>
          <div class="text-muted">'.$loginGroupName.'</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="subheader">Last Login</div>
        </div>
        <div class="h1 mb-3">'.timeago(lastLoginTimestamp($loginUserID)).'</div>
        <div class="d-flex mb-2">
          <div>'.lastLogin($loginUserID).'</div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/libs/peity/jquery.peity.min.js"></script>
<script>
      document.addEventListener("DOMContentLoaded", function () {
      	$().peity && $("#sparkline-7").text("56/100").peity("pie", {
      		width: 40,
      		height: 40,
      		stroke: "#cd201f",
      		strokeWidth: 2,
      		fill: ["#cd201f", "rgba(110, 117, 130, 0.2)"],
      		padding: .2,
      		innerRadius: 17,
      	});
      });
    </script>
';
