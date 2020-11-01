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
            Add-on Module
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');



if((isset($argv) && $argv['1'] != '')){
  include("ssh.class.php");
  $server_host = $argv['1'];
  $server_port = $argv['2'];
  $server_login = $argv['3'];
  $server_password = $argv['4'];

  //TODO Change branch!
  $cmd = "rm -rf install.sh && wget https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitoring-addon/v3.0/install.sh && chmod +x install.sh && ./install.sh";
  //$cmd = "ls";
  $cmd = "echo '" . $server_password . "' | sudo -S " . $cmd;

  $ssh = new ssh($server_host, $server_login, $server_password, $server_port);
  $test = $ssh('whoami');
  $test = preg_replace( "/\r|\n/", "", $test);
  $output = "\n[START] Installation of SOMA Add-ons\n";
  $output .= date("Y-m-d hh:mm:ss")."\n";
  if($test == $server_login){
    $output .= $ssh($cmd);
  }
  else $output .= "Wrong credentials\n";

  $output .= date("Y-m-d hh:mm:ss")."\n";
  $output .= "[END] Installation of SOMA Add-ons\n";
  $output .= "\n";
  $output .= "=========================================================\n";
  $output .= "\n";

  echo $output;

  //$db->exec("UPDATE player SET logOutput='".$output."' WHERE address='".$server_host."'");

  exit();

}
elseif(isset($_POST['addonInstall']) && $_POST['addonInstall'] == 'true' && $_POST['host'] != ''){
  $host = $_POST['host'];
  $port = $_POST['port'];
  $user = $_POST['user'];
  $pass = $_POST['pass'];
  shell_exec("php /var/www/html/monitor/assets/php/addon.php ".$host." ".$port." ".$user." ".$pass.' > /dev/null 2>/dev/null &');
  //shell_exec('php /var/www/html/monitor/assets/php/addon.php 192.168.178.54 22 pi raspberry');
  die('Installation started - This may take a while...');
}
else{
  $playerSQL 		= $db->query("SELECT name, address FROM player ORDER BY address");

  echo '

  <div class="container-xl">
    <!-- Page title -->
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-auto">
          <h2 class="page-title">
            Screenly OSE Monitoring Add-ons
          </h2>
        </div>
        <div class="col-auto ml-auto">
            <a href="#" data-toggle="modal" data-target="#manual_install" class="btn btn-info">
              Manual Installation
            </a>

        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Player</h3>
          </div>
          <div class="card-body border-bottom py-3">
            <div class="d-flex">
              <div class="text-muted">
                Show
                <div class="mx-2 d-inline-block">
                  <select class="form-select form-select-sm" id="addonLength_change">
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
                  <input type="text" class="form-control form-control-sm" id="addonSearch">
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table vertical-center" id="addon">
              <thead class="text-primary">
                <tr>
                  <th>Name</th>
                  <th>IP Address</th>
                  <th>Online</th>
                  <th>Monitor Output</th>
                  <th>Device Info</th>
                  <th><span class="d-none d-sm-block">Options</span></th>
                </tr>
              </thead>
              <tbody>
                  ';
                  while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
                  $name = $player['name'];
                  $ip   = $player['address'];
                  $monitorOutput   = $player['monitorOutput'];
                  $deviceInfo   = $player['deviceInfo'];

                  $offline = '<span class="badge bg-dark">offline</span>';
                  $not = '<span class="badge bg-danger">Not installed</span>';
                  if(checkAddress($ip, '50')){
                    $counter = 0;
                    $onlineS = '<span class="badge bg-green">Online</span>';
                    if($monitorOutput != '0'){
                    $monitorS = '<span class="badge bg-success">Version '.$monitorOutput.'</span>';
                    $counter++;
                    } else $monitorS = $not;

                    if($deviceInfo != '0'){
                      $deviceS = '<span class="badge bg-success">Version '.$deviceInfo.'</span>';
                      $counter++;
                    } else $deviceS = $not;

                    if($counter == 0) $optionS = '<button class="btn btn-success btn-sm installAddon" data-src="'.$player['address'].'" data-header="Install">Install SOMA</button>';
                    else $optionS = '<button class="btn btn-warning btn-sm installAddon" data-src="'.$player['address'].'" data-header="Reinstall">Reinstall</button>';
                  }
                  else {
                    $onlineS = $offline;
                    $monitorS = '';
                    $deviceS = '';
                  }

                  echo '
                  <tr>
                  <td>'.$player['name'].'</td>
                  <td>'.$player['address'].'</td>
                  <td>'.$onlineS.'</td>
                  <td>'.$monitorS.'</td>
                  <td>'.$deviceS.'</td>
                  <td>'.$optionS.'</td>
                  </tr>';
                  }
      echo '
              </tbody>
            </table>
          </div>
          <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted" id="dataTables_info"></p>
            <span class="pagination m-0 ml-auto" id="dataTables_paginate"></span>
          </div>
        </div>
      </div>
    </div>
  </div>


    <!-- manual_install -->
		<div class="modal fade" id="manual_install" tabindex="-1" role="dialog" aria-labelledby="newAddonModalLabel" aria-hidden="true">
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
						<input type="text" class="form-control" id="InputBash" onClick="this.select();" value="bash <(curl -sL https://git.io/Jf900)">
						After that the player restarts and the addon has been installed.<br />
					</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
				</div>
			</div>
		</div>

    <!-- installAddon -->
		<div class="modal fade" id="installAddon" tabindex="-1" role="dialog" aria-labelledby="newPlayerModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="editPlayerModalLabel"><span id="headerText"></span> Addon</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form id="installAddonForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
              <div class="row">
                <div class="col-lg-10">
                  <div class="mb-3">
                  <label class="form-label">Enter the IP address</label>
                  <input name="host" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="No valid IPv4 address" type="text" class="form-control" id="InputAdressEdit" placeholder="192.168.1.100" required />
                  <div class="help-block with-errors"></div>
                  </div>
                </div>
                <div class="col-lg-2">
                  <div class="mb-3">
                    <label class="form-label">Port</label>
                    <input name="port" type="text" class="form-control" placeholder="22" value="22" />
                  </div>
                </div>
              </div>
              <div class="mb-3">
								<label class="form-label">Username *</label>
								<input name="user" type="text" class="form-control" id="InputLoginname" placeholder="pi" autofocus/>
							</div>
							<div class="mb-3">
								<label class="form-label">Password *</label>
								<input name="pass" type="password" class="form-control" id="InputPassword" placeholder="raspberry" />
							</div>
              <div class="mb-3">
                <div class="alert alert-warning" role="alert">
                  * This information will not be saved!
                </div>
							</div>
					  </div>
            <div class="modal-footer">
              <input name="addonInstall" type="hidden" value="true" />
              <button type="button" class="btn btn-secondary close_modal mr-auto" data-close="#installer" data-dismiss="modal">Close</button>
              <button type="submit" name="updatePlayer" id="btnText" class="btn btn-warning install">Install</button>
            </div>
          </form>
				</div>
			</div>
		</div>
    ';


}
