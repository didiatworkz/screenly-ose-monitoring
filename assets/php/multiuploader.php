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

  if(isset($_GET['tab']) AND $_GET['tab'] == 'drop' ) {
    $active_drop = 'active';
    $active_url = '';
  }
  else {
    $active_drop = '';
    $active_url = 'active';
  }


  if(isset($_POST['send'])){
      redirect($_moduleLink, 0);
  }
  else {
    $playerList = '';
    $playerSQL = $db->query("SELECT * FROM `player` ORDER BY name");
    while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
      $playerList .= '
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" name="id[]" type="checkbox" data-ip="'.$player['address'].'" value="'.$player["playerID"].'">
          <span class="form-check-sign">
            <span class="check">'.$player['name'].' (IP: '.$player['address'].')</span>
          </span>
        </label>
      </div>
        ';

    }
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
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link '.$active_url.'" href="#url" role="tab" data-toggle="tab">URL</a>
            </li>
            <li class="nav-item">
              <a class="nav-link '.$active_drop.'" href="#upload" role="tab" data-toggle="tab">Upload</a>
            </li>
          </ul>

          <div class="tab-content">
            <div role="tabpanel" class="tab-pane '.$active_url.'" id="url">
              <form id="assetNewForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-multiloader="true">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group" id="playerList">
                      <label>Choose player</label>
                      '.$playerList.'
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="InputNewAssetUrl">Asset URL</label>
                      <input name="url" type="text" pattern="^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&\'\(\)\*\+,;=.]+$" class="form-control" id="InputNewAssetUrl" placeholder="http://www.example.com" autofocus>
                    </div>
                    <div class="form-group text-right">
                      <input name="mimetype" type="hidden" value="webpage" />
                      <input name="newAsset" type="hidden" value="1" />
                      <button type="submit" name="saveAsset" class="btn btn-success btn-sm">Upload</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div role="tabpanel" class="tab-pane '.$active_drop.'" id="upload">
            <form id="dropzoneupload">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Choose player</label>
                    '.$playerList.'
                  </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                      <div id="imageUpload" class="dropzoneMulti dropzone"></div>
                    </div>
                  <div class="form-group text-right">
                    <br />
                    <input type="hidden" name="multidrop" id="multidrop" value="1" />
                    <input type="hidden" name="test" id="test" value="1" />
                    <a id="refresh" href="'.$_moduleLink.'&tab=drop" class="btn btn-info btn-sm" style="display:none;">Reload</a>
                    <button type="button" id="uploadfiles" class="btn btn-success btn-sm">Upload</button>
                  </div>
                </div>
              </div>
            </form>
            </div>
          </div>
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
