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
        Last update '.timeago($lastSync).'
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
        <div class="card-body  text-center">
          <span class="avatar avatar-md mb-4">'.$loginFirstname[0].$loginName[0].'</span>
          <h3 class="mb-0">'.$loginFullname.'</h3>
          <p class="mb-3">
          <span class="badge bg-blue-lt">'.$loginGroupName.'</span>
          </p>
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
