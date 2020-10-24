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
             Menu Module
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');


  $adminUserManagement  = '';
  $adminSettings        = '';

  if(getGroupID($loginUserID) == 1){
    $adminUserManagement = '
      <li class="nav-link">
        <a href="index.php?site=usermanagement" class="nav-item dropdown-item">'.Translation::of('user_management').'</a>
      </li>
    ';

    $adminSettings = '
      <li class="nav-link">
       <a href="javascript:void(0)" data-toggle="modal" data-target="#settings" class="nav-item dropdown-item">'.Translation::of('settings').'</a>
     </li>
    ';
  }

  if($playerCount >= 2){
    $multiMenu = '
    <li class="nav-item">
      <a href="index.php?site=multiuploader" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="'.Translation::of('multi_uploader').'">
        <i class="tim-icons icon-upload"></i>
        <p class="d-lg-none">'.Translation::of('multi_uploader').'</p>
      </a>
    </li>
    ';
  } else $multiMenu = '';

  echo'
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
    <div class="container-fluid">
      <div class="navbar-wrapper">
        <a class="navbar-brand" href="./index.php">'._SYSTEM_NAME.'</a>
      </div>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-bar bar1"></span>
        <span class="navbar-toggler-bar bar2"></span>
        <span class="navbar-toggler-bar bar3"></span>
      </button>
      <div class="collapse navbar-collapse" id="navigation">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a href="javascript:void(0)" data-toggle="modal" data-target="#newPlayer" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="'.Translation::of('add_player').'">
              <i class="tim-icons icon-simple-add"></i>
              <p class="d-lg-none">'.Translation::of('add_player').'</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link" id="search-button" data-toggle="modal" data-target="#searchModal" data-tooltip="tooltip" data-placement="bottom" title="'.Translation::of('search_player').'">
              <i class="tim-icons icon-zoom-split"></i>
              <p class="d-lg-none">'.Translation::of('search_player').'</p>
            </a>
          </li>
            '.$update.'
            '.$multiMenu.'
          <li class="nav-item">
            <a href="'.$_SERVER['REQUEST_URI'].'" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="'.Translation::of('refresh').'">
              <i class="tim-icons icon-refresh-02"></i>
              <p class="d-lg-none">'.Translation::of('refresh').'</p>
            </a>
          </li>
          <li class="nav-item">
						<a href="javascript:void(0)" data-toggle="modal" data-target="#addon" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="'.Translation::of('addon').'">
							<i class="tim-icons icon-puzzle-10"></i>
							<p class="d-lg-none">'.Translation::of('addon').'</p>
						</a>
					</li>
          <!--<li class="nav-item">
            <a href="index.php?site=extensions" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="'.Translation::of('extensions').'">
              <i class="tim-icons icon-puzzle-10"></i>
              <p class="d-lg-none">'.Translation::of('extensions').'</p>
            </a>
          </li>-->
          <li class="dropdown nav-item">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
              <i class="tim-icons icon-single-02"></i>
              <b class="caret d-none d-lg-block d-xl-block"></b>
              <p class="d-lg-none">'.Translation::of('user').'</p>
            </a>
            <ul class="dropdown-menu dropdown-navbar">
              <li class="nav-link">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#account" class="nav-item dropdown-item">'.Translation::of('account').'</a>
              </li>
              '.$adminUserManagement.'
              '.$adminSettings.'
              <li class="nav-link">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#publicLink" class="nav-item dropdown-item">'.Translation::of('public_access_link').'</a>
              </li>
              <li class="dropdown-divider"></li>
              <li class="nav-link">
                <a href="index.php?action=logout" class="nav-item dropdown-item">'.Translation::of('logout').'</a>
              </li>
            </ul>
          </li>
          <li class="separator d-lg-none"></li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->

  ';
