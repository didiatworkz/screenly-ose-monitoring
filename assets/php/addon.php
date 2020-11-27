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

// Translation DONE

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

$_moduleName = 'Add-on';
$_moduleLink = 'index.php?site=addon';

if((isset($argv) && $argv['1'] != '')){
  include_once('ssh.class.php');
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

  $db->exec("UPDATE player SET logOutput='".$output."' WHERE address='".$server_host."'");

  exit();

}
elseif(isset($_POST['addonInstall']) && $_POST['addonInstall'] == 'true' && $_POST['host'] != ''){
  $host = $_POST['host'];
  $port = $_POST['port'];
  $user = $_POST['user'];
  $pass = $_POST['pass'];

  $test =  shell_exec("apt policy php-ssh2");
  if (strpos($test, 'Installed: (none)') === false) {
      shell_exec("php /var/www/html/monitor/assets/php/addon.php ".$host." ".$port." ".$user." ".$pass.' > /dev/null 2>/dev/null &');
      //shell_exec('php /var/www/html/monitor/assets/php/addon.php 192.168.178.54 22 pi raspberry');
      die(Translation::of('soma.start_installation'));
  } else die(Translation::of('soma.no_package_found'));

}
else {
  if(hasModuleRight($loginUserID, 'addon')){
    $playerSQL 		= $db->query("SELECT playerID, name, address, monitorOutput, deviceInfo, status, logOutput FROM player ORDER BY address");

    echo '

    <div class="container-xl">
      <!-- Page title -->
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-auto">
            <h2 class="page-title">
              '.Translation::of('soma').' [BETA]
            </h2>
          </div>
          <div class="col-auto ml-auto">
              <a href="#" data-toggle="modal" data-target="#manual_install" class="btn btn-info">
                '.Translation::of('manual_installation').'
              </a>

          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">'.Translation::of('player').'</h3>
            </div>
            <div class="card-body border-bottom py-3">
              <div class="d-flex">
                <div class="text-muted">
                  '.Translation::of('show').'
                  <div class="mx-2 d-inline-block">
                    <select class="form-select form-select-sm" id="addonLength_change">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">All</option>
                    </select>
                  </div>
                  '.strtolower(Translation::of('entries')).'
                </div>
                <div class="ml-auto text-muted">
                  '.Translation::of('search').':
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
                    <th>'.Translation::of('name').'</th>
                    <th>'.Translation::of('ip_address').'</th>
                    <th>'.Translation::of('online').'</th>
                    <th>'.Translation::of('monitor_output').'</th>
                    <th>'.Translation::of('device_info').'</th>
                    <th><span class="d-none d-sm-block">'.Translation::of('options').'</span></th>
                  </tr>
                </thead>
                <tbody>
                    ';
                    while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
                    $name = $player['name'];
                    $id   = $player['playerID'];
                    $ip   = $player['address'];
                    $log  = $player['logOutput'];
                    $monitorOutput   = $player['monitorOutput'];
                    $deviceInfo   = $player['deviceInfo'];

                    $offline = '<span class="badge bg-danger p-1">'.strtolower(Translation::of('offline')).'</span>';
                    $not = '<span class="badge bg-danger p-1">'.Translation::of('not_installed').'</span>';
                    if(checkAddress($ip, '50')){
                      $counter = 0;
                      $onlineS = '<span class="badge bg-green p-1">'.strtolower(Translation::of('online')).'</span>';
                      if($monitorOutput != '0'){
                      $monitorS = '<span class="badge bg-success p-1">'.Translation::of('version').' '.$monitorOutput.'</span>';
                      $counter++;
                      } else $monitorS = $not;

                      if($deviceInfo != '0'){
                        $deviceS = '<span class="badge bg-success p-1">Version '.$deviceInfo.'</span>';
                        $counter++;
                      } else $deviceS = $not;

                      if($log != ''){
                        $logModal= '
                        <div class="modal modal-blur fade" id="log-modal-'.$id.'" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Full width modal</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci animi beatae delectus deleniti dolorem eveniet facere fuga iste nemo nesciunt nihil odio perspiciatis, quia quis reprehenderit sit tempora totam unde.
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-white mr-auto" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        ';
                        $logBtn = '<button class="btn btn-info btn-sm">i</button>';
                      }
                      else {
                        $logModal = '';
                        $logBtn = '';
                      }

                      if($counter == 0) $optionS = '<button class="btn btn-outline-secondary installAddon" data-src="'.$player['address'].'" data-header="Install">'.Translation::of('soma.install').'</button>';
                      else $optionS = '<button class="btn btn-outline-warning installAddon" data-src="'.$player['address'].'" data-header="Reinstall">'.Translation::of('reinstall').'</button>'.$logBtn;
                      $counter = 0;
                    }
                    else {
                      $onlineS = $offline;
                      $monitorS = '';
                      $deviceS = '';
                      $optionS = '';
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
  						<h5 class="modal-title" id="newAddonModalLabel">'.Translation::of('addon').'</h5>
  						<button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
  							<span aria-hidden="true">&times;</span>
  						</button>
  					</div>
  					<div class="modal-body">
  						<img src="assets/img/addon.png" class="img-fluid mx-auto d-block" alt="addon" style="height: 180px" />
              '.Translation::of('soma.manual_install_text1').'
              <input type="text" class="form-control" id="InputBash" onClick="this.select();" value="bash <(curl -sL https://git.io/Jf900)">
  						'.Translation::of('soma.manual_install_text2').'
  					</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">'.Translation::of('close').'</button>
            </div>
  				</div>
  			</div>
  		</div>

      <!-- installAddon -->
  		<div class="modal fade" id="installAddon" tabindex="-1" role="dialog" aria-labelledby="newPlayerModalLabel" aria-hidden="true">
  			<div class="modal-dialog" role="document">
  				<div class="modal-content">
  					<div class="modal-header">
  						<h5 class="modal-title" id="editPlayerModalLabel"><span id="headerText"></span> '.Translation::of('addon').'</h5>
  						<button type="button" class="close" data-dismiss="modal" aria-label="'.Translation::of('close').'">
  							<span aria-hidden="true">&times;</span>
  						</button>
  					</div>
  					<div class="modal-body">
  						<form id="installAddonForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
                <div class="row">
                  <div class="col-lg-10">
                    <div class="mb-3">
                    <label class="form-label">'.Translation::of('enter_player_ip').'</label>
                    <input name="host" pattern="\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b" data-error="'.Translation::of('no_valid_ip').'" type="text" class="form-control" id="InputAdressEdit" placeholder="192.168.1.100" required />
                    <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <div class="mb-3">
                      <label class="form-label">'.Translation::of('port').'</label>
                      <input name="port" type="text" class="form-control" placeholder="22" value="22" />
                    </div>
                  </div>
                </div>
                <div class="mb-3">
  								<label class="form-label">'.Translation::of('username').' *</label>
  								<input name="user" type="text" class="form-control" id="InputLoginname" placeholder="pi" autofocus/>
  							</div>
  							<div class="mb-3">
  								<label class="form-label">'.Translation::of('password').' *</label>
  								<input name="pass" type="password" class="form-control" id="InputPassword" placeholder="raspberry" />
  							</div>
                <div class="mb-3">
                  <div class="alert alert-warning" role="alert">
                    * '.Translation::of('msg.this_information_will_not_be_saved').'
                  </div>
  							</div>
  					  </div>
              <div class="modal-footer">
                <input name="addonInstall" type="hidden" value="true" />
                <button type="button" class="btn btn-secondary close_modal mr-auto" data-close="#installer" data-dismiss="modal">'.Translation::of('close').'</button>
                <button type="submit" name="updatePlayer" id="btnText" class="btn btn-warning install">'.Translation::of('install').'</button>
              </div>
            </form>
  				</div>
  			</div>
  		</div>
      ';

    }
    else {
      sysinfo('danger', 'No Access to this module!');
      redirect($backLink, 2);
    }
}
