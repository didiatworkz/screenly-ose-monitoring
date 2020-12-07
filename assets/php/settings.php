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
           Settings Module
_______________________________________
*/

// Translation DONE

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

$_moduleName = Translation::of('settings');
$_moduleLink = 'index.php?site=settings';

// Public Access Link
if(isset($_GET['generateToken']) && $_GET['generateToken'] == 'yes' && (getGroupID($loginUserID) == 1 || hasSettingsPublicRight($loginUserID))){
  $now 	 = time();
  $token = md5($loginUsername.$loginPassword.$now);

  if($token){
    $db->exec("UPDATE settings SET token='".$token."' WHERE settingsID='1'");
    sysinfo('success', Translation::of('msg.new_token_generated'));
    systemLog($_moduleName, Translation::of('msg.new_token_generated'), $loginUserID, 1);
    redirect($backLink);
  } else sysinfo('danger', 'Error!');
}

if(isset($_GET['view']) && $_GET['view'] == 'profile'){

  if (!empty($_FILES)) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    $data = base64_encode(file_get_contents( $_FILES["file"]["tmp_name"]));
    $file = 'data:'.$check['mime'].';base64,'.$data;
    $newfilename = md5($loginUsername).'.txt';
    $targetPath = dirname( __FILE__ ).'/../img/avatars/';
    $targetFile =  $targetPath.$newfilename;
    file_put_contents($targetFile, $file);
  }

  if (isset($_GET['removeavatar']) && $_GET['removeavatar'] == '1') {
    $newfilename = md5($loginUsername).'.txt';
    $targetPath = dirname( __FILE__ ).'/../img/avatars/';
    $targetFile =  $targetPath.$newfilename;
    unlink($targetFile);
    redirect($backLink, 0);
  }

  echo '
  <div class="container-xl">
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-auto">
          <h2 class="page-title">
            '.Translation::of('profile_settings').'
          </h2>
          <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
            <li class="breadcrumb-item"><a href="'.$_moduleLink.'">'.$_moduleName.'</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="'.$_moduleLink.'&view=profile">'.Translation::of('profile_settings').'</a></li>
          </ol>
        </div>
        <div class="col-auto ml-auto d-print-none">
        </div>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-3 order-lg-1 mb-4">
        <div class="sticky-top">
          <div class="card">
            <div class="card-body text-center">
              <div class="mb-3">
                '.getUserAvatar($loginUserID, 'avatar-xl').'
              </div>
              <div class="card-title mb-1">'.$loginFullname.'</div>
              <div class="text-muted">'.$loginGroupName.'</div>
            </div>
          </div>
          <div class="card">
            <div class="card-body text-center">
              <div class="text-muted">'.Translation::of('change_avatar').'</div>
              <div class="mb-3">
                <form action="'.$_moduleLink.'&view=profile" class="avatar_upload dropzone">
                  <div class="fallback">
                    <input name="file" type="file" />
                  </div>
                </form>
                <a href="'.$_moduleLink.'&view=profile&removeavatar=1" class="btn btn-outline-danger btn-sm btn-block">'.Translation::of('remove_avatar').'</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="card card-lg">
          <form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
            <div class="card-body">
            <h2 id="personal">'.Translation::of('personal').'</h2>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('firstname').'</label>
                <div class="col">
                  <input name="firstname" type="text" class="form-control" id="InputFirstname" placeholder="John" value="'.$loginFirstname.'" />
                </div>
              </div>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('name').'</label>
                <div class="col">
                  <input name="name" type="text" class="form-control" id="InputName" placeholder="Doe" value="'.$loginName.'" />
                </div>
              </div>
              <hr />
              <h2 id="account">'.Translation::of('account').'</h2>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('change_username').'</label>
                <div class="col">
                  <input name="username" type="text" class="form-control" id="InputUsername" placeholder="'.Translation::of('new_username').'" value="'.$loginUsername.'" require />
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('change_username').'</label>
                <div class="col">
                  <input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="'.Translation::of('new_password').'" />
                </div>
              </div>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('change_username').'</label>
                <div class="col">
                <input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="'.Translation::of('confirm_password').'" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" />
                <div class="help-block with-errors"></div>
                </div>
              </div>
              <hr />
              <h2 id="account">'.Translation::of('player_control').'</h2>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('refresh_time_player').'</label>
                <div class="col">
                  <input name="refreshscreen" type="text" class="form-control" id="InputSetRefresh" placeholder="5" value="'.$loginRefreshTime.'" required />
                </div>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center">
              <a href="'.$_moduleLink.'" class="btn btn-link mr-auto">'.Translation::of('cancel').'</a>
              <button type="submit" name="saveAccount" class="btn btn-primary">'.Translation::of('update').'</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  ';
}
else if(isset($_GET['view']) && $_GET['view'] == 'system' && hasSettingsSystemRight($loginUserID)){
  $sqlite = SQLite3::version();
  $server_output = NULL;
  $server = shell_exec('hostnamectl');
  $serverOut = array();
  $server = preg_split('/\r\n|\r|\n/', $server);
  $n = 0;
  for ($i=0; $i < count($server); $i++) {
    if($server[$i] != '') {
      $serverOut[$n] = explode(':', $server[$i]);
      for ($j=0; $j < count($serverOut[$n]); $j++) {
        $serverOut[$n][$j] = trim($serverOut[$n][$j]);
      }
      if($serverOut[$n]['0'] == 'Machine ID' || $serverOut[$n]['0'] == 'Boot ID' || $serverOut[$n]['0'] == 'Icon name') continue;
      else $server_output .= '
      <tr>
        <td>'.$serverOut[$n]['0'].':</td>
        <td>'.$serverOut[$n]['1'].'</td>
      </tr>
      ';
      $n++;
    }
  }

  echo '
  <div class="container-xl">
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-auto">
          <h2 class="page-title">
            '.Translation::of('system_settings').'
          </h2>
          <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
            <li class="breadcrumb-item"><a href="'.$_moduleLink.'">'.$_moduleName.'</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="'.$_moduleLink.'&view=system">'.Translation::of('system_settings').'</a></li>
          </ol>
        </div>
        <div class="col-auto ml-auto d-print-none"></div>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-4 order-lg-1 mb-4">
        <div class="sticky-top">
          <div class="card">
            <div class="card-body">
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
              '.$server_output.'
              <tr>
                <td>'.Translation::of('php_version').':</td>
                <td>'.phpversion().'</td>
              </tr>
              <tr>
                <td>'.Translation::of('sqlite_version').':</td>
                <td>'.$sqlite['versionString'].'</td>
              </tr>
              <tr>
                <td>'.Translation::of('json_version').':</td>
                <td>'.phpversion('json').'</td>
              </tr>
              <tr>
                <td>'.Translation::of('ssh2_version').':</td>
                <td>'.phpversion('ssh2').'</td>
              </tr>
              </table>
              <a href="https://github.com/didiatworkz/screenly-ose-monitor/issues/new/choose" class="btn btn-block btn-secondary" target="_blank">'.Translation::of('you_need_help').'</a>
            </div>
          </div>
          <h5 class="subheader">'.Translation::of('on_this_page').'</h5>
          <ul class="list-unstyled">
            <li class="toc-entry toc-h2"><a href="#system">'.Translation::of('system_settings').'</a></li>
            <li class="toc-entry toc-h2"><a href="#player">'.Translation::of('player_control_settings').'</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card card-lg">
          <div class="card-body">
            <form id="settingsForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
              <h2 id="system">'.Translation::of('system').'</h2>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('title').'</label>
                <div class="col">
                  <input name="name" type="text" class="form-control" id="InputSetName" placeholder="'.Translation::of('somo').'" value="'.$set['name'].'" required />
                </div>
              </div>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('timezone').'</label>
                <div class="col">
                  <select class="form-select" name="timezone">
                    '.timezone($set['timezone']).'
                  </select>
                </div>
              </div>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('design').'</label>
                <div class="col">
                  <div class="row row-sm">
                    <div class="col-6">
                      <label class="form-colorinput form-colorinput-light align-middle">
                        <input name="color" type="radio" name="design" value="0" class="form-colorinput-input" '.($set['design'] == '0' ? 'checked' : '').'>
                        <span class="form-colorinput-color bg-white"></span>
                      </label>
                      '.Translation::of('light_mode').'
                    </div>
                    <div class="col-6">
                      <label class="form-colorinput align-middle">
                        <input name="color" type="radio" name="design" value="1" class="form-colorinput-input" '.($set['design'] == '1' ? 'checked' : '').'>
                        <span class="form-colorinput-color bg-dark"></span>
                      </label>
                      '.Translation::of('dark_mode').'
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('debug_mode').'</label>
                <div class="col">
                <label class="form-check form-check-single form-switch">
                  <input class="form-check-input" name="debug" type="checkbox"'.checkboxState($set['debug']).'>
                </label>
                </div>
              </div>
              <hr />
              <h2 id="player">'.Translation::of('player_control').'</h2>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('delay_of_weeks').'</label>
                <div class="col">
                  <input name="end_date" type="text" class="form-control" id="InputSetEndDate" placeholder="1" value="'.$set['end_date'].'" required />
                </div>
              </div>
              <div class="form-group mb-3 row">
                <label class="form-label col-3 col-form-label">'.Translation::of('assets_duration').'</label>
                <div class="col">
                  <input name="duration" type="text" class="form-control" id="InputSetDuration" placeholder="30" value="'.$set['duration'].'" required />
                </div>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center">
              <a href="'.$_moduleLink.'" class="btn btn-link mr-auto">'.Translation::of('cancel').'</a>
              <button type="submit" name="saveSettings" class="btn btn-primary">'.Translation::of('update').'</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  ';
}
else if(isset($_GET['view']) && $_GET['view'] == 'publicaccess' && hasSettingsPublicRight($loginUserID)){

  $tokenLink = 'http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/_public.php?key='.$set['token'];

  echo '
  <div class="container-xl">
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-auto">
          <h2 class="page-title">
            '.Translation::of('public_access_settings').'
          </h2>
          <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
            <li class="breadcrumb-item"><a href="'.$_moduleLink.'">'.$_moduleName.'</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="'.$_moduleLink.'&view=publicaccess">'.Translation::of('public_access_settings').'</a></li>
          </ol>
        </div>
        <div class="col-auto ml-auto d-print-none">
        </div>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-3 order-lg-1 mb-4">
        <div class="sticky-top">
          <div class="card">
            <div class="card-body text-center">
              <button class="btn btn-outline-info btn-block mb-2" id="open_token">'.Translation::of('open_in_new_window').'</button>
              <button class="btn btn-outline-secondary btn-block mb-2" id="add_dark">'.Translation::of('activate_darkmode').'</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="card card-lg">
          <form id="settingsForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
            <div class="card-body">
              <div class="form-group">
                <label for="InputSetName">'.Translation::of('public_access_link').'</label>
                <input type="text" class="form-control" id="InputSetToken" onClick="this.select();" value="'.$tokenLink.'" />
              </div>
            </div>
            <div class="card-footer d-flex align-items-center">
              <a href="'.$_moduleLink.'" class="btn btn-link mr-auto">'.Translation::of('cancel').'</a>
              <a href="'.$_moduleLink.'&view=publicaccess&generateToken=yes" class="btn btn-primary">'.Translation::of('generate_token').'</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  ';
}
else if(isset($_GET['view']) && $_GET['view'] == 'log' && isAdmin($loginUserID)){
  if(isset($_GET['action']) && $_GET['action'] == 'reset'){
    systemLog($_moduleName, 'Admin Log: Start Log reset', $loginUserID, 0);
    $db->exec("DELETE FROM log");
    sysinfo('success', Translation::of('msg.log_delete_successfully'));
    redirect($backLink, 0);
  }
  else {
    $logSQL = $db->query("SELECT * FROM `log` ORDER BY logTime");
    echo '
    <div class="container-xl">
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-auto">
            <h2 class="page-title">
              '.Translation::of('admin_logs').'
            </h2>
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
              <li class="breadcrumb-item"><a href="'.$_moduleLink.'">'.$_moduleName.'</a></li>
              <li class="breadcrumb-item active" aria-current="page"><a href="'.$_moduleLink.'&view=log">'.Translation::of('admin_logs').'</a></li>
            </ol>
          </div>
          <div class="col-auto ml-auto">
            <a href="#" data-toggle="modal" data-target="#confirmMessage" data-text="'.Translation::of('msg.delete_really_log').'" data-status="danger" data-href="'.$_moduleLink.'&view=log&action=reset" class="btn btn-danger"  title="delete">
              '.Translation::of('reset_log').'
            </a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">'.Translation::of('protocols').'</h3>
        </div>
        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <div class="text-muted">
              '.Translation::of('show').'
              <div class="mx-2 d-inline-block">
                <select class="form-select form-select-sm" id="logLength_change">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="-1">'.Translation::of('all').'</option>
                </select>
              </div>
              '.strtolower(Translation::of('entries')).'
            </div>
            <div class="ml-auto text-muted">
              '.Translation::of('search').':
              <div class="ml-2 d-inline-block">
                <input type="text" class="form-control form-control-sm" id="logSearch">
              </div>
            </div>
          </div>
        </div>
        <div class="table-responsive">

      <table class="table vertical-center" id="log">
        <thead class="text-primary">
          <tr>
            <th>'.Translation::of('log_time').'</th>
            <th>'.Translation::of('user').'</th>
            <th>'.Translation::of('module').'</th>
            <th>'.Translation::of('info').'</th>
          </tr>
        </thead>
        <tbody>
            ';
        while($log = $logSQL->fetchArray(SQLITE3_ASSOC)){
          if($log['userID'] == 0) $user = 'SYSTEM';
          else $user = getUserName($log['userID']);
          echo '
            <tr>
              <td>'.date('Y-m-d H:i:s', $log['logTime']).'</td>
              <td>'.$user.'</td>
              <td><span class="badge bg-secondary p-1">  '.$log['moduleName'].'  </span></td>
              <td>'.$log['info'].'</td>
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
    echo '

  </div>
</div>
    ';
  }
}
else {
  echo '
  <div class="container-xl">
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-auto">
          <h2 class="page-title">
            '.$_moduleName.'
          </h2>
        </div>
        <div class="col-auto ml-auto d-print-none">
        </div>
      </div>
    </div>
    <div class="row">
    '.$update_info.'
    </div>
    <div class="row row-deck">
      <div class="col">
        <div class="card">
          <a href="'.$_moduleLink.'&view=profile">
            <div class="card-body text-center">
              <div class="mb-3">
                <span class="avatar avatar-xl"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="7" r="4"></circle><path d="M5.5 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path></svg></span>
              </div>
              <div class="card-title mb-1">'.Translation::of('profile_settings').'</div>
              <div class="text-muted">'.Translation::of('name').', '.Translation::of('login').', '.Translation::of('design').'</div>
            </div>
          </a>
        </div>
      </div>';
      if($loginGroupID == 1 || hasSettingsSystemRight($loginUserID)) echo'
      <div class="col-md-6">
        <div class="card">
          <a href="'.$_moduleLink.'&view=system">
            <div class="card-body text-center">
              <div class="mb-3">
                <span class="avatar avatar-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M7 10h3v-3l-3.5 -3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1 -3 3l-6-6a6 6 0 0 1 -8 -8l3.5 3.5"></path></svg>
                </span>
              </div>
              <div class="card-title mb-1">'.Translation::of('system_settings').'</div>
              <div class="text-muted">'.Translation::of('api_settings').', '.Translation::of('default_settings').', '.Translation::of('design').'</div>
            </div>
          </div>
        </a>
      </div>';
      echo '
    </div>
    <div class="row row-deck">';
    if($loginGroupID == 1 || hasSettingsPublicRight($loginUserID)) echo'
      <div class="col">
        <div class="card">
          <a href="'.$_moduleLink.'&view=publicaccess">
            <div class="card-body text-center">
              <div class="mb-3">
                <span class="avatar avatar-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><line x1="3.6" y1="9" x2="20.4" y2="9"></line><line x1="3.6" y1="15" x2="20.4" y2="15"></line><path d="M11.5 3a17 17 0 0 0 0 18"></path><path d="M12.5 3a17 17 0 0 1 0 18"></path></svg>
                </span>
              </div>
              <div class="card-title mb-1">Public Access Settings</div>
              <div class="text-muted">'.Translation::of('design').', '.Translation::of('link').'</div>
            </div>
          </div>
        </a>
      </div>';
    if($loginGroupID == 1)
      echo'
      <div class="col-md-3 col-lg-6 col-xl-3">
        <div class="card">
          <a href="'.$_moduleLink.'&view=log">
            <div class="card-body text-center">
              <div class="mb-3">
                <span class="avatar avatar-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11"></path><line x1="8" y1="8" x2="12" y2="8"></line><line x1="8" y1="12" x2="12" y2="12"></line><line x1="8" y1="16" x2="12" y2="16"></line></svg>
                </span>
              </div>
              <div class="card-title mb-1">'.Translation::of('admin_logs').'</div>
              <div class="text-muted">'.Translation::of('protocols').', '.Translation::of('activities').'</div>
            </div>
          </div>
        </a>
      </div>';
      if($loginGroupID == 1 || hasSettingsUserRight($loginUserID)) echo'
      <div class="col">
        <div class="card">
          <a href="index.php?site=usermanagement">
            <div class="card-body text-center">
              <div class="mb-3">
                <span class="avatar avatar-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="9.5" y1="11" x2="9.51" y2="11"></line><line x1="14.5" y1="11" x2="14.51" y2="11"></line><path d="M9.5 15a3.5 3.5 0 0 0 5 0"></path><path d="M7 5h1v-2h8v2h1a3 3 0 0 1 3 3v9a3 3 0 0 1 -3 3v1h-10v-1a3 3 0 0 1 -3 -3v-9a3 3 0 0 1 3 -3"></path></svg>
                </span>
              </div>
              <div class="card-title mb-1">'.Translation::of('user_settings').'</div>
              <div class="text-muted">'.Translation::of('create').', '.Translation::of('edit').', '.Translation::of('remove').'</div>
            </div>
          </div>
        </a>
      </div>';
      if($loginGroupID == 1) echo'
      <div class="col-md-3 col-lg-6 col-xl-3">
        <div class="card">
          <a href="index.php?site=groupmanagement">
            <div class="card-body text-center">
              <div class="mb-3">
                <span class="avatar avatar-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="5" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="16" r="1"></circle><path d="M8 11v-5a4 4 0 0 1 8 0"></path></svg>
                </span>
              </div>
              <div class="card-title mb-1">'.Translation::of('group_settings').'</div>
              <div class="text-muted">'.Translation::of('create_groups').', '.Translation::of('manage_rights').'</div>
            </div>
          </div>
        </a>
      </div>';
      echo'
    </div>
  </div>
  ';
}
