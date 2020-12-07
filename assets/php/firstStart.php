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
            First Start
_______________________________________
*/

//Translation: DONE

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

$_moduleName = Translation::of('setup_wizard');
$_moduleLink = 'index.php';

if($set['firstStart'] != 0){

  // GET: action:startup - Skip firstStart screen
  if((isset($_GET['step']) && $_GET['step'] != '')){
    $db->exec("UPDATE settings SET firstStart='".$_GET['step']."' WHERE settingsID='1'");
    redirect($_moduleLink);
  }

  echo '
  <body class="border-top-wide border-primary d-flex flex-column wizard">
     <div class="flex-fill d-flex flex-column justify-content-center">
       <div class="container-tight py-4">
         <div class="card">
           <div class="card-body text-center py-4 p-sm-5">
             <img src="assets/img/undraw_Setup_wizard_re_nday.svg" height="256" class="mb-n2"  alt="">
             <h1 class="mt-5">'.Translation::of('somo_name').'<br />'.Translation::of('version').' '.$systemVersion.'</h1>
             <p class="text-muted">'.Translation::of('wizard.welcome').'<br />'.Translation::of('wizard.thank_you').'<br />- didiatworkz</p>
           </div>
           ';
//// TODO: Name and Firstname integration
  if($set['firstStart'] == 1){
    if($loginUsername == 'demo' && $loginPassword == 'fe01ce2a7fbac8fafaed7c982a04e229'){
      echo '
        <div class="hr-text hr-text-center hr-text-spaceless">'.Translation::of('user_account').'</div>
          <form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" >
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">'.Translation::of('username').'</label>
                <input name="username" type="text" class="form-control" id="InputUsername" placeholder="'.Translation::of('enter_user_name').'" autocomplete="section-wizard username" autofocus required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="mb-3">
                <label class="form-label">'.Translation::of('change_password').'</label>
                <input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="'.Translation::of('new_password').'" autocomplete="section-wizard new-password" required />
              </div>
              <div class="mb-3">
                <input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="'.Translation::of('confirm_password').'" autocomplete="section-wizard new-password" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="hr-text hr-text-center hr-text-spaceless mt-5 mb-2">'.Translation::of('personal_information').'</div>
              <div class="mb-3">
                <label class="form-label">'.Translation::of('firstname').'</label>
                <input name="firstname" type="text" class="form-control" id="InputFirstname" placeholder="John" autocomplete="section-wizard given-name" required />
              </div>
              <div class="mb-3">
                <label class="form-label">'.Translation::of('familyname').'</label>
                <input name="name" type="text" class="form-control" id="InputName" placeholder="Doe" autocomplete="section-wizard family-name" required />
              </div>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-4">
              <div class="progress">
                <div class="progress-bar" style="width: 25%" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                  <span class="visually-hidden"></span>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="btn-list justify-content-end">
                <input name="firstStartUser" type="hidden" value="1"/>
                <input name="mode" type="hidden" value="firstStep"/>
                <button type="submit" name="saveAccount" class="btn btn-primary">'.Translation::of('continue').'</button>
              </div>
            </div>
            </div>
        </form>
      </div>
    </div>
      ';
  } else redirect('index.php?step=2');
}
  else if($set['firstStart'] == 2){
    echo '
    <div class="hr-text hr-text-center hr-text-spaceless">'.Translation::of('somo_settings').'</div>
      <form id="settingsForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">'.Translation::of('somo_name').'</label>
            <input name="name" type="text" class="form-control" id="InputSetName" placeholder="'.Translation::of('somo').'" value="'.$set['name'].'" required />
          </div>
          <div class="mb-3">
            <label class="form-label">'.Translation::of('timezone').'</label>
            <select class="form-select" name="timezone">
              '.timezone($set['timezone']).'
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">'.Translation::of('refresh_time_player').'</label>
            <input name="refreshscreen" type="text" class="form-control" id="InputSetRefresh" placeholder="5" value="'.$loginRefreshTime.'" required />
          </div>
          <div class="mb-3">
            <label class="form-label">'.Translation::of('assets_duration').'</label>
            <input name="duration" type="text" class="form-control" id="InputSetDuration" placeholder="30" value="'.$set['duration'].'" required />
          </div>
          <div class="mb-3">
            <label class="form-label">'.Translation::of('delay_of_weeks').'</label>
            <input name="end_date" type="text" class="form-control" id="InputSetEndDate" placeholder="1" value="'.$set['end_date'].'" required />
          </div>
          <div class="mb-3">
            <label class="form-label">'.Translation::of('design').'</label>
            <div class="row row-sm">
              <div class="col-auto">
                <label class="form-colorinput form-colorinput-light align-middle">
                  <input name="color" type="radio" name="design" value="0" class="form-colorinput-input" checked>
                  <span class="form-colorinput-color bg-white"></span>
                </label>
                '.Translation::of('light_mode').'
              </div>
              <div class="col-auto">
                <label class="form-colorinput align-middle ml-5">
                  <input name="color" type="radio" name="design" value="1" class="form-colorinput-input">
                  <span class="form-colorinput-color bg-dark"></span>
                </label>
                '.Translation::of('dark_mode').'
              </div>
            </div>
          </div>
        </div>
        </div>
        <div class="row align-items-center">
          <div class="col-4">
            <div class="progress">
              <div class="progress-bar" style="width: 50%" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                <span class="visually-hidden"></span>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="btn-list justify-content-end">
              <input name="firstStartSettings" type="hidden" value="1"/>
              <button type="submit" name="saveSettings" class="btn btn-primary">'.Translation::of('continue').'</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
    ';
  }
  else if($set['firstStart'] == 3){
    if(checkAddress($_SERVER['SERVER_ADDR'].'/api/v1.2/assets')){
      echo '
      <div class="hr-text hr-text-center hr-text-spaceless">'.Translation::of('player_settings').'</div>
        <form id="playerForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">'.Translation::of('enter_player_name').'</label>
              <input name="name" type="text" class="form-control" id="InputPlayerName" placeholder="'.Translation::of('player_name').'" autofocus required />
            </div>
            <div class="mb-3">
              <label class="form-label">'.Translation::of('enter_player_location').'</label>
              <input name="location" type="text" class="form-control" id="InputLocation" placeholder="'.Translation::of('player_location').'" required />
            </div>
          </div>
          </div>
          <div class="row align-items-center">
            <div class="col-4">
              <div class="progress">
                <div class="progress-bar" style="width: 75%" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                  <span class="visually-hidden"></span>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="btn-list justify-content-end">
                <input name="firstStartPlayer" type="hidden" value="1"/>
                <input name="address" type="hidden" id="InputAdress" value="'.$_SERVER['SERVER_ADDR'].'" />
                <a href="index.php?action=startup" class="btn btn-link link-secondary">
                    Set up later
                  </a>
                <button type="submit" name="saveIP" class="btn btn-primary">'.Translation::of('continue').'</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
      ';
    }
    else redirect('index.php?step=4');
  }
  else if($set['firstStart'] == 4){
    systemLog($_moduleName, 'Setup wizard complete', $loginUserID, 1);
    echo '
    <div class="hr-text hr-text-center hr-text-spaceless">'.Translation::of('finish').'</div>
      <form action="'.$_SERVER['PHP_SELF'].'" method="POST">
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col-12 text-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xl" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="green" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><path d="M9 12l2 2l4 -4"></path></svg>
              <br />
              <p>
              '.Translation::of('wizard.all_settings_made').'<br />
              '.Translation::of('wizard.you_can_start').'
              </p>
            </div>
          </div>
        </div>
        </div>
        <div class="row align-items-center">
          <div class="col-4">
            <div class="progress">
              <div class="progress-bar bg-success" style="width: 100%" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                <span class="visually-hidden"></span>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="btn-list justify-content-end">
              <a href="index.php?step=0" class="btn btn-primary">
                '.Translation::of('finish').'
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  ';
  }
}
