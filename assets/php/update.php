<?php
if($updatecheck < time() && (date("d", $updatecheck) != date("d"))){
  shell_exec('ose-monitoring --scriptupdate');
  $db->exec("UPDATE `settings` SET updatecheck='".time()."' WHERE userID=1");
}

if(@file_exists('update.txt')) {
  $update = '
        <li class="nav-item">
          <a href="https://github.com/didiatworkz/screenly-ose-monitor" target="_blank" class="nav-link" data-tooltip="tooltip" data-placement="bottom" title="Update available!">
            <i class="tim-icons icon-cloud-download-93 blink"></i>
            <p class="d-lg-none">
              Update available!
            </p>
          </a>
        </li>
  ';

}
else $update = '';
