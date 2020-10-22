<?php

  $adminUserManagement  = '';
  $adminSettings        = '';

  if(getGroupID($loginUserID) == 1){
    $adminUserManagement = '
      <li class="nav-link">
        <a href="index.php?site=usermanagement" class="nav-item dropdown-item">User Management</a>
      </li>
    ';

    $adminSettings = '
      <li class="nav-link">
       <a href="javascript:void(0)" data-toggle="modal" data-target="#settings" class="nav-item dropdown-item">Settings</a>
     </li>
    ';
  }

  if($playerCount >= 2){
    $multiMenu = '
    <li class="nav-item">
      <a href="index.php?site=multiuploader" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Multi Uploader">
        <i class="tim-icons icon-upload"></i>
        <p class="d-lg-none">Multi Uploader</p>
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
            <a href="javascript:void(0)" data-toggle="modal" data-target="#newPlayer" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Add player">
              <i class="tim-icons icon-simple-add"></i>
              <p class="d-lg-none">Add player</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link" id="search-button" data-toggle="modal" data-target="#searchModal" data-tooltip="tooltip" data-placement="bottom" title="Search Player">
              <i class="tim-icons icon-zoom-split"></i>
              <p class="d-lg-none">Search Player</p>
            </a>
          </li>
            '.$update.'
            '.$multiMenu.'
          <li class="nav-item">
            <a href="'.$_SERVER['REQUEST_URI'].'" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Refresh">
              <i class="tim-icons icon-refresh-02"></i>
              <p class="d-lg-none">Refresh</p>
            </a>
          </li>
          <li class="nav-item">
						<a href="javascript:void(0)" data-toggle="modal" data-target="#addon" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Addon">
							<i class="tim-icons icon-puzzle-10"></i>
							<p class="d-lg-none">Addon</p>
						</a>
					</li>
          <!--<li class="nav-item">
            <a href="index.php?site=extensions" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Extensions">
              <i class="tim-icons icon-puzzle-10"></i>
              <p class="d-lg-none">Extensions</p>
            </a>
          </li>-->
          <li class="dropdown nav-item">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
              <i class="tim-icons icon-single-02"></i>
              <b class="caret d-none d-lg-block d-xl-block"></b>
              <p class="d-lg-none">User</p>
            </a>
            <ul class="dropdown-menu dropdown-navbar">
              <li class="nav-link">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#account" class="nav-item dropdown-item">Account</a>
              </li>
              '.$adminUserManagement.'
              '.$adminSettings.'
              <li class="nav-link">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#publicLink" class="nav-item dropdown-item">Public Link</a>
              </li>
              <li class="dropdown-divider"></li>
              <li class="nav-link">
                <a href="index.php?action=logout" class="nav-item dropdown-item">Logout</a>
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
