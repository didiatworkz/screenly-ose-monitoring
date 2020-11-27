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



$nav_active = ' active';


if(getPlayerCount() >= 2 && hasModuleRight($loginUserID, 'multi')){
  $multiMenu = '
  <li class="nav-item'.(isset($_GET['site']) && $_GET['site'] == 'multiuploader' ? $nav_active : '').'">
    <a class="nav-link" href="index.php?site=multiuploader" >
      <span class="nav-link-icon d-md-none d-lg-inline-block">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path><polyline points="7 9 12 4 17 9"></polyline><line x1="12" y1="4" x2="12" y2="16"></line></svg>
      </span>
      <span class="nav-link-title">
        '.Translation::of('multi_uploader').'
      </span>
    </a>
  </li>
  ';
} else $multiMenu = '';

echo'
<!-- Navbar -->
<header class="navbar navbar-expand-md navbar-light">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a href="." class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pr-0 pr-md-3">
      '._SYSTEM_NAME.'
    </a>
    <div class="navbar-nav flex-row order-md-last">';

    if(hasPlayerAddRight($loginUserID)) echo'
      <div class="nav-item dropdown d-none d-md-flex mr-3">
        <a href="#" data-toggle="modal" data-target="#newPlayer" class="nav-link px-0" tabindex="-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
        </a>
      </div>';

      echo '
      <div class="nav-item dropdown d-none d-md-flex mr-3">
        <a href="'.$_SERVER['REQUEST_URI'].'" class="nav-link px-0" tabindex="-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M4.05 11a8 8 0 1 1 .5 4m-.5 5v-5h5"></path></svg>
        </a>
      </div>
      <div class="nav-item'.(isset($_GET['site']) && $_GET['site'] == 'settings' ? $nav_active : '').' dropdown d-none d-md-flex mr-3">
        <a href="index.php?site=settings" class="nav-link px-0" tabindex="-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><circle cx="12" cy="12" r="3"></circle></svg>
          '.$update_badge.'
        </a>
      </div>';

      if($set['debug'] == 1) echo '
      <div class="nav-item d-none d-md-flex mr-3">
        <a href="#" class="nav-link px-0" tabindex="-1" title="Debug Mode is activated">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 9v-1a3 3 0 0 1 6 0v1" /><path d="M8 9h8a6 6 0 0 1 1 3v3a5 5 0 0 1 -10 0v-3a6 6 0 0 1 1 -3" /><line x1="3" y1="13" x2="7" y2="13" /><line x1="17" y1="13" x2="21" y2="13" /><line x1="12" y1="20" x2="12" y2="14" /><line x1="4" y1="19" x2="7.35" y2="17" /><line x1="20" y1="19" x2="16.65" y2="17" /><line x1="4" y1="7" x2="7.75" y2="9.4" /><line x1="20" y1="7" x2="16.25" y2="9.4" /></svg>
        </a>
      </div>';

      echo '
      <div class="nav-item dropdown">
        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-toggle="dropdown">
          '.getUserAvatar($loginUserID).'
          <div class="d-none d-xl-block pl-2">
            <div>'.$loginUsername.'</div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="index.php?action=logout">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path><path d="M7 12h14l-3 -3m0 6l3 -3"></path></svg>
            Logout
          </a>
        </div>
      </div>
    </div>
    <div class="collapse navbar-collapse" id="navbar-menu">
      <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
        <ul class="navbar-nav">
          <li class="nav-item'.(isset($_GET['site']) && $_GET['site'] == 'dashboard' ? $nav_active : '').'">
            <a class="nav-link" href="index.php?site=dashboard" >
              <span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><line x1="4" y1="9" x2="20" y2="9" /></svg>
              </span>
              <span class="nav-link-title">
                '.Translation::of('dashboard').'
              </span>
            </a>
          </li>
          <li class="nav-item'.(isset($_GET['site']) && $_GET['site'] == 'players' ? $nav_active : '').'">
            <a class="nav-link" href="index.php?site=players">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="4" y="4" width="6" height="6" rx="1"></rect><rect x="4" y="14" width="6" height="6" rx="1"></rect><rect x="14" y="14" width="6" height="6" rx="1"></rect><line x1="14" y1="7" x2="20" y2="7"></line><line x1="17" y1="4" x2="17" y2="10"></line></svg>
              </span>
              <span class="nav-link-title">
                '.Translation::of('player').'
              </span>
            </a>
          </li>
          '.$multiMenu.'
          ';
          if(hasModuleRight($loginUserID, 'addon')) echo'
          <li class="nav-item">
            <a class="nav-link" href="index.php?site=addon">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="4" y="4" width="16" height="4" rx="1"></rect><rect x="4" y="12" width="6" height="8" rx="1"></rect><line x1="14" y1="12" x2="20" y2="12"></line><line x1="14" y1="16" x2="20" y2="16"></line><line x1="14" y1="20" x2="20" y2="20"></line></svg>
              </span>
              <span class="nav-link-title">
                '.Translation::of('addon').'
              </span>
            </a>
          </li>';

          echo '
          <li class="nav-item d-block d-sm-none">
            <a class="nav-link" href="index.php?site=settings">
              <span class="nav-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </span>
              <span class="nav-link-title">
                '.Translation::of('settings').'
              </span>
            </a>
          </li>

          <li class="nav-item d-block d-sm-none">
            <a class="nav-link" href="index.php?action=logout">
              <span class="nav-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path><path d="M7 12h14l-3 -3m0 6l3 -3"></path></svg>
              </span>
              <span class="nav-link-title">
                '.Translation::of('logout').'
              </span>
            </a>
          </li>
        </ul>


        <div class="ml-md-auto pl-md-4 py-2 py-md-0 mr-md-4 order-first order-md-last flex-grow-1 flex-md-grow-0" id="somo_search">
            <div class="input-icon">
              <span class="input-icon-addon ">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z"/>
                  <circle cx="10" cy="10" r="7" />
                  <line x1="21" y1="21" x2="15" y2="15" />
                </svg>
              </span>
              <input type="text" id="inlineFormInputGroup" class="form-control" placeholder="'.Translation::of('search').'">
            </div>
        </div>
      </div>
    </div>
  </div>
</header>
<!-- End Navbar -->




';
