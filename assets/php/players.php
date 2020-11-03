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

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

// TODO: TRANSLATION!

// GET: action:view - Player detail overview
if(isset($_GET['action']) && $_GET['action'] == 'view'){
  if(isset($_GET['playerID'])){
    $playerID 	= $_GET['playerID'];
    $playerSQL 	= $db->query("SELECT * FROM player WHERE playerID='".$playerID."'");
    $player 		= $playerSQL->fetchArray(SQLITE3_ASSOC);
    $monitor 		= 0;
    $monitorAPI	= 0;

    $player['name'] != '' ? $playerName = $player['name'] : $playerName = Translation::of('unkown_name');
    $player['location'] != '' ? $playerLocation = $player['location'] : $playerLocation = '';

    $displayAPI     = '';
    $displayPower   = '';
    $displayRes      = '';

    if(checkAddress($player['address'].'/api/'.$apiVersion.'/assets')){
      $playerAPI = callURL('GET', $player['address'].'/api/'.$apiVersion.'/assets', false, $playerID, false);
      $db->exec("UPDATE player SET sync='".time()."' WHERE playerID='".$playerID."'");
      $monitor	 = checkAddress($player['address'].':9020/screen/screenshot.png');
      $monitorAPI	 = checkAddress($player['address'].':9021/check');
      $playerAPICall = TRUE;

      if($monitor == TRUE){
        $monitorInfo = '<span class="badge bg-success">  '.strtolower(Translation::of('installed')).'  </span>';
      } else $monitorInfo = '<a href="#" title="'.Translation::of('what_does_that_mean').'"><span class="badge bg-info">'.strtolower(Translation::of('not_installed')).'</span></a>';

      $showBox	 		= '';
      $statusBanner = '';
      $colSize	 		= 'col-sm-6 col-lg-3';
      $status		 		= strtolower(Translation::of('online'));
      $statusColor 	= 'success';
      $newAsset			= '<a href="#" data-toggle="modal" data-target="#newAsset" class="btn btn-success btn-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> '.Translation::of('new_asset').'</a>';
      $bulkDelete		= '<a href="#" data-toggle="modal" data-target="#confirmDeleteAssets" data-href="index.php?site=players&action=view&playerID=21&action2=deleteAllAssets&playerID='.$player['playerID'].'" class="btn btn-block btn-danger" title="delete"><i class="tim-icons icon-simple-remove"></i> '.Translation::of('clean_assets').'</a>';
      $navigation 	= '<div class="row"><div class="col-xs-12 col-md-6 mb-2"><button data-playerID="'.$player['playerID'].'" data-order="previous" class="changeAsset btn btn-sm btn-block btn-info" title="'.Translation::of('previous_asset').'"><i class="tim-icons icon-double-left"></i> '.Translation::of('asset').'</button></div> <div class="col-xs-12 col-md-6 mb-2"> <button data-playerID="'.$player['playerID'].'" data-order="next" class="changeAsset btn btn-sm btn-block btn-info" title="'.Translation::of('next_asset').'">'.Translation::of('asset').' <i class="tim-icons icon-double-right"></i></button></div></div>';
      $management		= '<a href="http://'.$player['address'].'" target="_blank" class="btn btn-primary btn-block"><i class="tim-icons icon-spaceship"></i> '.Translation::of('open_management').'</a>';
      $reboot				= '<button data-playerid="'.$player['playerID'].'" class="btn btn-block btn-info reboot" title="'.Translation::of('reboot_player').'"><i class="tim-icons icon-refresh-01"></i> '.Translation::of('reboot_player').'</button>';
      $script 			= '
      <tr>
        <td>'.Translation::of('monitor_addon').':</td>
        <td>'.$monitorInfo.'</td>
      </tr>
      ';

      $displayAPI = callURL('GET', $player['address'].'/api/v1/info', false, $playerID, false);
      if(is_array($displayAPI)){
        $displayRes = explode(',', $displayAPI['display_info']);
        $displayRes = $displayRes['1'];
        $displayPower = $displayAPI['display_power'];
      }

      if(deviceInfoInstalled($player['address'])){
        $deviceInfoHead = '
        <label class="form-check form-switch d-sm-inline-block mr-3">
          <input class="form-check-input deviceCheckbox" type="checkbox" checked>
          <span class="form-check-label">Device Info API</span>
        </label>
        <!--
        <a href="index.php?site=players&action=ps&playerID='.$playerID.'" class="btn btn-secondary ml-3 d-none d-sm-inline-block">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z"></path>
            <line x1="4" y1="6" x2="9.5" y2="6"></line>
            <line x1="4" y1="10" x2="9.5" y2="10"></line>
            <line x1="4" y1="14" x2="9.5" y2="14"></line>
            <line x1="4" y1="18" x2="9.5" y2="18"></line>
            <line x1="14.5" y1="6" x2="20" y2="6"></line>
            <line x1="14.5" y1="10" x2="20" y2="10"></line>
            <line x1="14.5" y1="14" x2="20" y2="14"></line>
            <line x1="14.5" y1="18" x2="20" y2="18"></line>
          </svg>
          Process Overview
        </a>
        <a href="index.php?site=players&action=ps&playerID='.$playerID.'" class="btn btn-secondary ml-3 d-sm-none btn-icon" aria-label="Create new report">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z"></path>
            <line x1="4" y1="6" x2="9.5" y2="6"></line>
            <line x1="4" y1="10" x2="9.5" y2="10"></line>
            <line x1="4" y1="14" x2="9.5" y2="14"></line>
            <line x1="4" y1="18" x2="9.5" y2="18"></line>
            <line x1="14.5" y1="6" x2="20" y2="6"></line>
            <line x1="14.5" y1="10" x2="20" y2="10"></line>
            <line x1="14.5" y1="14" x2="20" y2="14"></line>
            <line x1="14.5" y1="18" x2="20" y2="18"></line>
          </svg>
        </a>-->
        ';
        $deviceInfoBox = '
        <div class="row row-deck row-cards device-info" data-src="'.$player['address'].'">
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">CPU</div>
                </div>
                <div class="h1 mb-3"><span class="cpu"></span>%</div>
                <div class="d-flex mb-2">
                  <div>Frequency: <span class="cpu_frequency"></span> MHz</div>
                </div>
              </div>
              <div class="progress card-progress">
                <div class="progress-bar cpu-bar bg-red" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Memory</div>
                </div>
                <div class="h1 mb-3"><span class="memory"></span> MB</div>
                <div class="d-flex mb-2">
                  <div>Total: <span class="memory_total"></span> MB</div>
                </div>
              </div>
              <div class="progress card-progress">
                <div class="progress-bar memory-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Temperature</div>
                </div>
                <div class="h1 mb-3"><span class="temp"></span>°</div>
                <div class="d-flex mb-2">
                  <div>Sensor: CPU</div>
                </div>
              </div>
              <div class="progress card-progress">
                <div class="progress-bar temp-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Storage</div>
                </div>
                <div class="h1 mb-3"><span class="disk"></span> GB</div>
                <div class="d-flex mb-2">
                  <div>Total: <span class="disk_total"></span> GB</div>
                </div>
              </div>
              <div class="progress card-progress">
                <div class="progress-bar disk-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Uptime</div>
                </div>
                <div class="h1 mb-3"><span class="upnow"></span></div>
                <div class="d-flex mb-2">
                  <div><span class="uptime"></span></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Hostname</div>
                </div>
                <div class="h1 mb-3"><span class="hostname"></span></div>
                <div class="d-flex mb-2">
                  <div>/etc/hostname</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Platform</div>
                </div>
                <div class="h1 mb-3"><span class="platformName"></span></div>
                <div class="d-flex mb-2">
                  <div>Version: <span class="platformVersion"></span></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Version</div>
                </div>
                <div class="h1 mb-3"><span class="version"></span></div>
                <div class="d-flex mb-2">
                  <div>SOMA Device Info</div>
                </div>
              </div>
            </div>
          </div>
        </div>


        ';
      }
      else {
        $deviceInfoHead = '';
        $deviceInfoBox = '';
      }



    }
    else {
      $showBox	 		  = 'style="display: none;"';
      $colSize	 	   	= 'col-sm-12 col-lg-6';
      $playerAPICall 	= FALSE;
      $playerAPI 			= NULL;
      $status 				= strtolower(Translation::of('offline'));
      $statusColor 		= 'danger';
      $navigation 		= '';
      $script 				= '';
      $newAsset				= '';
      $bulkDelete			= '';
      $management			= '';
      $reboot 				= '';
      $statusBanner   = '';

      if(checkAddress($player['address'])){
        $status		 		= strtolower(Translation::of('online'));
        $statusColor 	= 'success';
        $statusBanner = '<div class="alert alert-danger alert-dismissible" role="alert">  <b>'.Translation::of('msg.no_screenly_api').' - </b> '.Translation::of('msg.no_data_collected').'</div>';
      }
    }
    // TODO: Device Information and Process view need to build up
    echo '
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-auto">
        <!--
          <h2 class="page-title">
            Playerinformation
          </h2>
          -->
          <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
            <li class="breadcrumb-item"><a href="index.php?site=players">Player</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="index.php?site=players&action=view&playerID='.$playerID.'">'.$playerName.'</a></li>
          </ol>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ml-auto" '.$showBox.'>

          '.$deviceInfoHead.'
          <a href="http://'.$player['address'].'" target="_blank" class="btn btn-info ml-3 d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z"></path>
              <circle cx="12" cy="12" r="9"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <polyline points="15 15 15 9 9 9"></polyline>
            </svg>
            Webinterface
          </a>
          <a href="http://'.$player['address'].'" target="_blank" class="btn btn-info ml-3 d-sm-none btn-icon" aria-label="Create new report">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z"></path>
              <circle cx="12" cy="12" r="9"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <polyline points="15 15 15 9 9 9"></polyline>
            </svg>
          </a>
        </div>
      </div>
    </div>
    <div class="row row-deck row-cards">
      <div class="'.$colSize.'">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="subheader">Information</div>
              <div class="ml-auto lh-1 text-muted">
                <a href="#" data-playerid="'.$player['playerID'].'" class="editPlayerOpen">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4"></path>
                    <line x1="13.5" y1="6.5" x2="17.5" y2="10.5"></line>
                  </svg>
                </a>
                <a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="index.php?site=players&action=delete&playerID='.$player['playerID'].'">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg>
                </a>
              </div>
            </div>
            <div class="h1 mb-3">'.$playerName.'</div>
            <div class="d-flex mb-2">
              <div>'.$playerLocation.'</div>
            </div>
          </div>
        </div>
      </div>
      <div class="'.$colSize.'">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="subheader">Status</div>
            </div>
            <div class="h1 mb-3">'.$status.'</div>
            <div class="d-flex mb-2">
              <div>'.$player['address'].'</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3" '.$showBox.'>
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="subheader">Display</div>
            </div>
            <div class="h1 mb-3">'.strtoupper($displayPower).'</div>
            <div class="d-flex mb-2">
              <div>'.$displayRes.'</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3" '.$showBox.'>
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="subheader">Asset Control</div>
            </div>
            <div class="btn-group d-flex mt-4" role="group" aria-label="Basic example">
              <button type="button" data-playerID="'.$player['playerID'].'" data-order="previous" class="changeAsset btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z"></path>
                  <polyline points="11 7 6 12 11 17"></polyline>
                  <polyline points="17 7 12 12 17 17"></polyline>
                </svg>
                Asset
              </button>
              <button type="button" data-playerID="'.$player['playerID'].'" data-order="next" class="changeAsset btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z"></path>
                  <polyline points="7 7 12 12 7 17"></polyline>
                  <polyline points="13 7 18 12 13 17"></polyline>
                </svg>
                Asset
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    '.$deviceInfoBox.'

    <div class="container-fluid p-0">
      <div class="row">
        <div class="col-xs-12 col-sm-4 col-lg-3 order-sm-4 order-lg-4 order-xl-4" '.$showBox.'>
          <div class="card">
            <img src="'.$loadingImage.'" class="card-img-top player" data-src="'.$player['address'].'" alt="...">
          </div>

          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Player Control</div>
              </div>
              <a href="#" data-toggle="modal" data-target="#confirmDeleteAssets" data-href="index.php?site=players&action=view&playerID=21&action2=deleteAllAssets&playerID='.$player['playerID'].'" class="btn btn-warning btn-block mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z"></path>
                  <path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2"></path>
                  <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                  <path d="M10 12l4 4m0 -4l-4 4"></path>
                </svg>
                Clean Assets
              </a>
              <button data-playerid="'.$player['playerID'].'" class="btn btn-danger btn-block mt-2 reboot">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z"></path>
                  <path d="M7 6a7.75 7.75 0 1 0 10 0"></path>
                  <line x1="12" y1="4" x2="12" y2="12"></line>
                </svg>
                Reboot
              </button>

            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-8 col-lg-9 order-sm-3 order-lg-3 order-xl-3" '.$showBox.'>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Assets</h3>
              <div class="col-auto ml-auto">
                '.$newAsset.'
              </div>
            </div>
            <div class="card-body border-bottom py-3">
              <div class="d-flex">
                <div class="text-muted">
                  Show
                  <div class="mx-2 d-inline-block">
                    <select class="form-select form-select-sm" id="assetLength_change">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">All</option>
                    </select>
                  </div>
                  entries
                </div>
                <div class="ml-auto text-muted">
                  Search:
                  <div class="ml-2 d-inline-block">
                    <input type="text" class="form-control form-control-sm" id="assetSearch">
                  </div>
                </div>
              </div>
            </div>
            <div class="table-responsive">
            ';
  if($playerAPICall && $playerAPI != 'authentication error 401'){
    echo '
          <table class="table vertical-center" id="assets">
            <thead class="text-primary">
              <tr>
                <th></th>
                <th data-priority="1">'.Translation::of('name').'</th>
                <th data-priority="3">'.Translation::of('status').'</th>
                <th>Date</th>
                <th class="d-none">'.Translation::of('show').'</th>
                <th data-priority="2"> </th>
              </tr>
            </thead>
            <tbody>
                ';
    for($i=0; $i < sizeof($playerAPI); $i++)  {
      $startAsset				= explode("T", $playerAPI[$i]['start_date']);
      $startAssetTime		= explode("+", $startAsset['1']);
      $startAssetTimeHM	= explode(":", $startAssetTime['0']);
      $start						= date('d.m.Y', strtotime($startAsset['0']));
      $start_date				= date('Y-m-d', strtotime($startAsset['0']));
      $start_time				= $startAssetTimeHM['0'].':'.$startAssetTimeHM['1'];
      $endAsset					= explode("T", $playerAPI[$i]['end_date']);
      $endAssetTime			= explode("+", $endAsset['1']);
      $endAssetTimeHM		= explode(":", $endAssetTime['0']);
      $end_time					= $endAssetTimeHM['0'].':'.$endAssetTimeHM['1'];

      if (strpos($endAsset['0'], '9999') === false) {
        $end				= date('d.m.Y', strtotime($endAsset['0']));
        $end_date		= date('Y-m-d', strtotime($endAsset['0']));
      } else {
        $end				= Translation::of('forever');
        $end_date		= $endAsset['0'];
      }

      $yes 							= '<span class="badge bg-success m-2" data-asset_id="'.$playerAPI[$i]['asset_id'].'">  '.strtolower(Translation::of('active')).'  </span>';
      $no 							= '<span class="badge bg-danger m-2" data-asset_id="'.$playerAPI[$i]['asset_id'].'">  '.strtolower(Translation::of('inactive')).'  </span>';
      $playerAPI[$i]['is_enabled'] == 1 ? $active = $yes : $active = $no;
      if($playerAPI[$i]['mimetype'] == 'webpage'){
        $mimetypeIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><line x1="3.6" y1="9" x2="20.4" y2="9"></line><line x1="3.6" y1="15" x2="20.4" y2="15"></line><path d="M11.5 3a17 17 0 0 0 0 18"></path><path d="M12.5 3a17 17 0 0 1 0 18"></path></svg>';
      }
      else if($playerAPI[$i]['mimetype'] == 'video'){
        $mimetypeIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect><line x1="8" y1="4" x2="8" y2="20"></line><line x1="16" y1="4" x2="16" y2="20"></line><line x1="4" y1="8" x2="8" y2="8"></line><line x1="4" y1="16" x2="8" y2="16"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="16" y1="8" x2="20" y2="8"></line><line x1="16" y1="16" x2="20" y2="16"></line></svg>';
      }
      else {
        $mimetypeIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="15" y1="8" x2="15.01" y2="8"></line><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="M4 15l4 -4a3 5 0 0 1 3 0l 5 5"></path><path d="M14 14l1 -1a3 5 0 0 1 3 0l 2 2"></path></svg>';
      }

      if($playerAPI[$i]['is_active'] == 1){
        $shown = Translation::of('shown');
        $shown_class = '';
      } else {
        $shown = Translation::of('hidden');
        $shown_class = 'class="asset-hidden"';
      }
      // TODO: add title to buttons
      echo '
              <tr id="'.$playerAPI[$i]['asset_id'].'" data-playerID="'.$player['playerID'].'"'.$shown_class.'>
                <td>'.$player['playerID'].'</td>
                <td>'.$mimetypeIcon.' '.$playerAPI[$i]['name'].'</td>
                <td>'.$active.'</td>
                <td><span class="d-block d-sm-none"><br /></span>'.Translation::of('start').': '.$start.'<br />'.Translation::of('end').':&nbsp;&nbsp;&nbsp;'.$end.'</td>
                <td class="d-none">'.$shown.'</td>
                <td>
                  <button class="changeState btn btn-info btn-icon mb-1" data-asset_id="'.$playerAPI[$i]['asset_id'].'" data-player_id="'.$player['playerID'].'" title="switch on/off"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M7 6a7.75 7.75 0 1 0 10 0"></path><line x1="12" y1="4" x2="12" y2="12"></line></svg></button>
                  <button class="options btn btn-warning btn-icon mb-1" data-asset="'.$playerAPI[$i]['asset_id'].'" data-player_id="'.$player['playerID'].'" data-name="'.$playerAPI[$i]['name'].'" data-start-date="'.$start_date.'" data-start-time="'.$start_time.'" data-end-date="'.$end_date.'" data-end-time="'.$end_time.'" data-duration="'.$playerAPI[$i]['duration'].'"
                  data-uri="'.$playerAPI[$i]['uri'].'" title="edit"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path><path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path><line x1="16" y1="5" x2="19" y2="8"></line></svg></button>
                  <a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="index.php?site=players&action=view&playerID='.$player['playerID'].'&action2=deleteAsset&id='.$player['playerID'].'&asset='.$playerAPI[$i]['asset_id'].'" class="btn btn-danger btn-icon mb-1" title="delete"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg></a>
                </td>
              </tr>
      ';
    }
    echo '
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex align-items-center">
          <p class="m-0 text-muted" id="dataTables_info"></p>
          <span class="pagination m-0 ml-auto" id="dataTables_paginate"></span>
        </div>
    ';
  }
  else {
    echo  '
          <div class="alert alert-danger">
            <span><b>'.Translation::of('msg.no_screenly_api').' - </b> '.Translation::of('msg.no_data_collected').'</span>
          </div>
    ';
  }
  echo '

          </div>
        </div>
      </div>
    </div>
    '.$statusBanner.'

    <!-- newAsset -->
    <div class="modal modal-blur fade" id="newAsset" tabindex="-1" role="dialog" aria-labelledby="newAssetModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newAssetModalLabel">New Asset</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <label class="form-label">Upload Mode</label>
            <div class="form-selectgroup-boxes row mb-3">
              <div class="col-lg-6">
                <label class="form-selectgroup-item">
                  <input type="radio" name="add_asset_mode" class="form-selectgroup-input" value="view_url" checked>
                  <span class="form-selectgroup-label d-flex align-items-center p-3">
                    <span class="mr-3">
                      <span class="form-selectgroup-check"></span>
                    </span>
                    <span class="form-selectgroup-label-content">
                      <span class="form-selectgroup-title strong mb-1">'.Translation::of('url').'</span>
                    </span>
                  </span>
                </label>
              </div>
              <div class="col-lg-6">
                <label class="form-selectgroup-item">
                  <input type="radio" name="add_asset_mode" class="form-selectgroup-input" value="view_upload">
                  <span class="form-selectgroup-label d-flex align-items-center p-3">
                    <span class="mr-3">
                      <span class="form-selectgroup-check"></span>
                    </span>
                    <span class="form-selectgroup-label-content">
                      <span class="form-selectgroup-title strong mb-1">'.Translation::of('upload').'</span>
                    </span>
                  </span>
                </label>
              </div>
            </div>
          </div>
          <div class="view_url tab">
            <form id="assetNewForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST">
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">'.Translation::of('asset_url').'</label>
                  <input name="url" type="text" pattern="^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&\'\(\)\*\+,;=.]+$" class="form-control" id="InputNewAssetUrl" placeholder="http://www.example.com" autofocus>
                </div>
              </div>
              <div class="modal-footer">
                <input name="id" type="hidden" value="'.$player['playerID'].'" />
                <input name="mimetype" type="hidden" value="webpage" />
                <input name="newAsset" type="hidden" value="1" />
                <button type="button" class="btn btn-link mr-auto" data-dismiss="modal">'.Translation::of('close').'</button>
                <button type="submit" name="saveAsset" class="btn btn-success">'.Translation::of('save').'</button>
              </div>
            </form>
          </div>
          <div class="view_upload tab" style="display: none;">
              <div class="modal-body">
                <div class="mb-3">
                  <form action="'.checkHTTP($player['address']).$player['address'].'/api/v1/file_asset" class="dropzone drop">
                    <div class="fallback">
                      <input name="file" type="file" multiple />
                    </div>
                  </form>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-link  close_modal" data-close="#newAsset">'.Translation::of('close').'</button>
              </div>
          </div>
        </div>
      </div>
    </div>

    <!-- editAsset -->
    <div class="modal modal-blur fade" id="editAsset" tabindex="-1" role="dialog" aria-labelledby="editAssetModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editAssetModalLabel">'.Translation::of('edit_asset').'</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="assetEditForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">'.Translation::of('name').'</label>
                <input name="name" type="text" class="form-control" id="InputAssetName" placeholder="'.Translation::of('name').'" value="Name" />
              </div>
              <div class="mb-3">
                <label class="form-label">'.Translation::of('url').'</label>
                <input name="name" type="text" class="form-control" id="InputAssetUrl" disabled="disabled" value="url" />
              </div>
              <div class="row">
                <div class="col-lg-8">
                  <div class="mb-3">
                    <label class="form-label">'.Translation::of('start').'</label>
                    <div class="input-icon caltime-padding">
                      <input name="start_date" type="text" id="InputAssetStart" value="'.date('Y-m-d', strtotime('now')).'" class="form-control" placeholder="'.Translation::of('start_date').'" />
                      <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="input-icon caltime-padding">
                      <input name="start_time" type="text" id="InputAssetStartTime" value="12:00" class="form-control" placeholder="'.Translation::of('start_time').'" />
                      <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 9 15"></polyline></svg>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-8">
                  <div class="mb-3">
                    <label class="form-label">'.Translation::of('end').'</label>
                    <div class="input-icon caltime-padding">
                      <input name="end_date" type="date" class="form-control" id="InputAssetEnd" placeholder="'.Translation::of('end_date').'" value="'.date('Y-m-d', strtotime('+1 week')).'" />
                      <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="input-icon caltime-padding">
                      <input name="end_time" type="time" class="form-control" id="InputAssetEndTime" placeholder="'.Translation::of('end_time').'" value="12:00" />
                      <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 9 15"></polyline></svg>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">'.Translation::of('duration_in_sec').'</label>
                <input name="duration" type="number" class="form-control" id="InputAssetDuration" value="30" />
              </div>
            </div>
            <div class="modal-footer">
              <input name="updateAsset" type="hidden" value="1" />
              <input name="asset" id="InputAssetId"type="hidden" value="1" />
              <input name="id" id="InputSubmitId" type="hidden" value="'.$player['playerID'].'" />
              <button type="button" class="btn btn-link mr-auto" data-dismiss="modal">'.Translation::of('close').'</button>
              <button type="submit" class="btn btn-warning ">'.Translation::of('update').'</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- confirmReboot -->
    <div class="modal modal-blur fade" id="confirmReboot" tabindex="-1" role="dialog" aria-labelledby="confirmRebootModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">'.Translation::of('attention').'!</h5>
          </div>
          <div class="modal-body">
            '.Translation::of('msg.reboot_really_player').'
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">'.Translation::of('cancel').'</button>
            <button class="exec_reboot btn  btn-danger" title="'.Translation::of('reboot_now').'">'.Translation::of('reboot_now').'</button>
          </div>
        </div>
      </div>
    </div>

    <!-- confirmDeleteAssets -->
    <div class="modal modal-blur fade" id="confirmDeleteAssets" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteAssets" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">'.Translation::of('attention').'!</h5>
          </div>
          <div class="modal-body">
            '.Translation::of('msg.clean_all_assets').'
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link mr-auto" data-dismiss="modal">'.Translation::of('cancel').'</button>
            <a class="btn btn-danger btn-ok">'.Translation::of('delete').'</a>
          </div>
        </div>
      </div>
    </div>
    ';
  }
  else {
    sysinfo('danger', Translation::of('msg.no_player_submitted'));
    redirect('index.php');
  }
}
else {
  $playerSQL = $db->query("SELECT * FROM player ORDER BY name ASC");

  if(getPlayerCount() > 0){
    echo'
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-auto">
          <h2 class="page-title">
            Player Overview
          </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ml-auto d-print-none">
          <a href="javascript:void(0)" data-toggle="modal" data-target="#newPlayer" class="btn btn-primary ml-3 d-none d-sm-inline-block" data-toggle="modal" data-target="#modal-report">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
            Add Player
          </a>
          <a href="javascript:void(0)" data-toggle="modal" data-target="#newPlayer" class="btn btn-primary ml-3 d-sm-none btn-icon" data-toggle="modal" data-target="#modal-report" aria-label="Create new report">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
          </a>
        </div>
      </div>
    </div>
  <div class="row">
    ';
    while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
      if($player['name'] == ''){
        $name	 		= Translation::of('no_player_name');
        $imageTag = Translation::of('no_player_name').' '.$player['playerID'];
      }
      else {
        $name 		= $player['name'];
        $imageTag = $player['name'];
      }
      echo'
      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6" data-string="'.$name.'">
        <div class="card card-sm">
          <a href="index.php?site=players&action=view&playerID='.$player['playerID'].'" class="d-block"><img src="'.$loadingImage.'" data-src="'.$player['address'].'" alt="'.$imageTag.'" class="player card-img-top"></a>
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="lh-sm">
                <div>'.$name.'</div>
              </div>
              <div class="ml-auto">
                <a href="index.php?site=players&action=view&playerID='.$player['playerID'].'" class="text-muted">
                  '.$player['address'].'
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      ';
    }
    echo '
  </div>
  ';
  }
  else {
    echo '
    <div class="container-xl d-flex flex-column justify-content-center">
      <div class="empty">
        <div class="empty-icon">
          <img src="./static/illustrations/undraw_printing_invoices_5r4r.svg" height="128" class="mb-4"  alt="">
        </div>
        <p class="empty-title h3">No player found</p>
        <p class="empty-subtitle text-muted">
          All players are listed here. But currently none has been set up yet!
        </p>
        <div class="empty-action">
          <a href=".#" data-toggle="modal" data-target="#newPlayer" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
            Add your first player
          </a>
        </div>
      </div>
    </div>
    ';
  }
}
// TODO: validator not validate!
echo '



