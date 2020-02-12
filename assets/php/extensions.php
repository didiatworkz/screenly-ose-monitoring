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
________________________________________
      Screenly OSE Monitor
        Extension Module
________________________________________
*/

function checkState($url){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 200);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER[ 'HTTP_USER_AGENT' ] );
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  $data = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if(($httpcode >= 200 && $httpcode < 300) || $httpcode == 401) return true;
  else return false;
}

if(isset($_POST['install']) && $_POST['install'] == 'yes'){
  include("ssh.class.php");
  $server_host = $_POST['address'];
  $server_login = $_POST['user'];
  $server_password = $_POST['password'];

  $cmd = "rm -rf addon.sh && wget https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitoring-addon/v3.0/addon.sh && chmod +x addon.sh && ./addon.sh";
  $cmd = "echo '" . $server_password . "' | sudo -S " . $cmd;

  header("HTTP/1.1 200 OK");
  echo json_encode('Start Installation');
  $ssh = new ssh($server_host, $server_login, $server_password);
  $output = $ssh($cmd);
  echo json_encode($output);
}
else{
  $playerSQL 		= $db->query("SELECT name, address FROM player ORDER BY address");

  echo '
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-10">
              <h5 class="title">Extension Management</h5>
            </div>
            <div class="col-md-2 float-right">
              <a href="#" data-toggle="modal" data-target="#addon" class="btn btn-info btn-sm btn-block"><i class="tim-icons icon-app"></i> Manual Installation</a>
              <a href="#" data-toggle="modal" data-target="#installer" class="btn btn-success btn-sm btn-block"><i class="tim-icons icon-app"></i> Auto Installation</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <table class="table" id="extension">
            <thead class="text-primary">
              <tr>
                <th>Name</th>
                <th>IP Address</th>
                <th>Screenshot</th>
                <th>API</th>
                <th><span class="d-none d-sm-block">Options</span></th>
              </tr>
            </thead>
            <tbody>';
  while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
      $name = $player['name'];
      $ip   = $player['address'];

      $offline = '<span class="badge badge-dark">offline</span>';
      $not = '<span class="badge badge-danger">Not installed</span>';
      if(checkState($ip)){
        if(checkState($ip.':9020/screen/screenshot.png')){
          $screenS = '<span class="badge badge-success">Version 1</span>';
        } else $screenS = $not;

        if(checkState($ip.':9020/index.php?get=version')){
          $apiV = callURL('GET', $player['address'].':9020/index.php?get=version', false, false, false);
          $apiV = json_decode($apiV, true);
          if($apiV['screenshotversion'] != ''){
            $screenS = '<span class="badge badge-success">Version '.$apiV['screenshotversion'].'</span>';
          }
          $apiS = '<span class="badge badge-success">Version '.$apiV['apiversion'].'</span>';
        } else $apiS = $not;
      }
      else {
        $screenS = $offline;
        $apiS = $offline;
      }

      echo '
      <tr>
        <td>'.$player['name'].'</td>
        <td>'.$player['address'].'</td>
        <td>'.$screenS.'</td>
        <td>'.$apiS.'</td>
        <td>xx</td>
      </tr>';
    }

    echo '</tbody>
    </table>
    </div>
    </div>
    </div>
    </div>

    <!-- addon -->
		<div class="modal fade" id="addon" tabindex="-1" role="dialog" aria-labelledby="newAddonModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="newAddonModalLabel">Addon</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<img src="assets/img/addon.png" class="img-fluid mx-auto d-block" alt="addon" style="height: 180px" />
						The Screenly OSE Monitoring addon allows you to retrieve even more data from the Screenly Player and process it in the monitor. <br />
						You have the possibility to get a "live" image of the player\'s output.<br /><br />
						To install, you have to log in to the respective Screenly Player via SSH (How it works: <a href="https://www.raspberrypi.org/documentation/remote-access/ssh/" target="_blank">here</a>) <br />and execute this command:<br />
						<input type="text" class="form-control" id="InputBash" onClick="this.select();" value="bash <(curl -sL http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/assets/tools/addon.sh)">
						After that the player restarts and the addon has been installed.<br />
						<button type="button" class="btn btn-secondary btn-sm pull-right" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

    <!-- installExtension -->
		<div class="modal fade" id="installer" tabindex="-1" role="dialog" aria-labelledby="newPlayerModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="editPlayerModalLabel">Install Extension</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form id="installExtension" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
							<div class="form-group">
								<label for="InputAdressEdit">Enter the IP address</label>
								<input name="address" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="No valid IPv4 address" type="text" class="form-control" id="InputAdressEdit" placeholder="192.168.1.100" required autofocus />
								<div class="help-block with-errors"></div>
							</div>
              <div class="form-group">
								<label for="InputLoginname">Username</label>
								<input name="user" type="text" class="form-control" id="InputLoginname" placeholder="pi" />
							</div>
							<div class="form-group">
								<label for="InputPassword">Password</label>
								<input name="password" type="password" class="form-control" id="InputPassword" placeholder="raspberry" />
							</div>
							<div class="form-group text-right">
								<input name="install" type="hidden" value="yes" />
								<button type="submit" name="updatePlayer" class="btn btn-sm btn-warning install">Install</button>
								<button type="button" class="btn btn-secondary btn-sm install_close" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
    ';


}
