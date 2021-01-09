<?php
	// FUNCTIONS
	require_once('_functions.php');
	// TRANSLATION CLASS
	require_once(__DIR__.'/assets/php/translation.php');
	use Translation\Translation;

echo'
<!doctype html>
<html lang="'.Translation::of('lang_tag').'">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>'._SYSTEM_NAME.'</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
		<link rel="apple-touch-icon" sizes="180x180" href="assets/img/fav/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="assets/img/fav/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="assets/img/fav/favicon-16x16.png">
		<link rel="manifest" href="assets/img/fav/site.webmanifest">
		<link rel="mask-icon" href="assets/img/fav/safari-pinned-tab.svg" color="#2f3949">
		<link rel="shortcut icon" href="assets/img/fav/favicon.ico">
		<meta name="msapplication-TileColor" content="#f5f7fb">
		<meta name="msapplication-config" content="assets/img/fav/browserconfig.xml">
		<meta name="theme-color" content="#f5f7fb">
    <!-- Libs CSS -->
    <link href="assets/libs/selectize/dist/css/selectize.css?t='.$set['updatecheck'].'" rel="stylesheet"/>
    <link href="assets/libs/flatpickr/dist/flatpickr.min.css?t='.$set['updatecheck'].'" rel="stylesheet"/>
    <link href="assets/libs/nouislider/distribute/nouislider.min.css?t='.$set['updatecheck'].'" rel="stylesheet"/>

		<link rel="stylesheet" href="assets/libs/DataTables/datatables.min.css?t='.$set['updatecheck'].'" />
		<link rel="stylesheet" href="assets/libs/dropzone/dropzone.min.css?t='.$set['updatecheck'].'">

    <!-- Tabler Core -->
    <link href="assets/css/tabler.min.css?t='.$set['updatecheck'].'" rel="stylesheet"/>
    <!-- Tabler Plugins -->
    <link href="assets/css/tabler-buttons.min.css?t='.$set['updatecheck'].'" rel="stylesheet"/>
    <link href="assets/css/monitor.css?t='.$set['updatecheck'].'" rel="stylesheet"/>

		<!-- Libs JS -->
		<script src="assets/js/jquery.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/js/jquery-ui.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/libs/DataTables/datatables.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/libs/dropzone/dropzone.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/php/dropzone_lang.js.php?t='.$set['updatecheck'].'"></script>
		<script src="assets/libs/flatpickr/dist/flatpickr.min.js?t='.$set['updatecheck'].'"></script>
		<!-- Tabler Core -->
		<script src="assets/js/tabler.min.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/js/bootstrap-notify.js?t='.$set['updatecheck'].'"></script>
		<script src="assets/js/validator.js?t='.$set['updatecheck'].'"></script>
    <style>
      body {
      	display: none;
      }
    </style>
  </head>';

		if($loggedIn){
			// Player Authentication
			$scriptAuthUsername = 'dummy';
			$scriptAuthPassword = 'dummy';

			if(isset($_GET['playerID']) && $_GET['playerID'] != ''){
				$scriptAuth = playerAuthentication($_GET['playerID']);

				if($scriptAuth["username"] != '' && $scriptAuth["password"] != ''){
					$scriptAuthUsername = $scriptAuth["username"];
					$scriptAuthPassword = $scriptAuth["password"];
				}
			}

			$scriptPlayerAuth = base64_encode($scriptAuthUsername.':'.$scriptAuthPassword);

			if($set['firstStart'] != 0) include('assets/php/firstStart.php');
			else {
				echo'
		<body class="antialiased'.$body_theme.'">
			<div class="page">
				';

				// TOP MENU
				include_once('assets/php/menu.php');

				// START CONTENT
				echo'
				<!-- START CONTENT -->
				<div class="content">
	        <div class="container-fluid">
					';

				if(isset($_GET['site'])){
					$moduleName = $_GET['site'];
					$siteName = ROOT_DIR.'/assets/php/'.$moduleName.'.php';
					if (@file_exists($siteName) && @file_get_contents($siteName, 0, NULL, 0, 1)) {
						if(in_array(basename($moduleName), $_modules)){
							include('assets/php/'.basename($moduleName).'.php');
						}	else sysinfo('danger', Translation::of('msg.module_not_allowed'));
					}	else sysinfo('danger', Translation::of('msg.module_not_exists'));
				}
				else {
					include('assets/php/dashboard.php');
				}
			}
			echo '
		</div>
		<!-- END CONTENT -->';

		if(hasPlayerAddRight($loginUserID)) echo'
		<!-- newPlayer -->
		<div class="modal modal-blur fade" id="newPlayer" tabindex="-1" role="dialog" aria-labelledby="newPlayerModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		    <div class="modal-content shadow">
		      <div class="modal-header">
		        <h5 class="modal-title">'.Translation::of('add_player').'</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
		          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
		        </button>
		      </div>
		      <div class="modal-body">
		        <label class="form-label">'.Translation::of('mode').'</label>
		        <div class="form-selectgroup-boxes row mb-3">
		          <div class="col-lg-6">
		            <label class="form-selectgroup-item">
		              <input type="radio" name="add_player_mode" class="form-selectgroup-input" value="view_manual" checked>
		              <span class="form-selectgroup-label d-flex align-items-center p-3">
		                <span class="mr-3">
		                  <span class="form-selectgroup-check"></span>
		                </span>
		                <span class="form-selectgroup-label-content">
		                  <span class="form-selectgroup-title strong mb-1">'.Translation::of('manual').'</span>
		                  <span class="d-block text-muted">'.Translation::of('add_player_manually').'</span>
		                </span>
		              </span>
		            </label>
		          </div>
		          <div class="col-lg-6">
		            <label class="form-selectgroup-item">
		              <input type="radio" name="add_player_mode" class="form-selectgroup-input" value="view_auto">
		              <span class="form-selectgroup-label d-flex align-items-center p-3">
		                <span class="mr-3">
		                  <span class="form-selectgroup-check"></span>
		                </span>
		                <span class="form-selectgroup-label-content">
		                  <span class="form-selectgroup-title strong mb-1">'.Translation::of('automatically').'</span>
		                  <span class="d-block text-muted">'.Translation::of('add_player_automatically').'</span>
		                </span>
		              </span>
		            </label>
		          </div>
		        </div>
		      </div>

		      <div class="view_manual tab">
		        <div class="modal-body">
		          <form id="playerForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
		            <div class="mb-3">
		              <label class="form-label">'.Translation::of('player_name').'</label>
		              <input name="name" type="text" class="form-control" id="InputPlayerName" placeholder="'.Translation::of('enter_player_name').'" autofocus />
		            </div>
		            <div class="row">
		              <div class="col-lg-4">
		                <div class="mb-3">
		                  <label class="form-label">'.Translation::of('ip_address').'</label>
		                  <input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="'.Translation::of('no_valid_ip').'" type="text" class="form-control" id="InputAdress" placeholder="192.168.1.100" required />
		                </div>
		              </div>
		              <div class="col-lg-8">
		                <div class="mb-3">
		                  <label class="form-label">'.Translation::of('player_location').'</label>
		                  <input name="location" type="text" class="form-control" id="InputLocation" placeholder="'.Translation::of('enter_player_location').'" />
		                </div>
		              </div>
		            </div>
		            <div class="mb-3">
		              <div class="form-label">'.Translation::of('player_authentication').'</div>
		              <label class="form-check form-switch">
		                <input class="form-check-input toggle_div" data-src=".authentication" type="checkbox">
		                <span class="form-check-label">'.Translation::of('player_is_protected').'</span>
		              </label>
		            </div>
		          </div>
		          <div class="modal-body authentication" style="display: none">
		            <div class="row">
		              <div class="col-lg-6">
		                <div class="mb-3">
		                  <label class="form-label">'.Translation::of('username').'</label>
		                  <input name="user" type="text" class="form-control" id="InputUser" autocomplete="section-newplayer username" placeholder="'.Translation::of('username').'" />
		                </div>
		              </div>
		              <div class="col-lg-6">
		                <div class="mb-3">
		                  <label class="form-label">'.Translation::of('password').'</label>
		                  <input name="pass" type="password" class="form-control" id="InputPassword" autocomplete="section-newplayer current-password" placeholder="'.Translation::of('password').'" />
		                </div>
		              </div>
		            </div>
		          </div>
		          <div class="modal-footer">
		            <a href="#" class="btn btn-link link-link" data-dismiss="modal">
		              '.Translation::of('close').'
		            </a>
		            <button type="submit" name="saveIP" class="btn btn-primary ml-auto">
		              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path><circle cx="12" cy="14" r="2"></circle><polyline points="14 4 14 8 8 8 8 4"></polyline></svg>
		              '.Translation::of('save').'
		            </button>
		          </div>
		        </form>
		      </div>
		      <div class="view_auto tab" style="display:none">
		        <div class="modal-body">
		          <form id="newPlayerDiscover" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
		            <div class="mb-3">
		              <label class="form-label">'.Translation::of('enter_ip_range').'</label>
		              <input name="range" pattern="^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(3[0-2]|[1-2][0-9]|[0-9]))$" data-error="No valid IPv4 address with CIDR" type="text" class="form-control" id="InputCIDR" placeholder="192.168.1.0/24" required />
		              <div class="help-block with-errors"></div>
		            </div>
		            <div class="mb-3">
		              <label class="form-label">'.Translation::of('status').'</label>
		              <hr />
		              <div id="discoverStatus"></div>
		            </div>
		          </div>
		          <div class="modal-footer">
		            <input name="userID" type="hidden" value="'.$loginUserID.'" />
		            <button type="button" class="btn btn-link link-link close_modal" data-close="#newPlayer">'.Translation::of('close').'</button>
		            <button type="submit" name="startDiscover" class="btn btn-primary ml-auto start_discovery">'.Translation::of('discovery').'</button>
		          </div>
		        </form>
		      </div>
		    </div>
		  </div>
		</div>';

		echo '
		<!-- info -->
		<div class="modal modal-blur fade" id="info" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content shadow">
					<div class="modal-header">
		        <h5 class="modal-title">'._SYSTEM_NAME.'</h5>
		      </div>
					<div class="modal-body">
					  <a href="https://atworkz.de" target="_blank"><img src="assets/img/atworkz-logo.png" alt="atworkz" class="img-fluid mx-auto d-block" /></a>
						<table class="table table-sm">
						  <tr>
						    <td>'.Translation::of('monitor_version').':</td>
						    <td>'.$systemVersion.'</td>
						  </tr>
							<tr>
						    <td>'.Translation::of('screenly_api').':</td>
						    <td>'.$apiVersion.'</td>
						  </tr>
							<tr>
						    <td>&nbsp;</td>
						    <td>'.(isAdmin($loginUserID) ? '<a href="index.php?site=settings&view=system">'.strtolower(Translation::of('more_information')).'</a>' : '&nbsp;').'</td>
						  </tr>
						  <tr>
						    <td>'.Translation::of('project').':</td>
						    <td><a href="https://github.com/didiatworkz/screenly-ose-monitor" target="_blank">GitHub</a></td>
						  </tr>
							<tr>
						    <td>'.Translation::of('copyright').':</td>
						    <td><a href="https://atworkz.de" target="_blank">atworkz.de</a></td>
						  </tr>
						  <tr>
						    <td>'.Translation::of('design').':</td>
						    <td>
									<a href="https://github.com/tabler/tabler/" target="_blank">tabler</a><br />
									<a href="https://undraw.co/" target="_blank">undraw illustrations</a>
								</td>
						  </tr>
						  <tr>
						    <td>'.Translation::of('scripts').':</td>
						    <td>
							  <a href="https://datatables.net" target="_blank">DataTables</a><br />
							  <a href="https://www.dropzonejs.com/" target="_blank">dropzoneJS</a><br />
							  <a href="http://bootstrap-notify.remabledesigns.com/" target="_blank">Bootstrap notify</a><br />
							  <a href="https://github.com/InterativaDigital/php-translation-class" target="_blank">PHP Translation Class</a><br />
							  <a href="https://github.com/members/ssh" target="_blank">SSH Client Class</a><br />
							</td>
						  </tr>
						</table>
	        </div>
					<div class="modal-footer">
            <button type="button" class="btn btn-secondary pull-right" data-dismiss="modal">'.Translation::of('close').'</button>
          </div>
				</div>
			</div>
		</div>';

		if(hasPlayerDeleteRight($loginUserID) || hasAssetDeleteRight($loginUserID) || hasSettingsUserDeleteRight($loginUserID) || getGroupID($loginUserID) == 1) echo'
		<!-- confirmMessage -->
		<div class="modal modal-blur fade" id="confirmMessage" tabindex="-1" role="dialog" aria-labelledby="confirmMessageModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content shadow">
		      <div class="modal-header">
		        <h5 class="modal-title">'.Translation::of('attention').'!</h5>
		      </div>
		      <div class="modal-body">
		        <span class="delete-text"></span>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-link mr-auto" data-dismiss="modal">'.Translation::of('cancel').'</button>
		        <a class="btn btn-ok">'.Translation::of('confirm').'</a>
		      </div>
		    </div>
		  </div>
		</div>
			';
		}
		else {
			if (isset($logedout)){
				sysinfo('success', '<i class="fa fa-check"></i> '.Translation::of('msg.logged_out_successfully'));
			}
			include('assets/php/login.php');
		}
		$db->close();

		echo '
		<footer class="footer footer-transparent">
			<div class="container">
				<div class="row text-center align-items-center flex-row-reverse">';
				if(!(isset($_GET['monitoring']) OR !$loggedIn)) echo '
					<div class="col-lg-auto ml-lg-auto">
						<ul class="list-inline list-inline-dots mb-0">
							<li class="list-inline-item"><a href="https://www.github.com/didiatworkz" target="_blank" class="link-secondary">Github</a></li>
							<li class="list-inline-item"><a href="javascript:void(0)" data-toggle="modal" data-target="#info" class="link-secondary">'.Translation::of('information').'</a></li>
							</ul>
					</div>';
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

