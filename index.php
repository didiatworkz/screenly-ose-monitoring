<?php
	// SESSION OPTIONS
	session_set_cookie_params(36000, '/' );
	session_start();
	// TRANSLATION CLASS
	require_once(__DIR__.'/assets/php/translation.php');
	use Translation\Translation;
	// FUNCTIONS
	require_once('_functions.php');
echo'

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>'._SYSTEM_NAME.'</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <meta name="msapplication-TileColor" content="#206bc4"/>
    <meta name="theme-color" content="#206bc4"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="HandheldFriendly" content="True"/>
    <meta name="MobileOptimized" content="320"/>
    <meta name="robots" content="noindex,nofollow,noarchive"/>
		<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png" />
		<link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png" />
		<link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png" />
		<link rel="manifest" href="assets/img/site.webmanifest" />
		<link rel="mask-icon" href="assets/img/safari-pinned-tab.svg" color="#1e1e2f" />
		<link rel="shortcut icon" href="assets/img/favicon.ico" />
		<meta name="msapplication-TileColor" content="#1e1e2f" />
		<meta name="msapplication-config" content="assets/img/browserconfig.xml" />
		<meta name="theme-color" content="#1e1e2f" />
    <!-- Libs CSS -->
    <link href="assets/libs/selectize/dist/css/selectize.css" rel="stylesheet"/>
    <link href="assets/libs/flatpickr/dist/flatpickr.min.css" rel="stylesheet"/>
    <link href="assets/libs/nouislider/distribute/nouislider.min.css" rel="stylesheet"/>

		<link rel="stylesheet" href="assets/tools/DataTables/datatables.min.css" />
		<link rel="stylesheet" href="assets/tools/dropzone/dropzone.min.css">

    <!-- Tabler Core -->
    <link href="assets/css/tabler.min.css" rel="stylesheet"/>
    <!-- Tabler Plugins -->
    <link href="assets/css/tabler-buttons.min.css" rel="stylesheet"/>
    <!-- <link href="assets/css/demo.min.css" rel="stylesheet"/> -->
    <link href="assets/css/monitor.css" rel="stylesheet"/>
    <style>
      body {
      	display: none;
      }
    </style>
  </head>';



		if($loggedIn){
			// Player authentication
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

			echo'
	<body class="antialiased">
		<div class="page">
			';

			// INCLUDE: Top menubar
			include_once('assets/php/menu.php');

			// START CONTENT
			echo'
			<div class="content">
        <div class="container-fluid">
				';

			if(isset($_GET['site'])){
				$moduleName = $_GET['site'];

				if (@file_get_contents('assets/php/'.$moduleName.'.php', 0, NULL, 0, 1)) {
					if(in_array(basename($moduleName), $_modules)){
						include('assets/php/'.basename($moduleName).'.php');
					}	else sysinfo('danger', Translation::of('msg.module_not_allowed'));
				}	else sysinfo('danger', Translation::of('msg.module_not_exists'));
			}
			else {
				include('assets/php/players.php');
			}
			echo '


		</div>
		<!-- END CONTENT -->



		<!-- addon -->
		<div class="modal modal-blur fade" id="addon" tabindex="-1" role="dialog" aria-labelledby="newAddonModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="newAddonModalLabel">'.Translation::of('addon').'</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<img src="assets/img/addon.png" class="img-fluid mx-auto d-block" alt="addon" style="height: 180px" />
						The Screenly OSE Monitoring addon allows you to retrieve even more data from the Screenly Player and process it in the monitor. <br />
						You have the possibility to get a "live" image of the player\'s output.<br /><br />
						To install, you have to log in to the respective Screenly Player via SSH (How it works: <a href="https://www.raspberrypi.org/documentation/remote-access/ssh/" target="_blank">here</a>) <br />and execute this command:<br />
						<input type="text" class="form-control" id="InputBash" onClick="this.select();" value="bash <(curl -sL https://git.io/Jf900)">
						After that the player restarts and the addon has been installed.<br />
						<button type="button" class="btn btn-secondary btn-sm pull-right" data-dismiss="modal">'.Translation::of('close').'</button>
					</div>
				</div>
			</div>
		</div>

		<!-- info -->
		<div class="modal modal-blur fade" id="info" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
		        <h5 class="modal-title">'._SYSTEM_NAME.'</h5>
		      </div>
					<div class="modal-body">
					  <a href="https://atworkz.de" target="_blank"><img src="assets/img/atworkz-logo.png" class="img-fluid mx-auto d-block" /></a>
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
						    <td>'.Translation::of('server_ip').':</td>
						    <td>'.$_SERVER['SERVER_ADDR'].($_SERVER['SERVER_PORT'] != '80' ? ':'.$_SERVER['SERVER_PORT'] : '').'</td>
						  </tr>
							<tr>
						    <td>'.Translation::of('php_version').':</td>
						    <td>'.phpversion().'</td>
						  </tr>
							<tr>
						    <td>&nbsp;</td>
						    <td>&nbsp;</td>
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
						    <td><a href="https://github.com/tabler/tabler/" target="_blank">tabler</a></td>
						  </tr>
						  <tr>
						    <td>'.Translation::of('scripts').':</td>
						    <td>
							  <a href="https://datatables.net" target="_blank">DataTables</a><br />
							  <a href="https://www.dropzonejs.com/" target="_blank">dropzoneJS</a><br />
							  <a href="http://bootstrap-notify.remabledesigns.com/" target="_blank">Bootstrap notify</a><br />
							  <a href="https://github.com/InterativaDigital/php-translation-class" target="_blank">PHP Translation Class</a><br />
							</td>
						  </tr>
						</table>
	        </div>
					<div class="modal-footer">
            <button type="button" class="btn btn-secondary pull-right" data-dismiss="modal">'.Translation::of('close').'</button>
          </div>
				</div>
			</div>
		</div>

		<!-- publicLink -->
		<div class="modal modal-blur fade" id="publicLink" tabindex="-1" role="dialog" aria-labelledby="publicLinkModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">'.Translation::of('public_access_link').'</h5>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="InputSetToken">'.Translation::of('public_access_link_info').'</label>
							<input type="text" class="form-control" id="InputSetToken" onClick="this.select();" value="http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/index.php?monitoring=1&key='.$set['token'].'" />
						</div>
						<div class="form-group text-right">
							<a href="index.php?generateToken=yes" class="btn btn-info btn-sm">'.Translation::of('generate_token').'</a>
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('close').'</button>
						</div>
					</div>
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
						<div class="form-group text-right">
							<a class="btn btn-danger btn-ok btn-sm">'.Translation::of('delete').'</a>
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">'.Translation::of('cancel').'</button>
						</div>
					</div>
				</div>
			</div>
		</div>
			';
		}
	  else if((isset($_GET['monitoring']) && $_GET['monitoring'] == '1') && isset($_GET['key'])){
	    include('assets/php/publicLink.php');
	  }
		else {
			if (isset($logedout)){
				sysinfo('success', '<i class="fa fa-check"></i> '.Translation::of('msg.logged_out_successfully'));
			}
			echo '
			<body class="antialiased border-top-wide border-primary d-flex flex-column">
				<div class="flex-fill d-flex flex-column justify-content-center">
		      <div class="container-tight py-6">
						<form id="Login" action="'.$_SERVER['PHP_SELF'].'" class="card card-md" method="POST">
		          <div class="card-body">
		            <h2 class="mb-5 text-center">'._SYSTEM_NAME.'</h2>
		            <div class="mb-3">
		              <label class="form-label">Username</label>
		              <input name="user" type="text" class="form-control" placeholder="'.Translation::of('username').'" autofocus autocomplete="off">
		            </div>
		            <div class="mb-2">
		              <label class="form-label">Password</label>
									<input name="password" type="password" class="form-control" placeholder="'.Translation::of('password').'">
		            </div>
		            <div class="form-footer">
		              <button type="submit" name="Login" class="btn btn-primary btn-block" value="1">'.Translation::of('login').'</button>
		            </div>
		          </div>
		        </form>
		      </div>
		    </div>
			';
		}
		$db->close();

		echo '
		<footer class="footer footer-transparent">
			<div class="container">
				<div class="row text-center align-items-center flex-row-reverse">
					<div class="col-lg-auto ml-lg-auto">';
						if(!(isset($_GET['monitoring']) OR !$loggedIn)) echo '
						<ul class="list-inline list-inline-dots mb-0">
							<li class="list-inline-item"><a href="https://www.github.com/didiatworkz" target="_blank" class="link-secondary">Github</a></li>
							<li class="list-inline-item"><a href="javascript:void(0)" data-toggle="modal" data-target="#info" class="link-secondary">'.Translation::of('information').'</a></li>
							</ul>';
					echo'</div>
					<div class="col-12 col-lg-auto mt-3 mt-lg-0">';
						if(isset($pagination)) echo $pagination; echo '
						&copy '.date('Y').' by <a href="https://www.atworkz.de" target="_blank">atworkz.de</a>
					</div>
				</div>
			</div>
		</footer>
  </div>
