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
$playerCount    = 0;
$assetCount     = 0;
$assetShowCount = 0;
$assetHideCount = 0;
$statOnline     = 0;
$statOffline    = 0;

while($player	= $playerSQL->fetchArray(SQLITE3_ASSOC)){
  $playerCount++;
  $assets = $player['assets'];
  $assets = json_decode($assets, true);

  for ($i=0; $i < count(array($assets)); $i++) {
    $assetCount++;
    if($assets[$i]['is_enabled'] == 1) $assetShowCount++;
    else $assetHideCount++;
  }
  $lastSync = $player['bg_sync'];

  if($player['status'] == 1) $statOnline++;
  else $statOffline++;
}

$statOnlinePro  = ceil($statOnline / $playerCount * 100);
$statOfflinePro = 100 - $statOnline;


$lastFivePlayer = NULL;
$playerSQL = $db->query("SELECT playerID, name, address, created FROM player ORDER BY created DESC");
if($playerCount < 5) $maxEntries = $playerCount;
else $maxEntries = 5;
for ($i=0; $i < $maxEntries; $i++) {
  $player	= $playerSQL->fetchArray(SQLITE3_ASSOC);
  $lastFivePlayer .= '
  <div class="list-item">
    <div><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 12v3a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10a1 1 0 0 1 1 -1h9" /><line x1="7" y1="20" x2="17" y2="20" /><line x1="9" y1="16" x2="9" y2="20" /><line x1="15" y1="16" x2="15" y2="20" /><path d="M17 4h4v4" /><path d="M16 9l5-5" /></svg></div>
    <div class="text-truncate">
      <a href="index.php?site=players&action=view&playerID='.$player['playerID'].'" class="text-body d-block">'.$player['name'].'</a>
      <small class="d-block text-muted text-truncate mt-n1">'.$player['address'].'</small>
    </div>
    <div class="list-item-actions">'.$player['created'].'</div>
  </div>
  ';
}

$lastFiveAssets = NULL;
$playerSQL = $db->query("SELECT playerID, name, assets FROM player");
if($playerCount < 5) $maxEntries = $playerCount;
else $maxEntries = 5;
for ($i=0; $i < $maxEntries; $i++) {
  $player	= $playerSQL->fetchArray(SQLITE3_ASSOC);
  $assets = $player['assets'];
  $assets = json_decode($assets, true);
  $assetCount = count($assets);
  $maxAssets = 0;
  for ($i=0; $i < $assetCount; $i++) {
    // 2020-10-28T00:00:00+00:00
    $date_now = date("Y-m-d");
    $time_now = date("H:i:s");
    $now = $date_now.'T'.$time_now.'+00:00';
    $end_date = str_replace(':00+00:00', '', $assets[$i]['end_date']);
    $end_date = str_replace('T', ' ', $end_date);
    if(($assets[$i]['is_enabled'] == 0 || $assets[$i]['is_active'] == 0 || $assets[$i]['end_date'] < $now) && $maxAssets < 5){
      if($assets[$i]['mimetype'] == 'webpage'){
        $mimetypeIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><line x1="3.6" y1="9" x2="20.4" y2="9"></line><line x1="3.6" y1="15" x2="20.4" y2="15"></line><path d="M11.5 3a17 17 0 0 0 0 18"></path><path d="M12.5 3a17 17 0 0 1 0 18"></path></svg>';
      }
      else if($assets[$i]['mimetype'] == 'video'){
        $mimetypeIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect><line x1="8" y1="4" x2="8" y2="20"></line><line x1="16" y1="4" x2="16" y2="20"></line><line x1="4" y1="8" x2="8" y2="8"></line><line x1="4" y1="16" x2="8" y2="16"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="16" y1="8" x2="20" y2="8"></line><line x1="16" y1="16" x2="20" y2="16"></line></svg>';
      }
      else {
        $mimetypeIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="15" y1="8" x2="15.01" y2="8"></line><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="M4 15l4 -4a3 5 0 0 1 3 0l 5 5"></path><path d="M14 14l1 -1a3 5 0 0 1 3 0l 2 2"></path></svg>';
      }

      $lastFiveAssets .= '
      <div class="list-item">
        <div>'.$mimetypeIcon.'</div>
        <div class="text-truncate">
          <a href="index.php?site=players&action=view&playerID='.$player['playerID'].'" class="text-body d-block">'.$assets[$i]['name'].'</a>
          <small class="d-block text-muted text-truncate mt-n1">'.$player['name'].'</small>
        </div>
        <div class="list-item-actions">'.$end_date.'</div>
      </div>
      ';
      $maxAssets++;
    }
  }
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

    <div class="col-sm-3 col-lg-2">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="subheader">Status</div>
          </div>
          <div class="h1 mb-3">'.$playerCount.'</div>
          <div class="d-flex mb-2">
            <div>Devices</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-3 col-lg-2">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="subheader">Assets</div>
          </div>
          <div class="h1 mb-3">'.$assetCount.'</div>
          <div class="d-flex mb-2">
            <div>Summary</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-4">
      <div class="card">
        <div class="card-body">
          <p class="mb">Online Status</p>
          <div class="progress progress-separated mb-4">
            <div class="progress-bar bg-danger" role="progressbar" style="width: '.$statOfflinePro.'%"></div>
            <div class="progress-bar bg-success" role="progressbar" style="width: '.$statOnlinePro.'%"></div>
          </div>
          <div class="row">
            <div class="col-auto d-flex align-items-center pr-2">
              <span class="legend mr-2 bg-danger"></span>
              <span>Offline</span>
              <span class="ml-2 text-muted">'.$statOffline.' Devices</span>
            </div>
            <div class="col-auto d-flex align-items-center px-2">
              <span class="legend mr-2 bg-success"></span>
              <span>Online</span>
              <span class="ml-2 text-muted">'.$statOnline.' Devices</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-8 col-lg-4">
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


    <div class="col-sm-12 col-lg-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">5 Last Players</h3>
        </div>
        <div class="card-body">
          <div class="list list-row list-hoverable">
            '.$lastFivePlayer.'
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-12 col-lg-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">5 Hidden Assets</h3>
        </div>
        <div class="card-body">
          <div class="list list-row list-hoverable">
            '.$lastFiveAssets.'
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
            ';
