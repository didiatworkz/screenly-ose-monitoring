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
            Update Module
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

if($updatecheck < time() && (date("d", $updatecheck) != date("d"))){
  shell_exec('ose-monitoring --scriptupdate');
  $db->exec("UPDATE `settings` SET updatecheck='".time()."' WHERE settingsID=1");
}

if(@file_exists('update.txt')) {
  $update_badge = '<span class="badge bg-red blink"></span>';
  $update_card = '
    <div class="col-md-12">
      <div class="card">
      <div class="card-status-top bg-danger"></div>
        <div class="card-body text-center">
          <div class="mb-3">
            <span class="avatar avatar-xl">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M4 7h3a1 1 0 0 0 1 -1v-1a2 2 0 0 1 4 0v1a1 1 0 0 0 1 1h3a1 1 0 0 1 1 1v3a1 1 0 0 0 1 1h1a2 2 0 0 1 0 4h-1a1 1 0 0 0 -1 1v3a1 1 0 0 1 -1 1h-3a1 1 0 0 1 -1 -1v-1a2 2 0 0 0 -4 0v1a1 1 0 0 1 -1 1h-3a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1h1a2 2 0 0 0 0 -4h-1a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1"></path></svg>
            </span>
          </div>
          <div class="card-title mb-1">'.Translation::of('update').'</div>
          <div class="text-muted blink">'.Translation::of('update_available').'</div>
        </div>
        <a href="https://github.com/didiatworkz/screenly-ose-monitor/releases" target="_blank" class="card-btn">Show</a>
      </div>
    </div>
  ';

}
else {
  $update_badge = '';
  $update_card = '';
}