<!-- editPlayer -->
<div class="modal modal-blur fade" id="editPlayer" tabindex="-1" role="dialog" aria-labelledby="newPlayerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPlayerModalLabel">'.Translation::of('edit_name', ['name' => '<span id="playerNameTitle"></span>']).'</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="playerFormEdit" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">'.Translation::of('player_name').'</label>
            <input name="name" type="text" class="form-control" id="InputPlayerNameEdit" placeholder="'.Translation::of('enter_player_name').'" autofocus />
          </div>
          <div class="row">
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label">'.Translation::of('ip_address').'</label>
                <input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="'.Translation::of('no_valid_ip').'" type="text" class="form-control" id="InputAdressEdit" placeholder="192.168.1.100" required />
              </div>
            </div>
            <div class="col-lg-8">
              <div class="mb-3">
                <label class="form-label">'.Translation::of('player_location').'</label>
                <input name="location" type="text" class="form-control" id="InputLocationEdit" placeholder="'.Translation::of('enter_player_location').'" />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">'.Translation::of('username').'</label>
                <input name="user" type="text" class="form-control" id="InputUserEdit" placeholder="'.Translation::of('username').'" />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">'.Translation::of('password').'</label>
                <input name="pass" type="password" class="form-control" id="InputPasswordEdit" placeholder="'.Translation::of('password').'" />
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <input name="playerID" id="playerIDEdit" type="hidden" value="" />
          <button type="button" class="btn btn-link mr-auto" data-dismiss="modal">'.Translation::of('close').'</button>
          <button type="submit" name="updatePlayer" class="btn btn-warning" value="1">'.Translation::of('update').'</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- confirmDelete -->
<div class="modal modal-blur fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">'.Translation::of('attention').'!</h5>
      </div>
      <div class="modal-body">
        '.Translation::of('msg.delete_really_entry').'
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link mr-auto" data-dismiss="modal">'.Translation::of('cancel').'</button>
        <a class="btn btn-danger btn-ok">'.Translation::of('delete').'</a>
      </div>
    </div>
  </div>
</div>
';
