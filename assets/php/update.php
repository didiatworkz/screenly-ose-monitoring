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

// Translation DONE

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

$_moduleName = Translation::of('update');
$_moduleLink = '';

if($updatecheck < time() && (date("d", $updatecheck) != date("d"))){
  shell_exec('ose-monitoring --scriptupdate');
  $db->exec("UPDATE `settings` SET updatecheck='".time()."' WHERE settingsID=1");
  systemLog($_moduleName, 'Update available!', '', 1);
}

if(@file_exists('update.txt') && isAdmin($loginUserID)) {
  $update_badge = '<span class="badge bg-red blink"></span>';
  $update_info = '
  <div class="alert alert-info text-center" role="alert">
  <svg xmlns="http://www.w3.org/2000/svg" class="icon mr-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12.01" y2="8" /><polyline points="11 12 12 12 12 16 13 16" /></svg>
  SOMO information — <a href="https://github.com/didiatworkz/screenly-ose-monitor/releases" target="_blank" class="blink">'.Translation::of('update_available').'</a>
</div>

  ';

}
else {
  $update_badge = '';
  $update_info = '';
}
