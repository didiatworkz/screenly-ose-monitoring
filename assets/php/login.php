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
           Login Module
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

$_moduleName = Translation::of('login');
$_moduleLink = '';

$nextPage = $_SERVER['PHP_SELF'];
$nextPage = str_replace('/', '', $nextPage);


echo'
<body class="antialiased border-top-wide border-primary d-flex flex-column">
  <div class="flex-fill d-flex flex-column justify-content-center">
    <div class="container-tight py-6">
      <form id="Login" action="'.$nextPage.'" class="card card-md" method="POST">
        <div class="card-body">
          <h2 class="mb-5 text-center">'._SYSTEM_NAME.'</h2>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="user" type="text" class="form-control" placeholder="'.Translation::of('username').'" autofocus autocomplete="section-login username">
          </div>
          <div class="mb-2">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" placeholder="'.Translation::of('password').'" autocomplete="section-login current-password">
          </div>
          <div class="form-footer">
            <input type="hidden"  name="Login" value="1" />
            <button type="submit"class="btn btn-primary btn-block" >'.Translation::of('login').'</button>
          </div>
        </div>
      </form>
      <div class="progress login-progress" style="display: none;">
        <div class="progress-bar progress-bar-indeterminate bg-blue"></div>
      </div>
    </div>
  </div>
';
