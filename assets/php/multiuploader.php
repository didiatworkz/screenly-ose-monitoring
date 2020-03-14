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
________________________________________
      Screenly OSE Monitor
      Multi Uploader Module
________________________________________
*/

if(getGroupID($loginUserID)){

  $_moduleName = 'Multi Uploader';
  $_moduleLink = 'index.php?site=multiuploader';

  if(isset($_POST['send'])){
      redirect($_moduleLink, 0);
  }
  else {
    $userSQL = $db->query("SELECT * FROM `users` ORDER BY userID");
    echo '
    <div class="row justify-content-md-center">
      <div class="col-md-10">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-md-10">
                <h5 class="title">'.$_moduleName.'</h5>
              </div>
            </div>
          </div>
          <div class="card-body">
            Hello
          </div>
        </div>
      </div>
    </div>
    ';
  }
}
else {
  sysinfo('danger', 'No Access to this module!');
  redirect($backLink, 2);
}