var scriptPlayerAuth 	= "'.($loggedIn ? $scriptPlayerAuth : '10').'";
var settingsRefreshRate = "'.($loggedIn ? $loginRefreshTime : '5').'000";
var settingsRunerTime 	= "'.($loggedIn ? $runnerTime : 'FALSE').'";
var userAddonActive		= "'.($loggedIn ? $loginUserAddon : '0').'";
var uploadMaxSize		= '.($loggedIn ? $uploadMaxSize : '50').';
var playerAssetsOrder 	= "'.Translation::of('player_assets_order').'";
localStorage.removeItem("runnerExecute");

if (!(localStorage.getItem("notification_style") === null && localStorage.getItem("notification_message") === null)) {
	if(localStorage.getItem("notification_counter") == "1"){
		$.notify({icon: "tim-icons icon-bell-55",message: localStorage.getItem("notification_message")},{type: localStorage.getItem("notification_style"),timer: 2000 ,placement: {from: "bottom",align: "center"}, animate: {enter: "animated fadeInDown", exit: "animated fadeOutUp"}});
		console.log("unload");
		localStorage.removeItem("notification_message");
		localStorage.removeItem("notification_style");
		localStorage.removeItem("notification_counter");
	}
	else {
		var num = localStorage.getItem("notification_counter"),
		num = parseInt(num, 10);
		num--;
		localStorage.setItem("notification_counter", num);
	}
}

</script>
<script src="assets/js/monitor.js?t='.$set['updatecheck'].'"></script>
<script src="assets/libs/typeaheadjs/dist/typeahead.bundle.min.js?t='.$set['updatecheck'].'"></script>
<script src="assets/js/monitor_search.js?t='.$set['updatecheck'].'"></script>
<script src="assets/js/flatpickr.lang.js?t='.$set['updatecheck'].'"></script>
<script>
	document.body.style.display = "block"
	flatpickr.localize(flatpickr.l10ns.'.Translation::of('flatpickr.lang').');
</script>';
$totalTime = array_sum(explode(' ',  microtime())) - $_loadMessureStart; echo '<script>console.log("Loaded in: '.$totalTime.'")</script>
</body>
</html>
';
?>