</div>
<!-- Libs JS -->
<script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js?1588343458"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/tools/DataTables/datatables.min.js"></script>
<script src="assets/tools/dropzone/dropzone.min.js"></script>
<script src="assets/js/jquery-ui.min.js"></script>
<!-- Tabler Core -->
<script src="assets/js/tabler.min.js?1588343458"></script>
<script type="text/javascript" src="/assets/js/bootstrap-notify.js"></script>
<script type="text/javascript" src="assets/js/validator.js"></script>
<script type="text/javascript">

var scriptPlayerAuth = "'.($loggedIn ? $scriptPlayerAuth : '10').'";
var settingsRefreshRate = "'.($loggedIn ? $loginRefreshTime : '5').'000";

if (!(localStorage.getItem("notification_style") === null && localStorage.getItem("notification_message") === null)) {
	if(localStorage.getItem("notification_counter") == "0"){
		$.notify({icon: "tim-icons icon-bell-55",message: localStorage.getItem("notification_message")},{type: localStorage.getItem("notification_style"),timer: 2000 ,placement: {from: "top",align: "center"}});
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
<script type="text/javascript" src="assets/js/monitor.js"></script>
<script>
	document.body.style.display = "block"
</script>';
if(isset($_GET['showToken']) && $_GET['showToken'] == '1'){
	echo '
		<script>
			$(\'#publicLink\').modal(\'show\');
		</script>';
}
$totalTime = array_sum(explode(' ',  microtime())) - $_loadMessureStart; echo '<script>console.log("Loaded in: '.$totalTime.'")</script>
</body>
</html>
';
?>
