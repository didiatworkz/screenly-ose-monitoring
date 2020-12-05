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
        Multi Uploader Module
_______________________________________
*/

// Translation DONE

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

if(hasModuleRight($loginUserID, 'multi')){

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
      if(hasPlayerRight($loginUserID, $player["playerID"])){
      $playerList .= '
      <label class="form-selectgroup-item flex-fill">
        <input type="checkbox" name="id[]" data-ip="'.$player['address'].'" value="'.$player["playerID"].'" class="form-selectgroup-input">
        <div class="form-selectgroup-label d-flex align-items-center p-3">
          <div class="mr-3">
            <span class="form-selectgroup-check"></span>
          </div>
          <div class="form-selectgroup-label-content d-flex align-items-center">
            <div class="lh-sm">
              <div class="strong">'.$player['name'].'</div>
              <div class="text-muted">IP: '.$player['address'].'</div>
            </div>
          </div>
        </div>
      </label>
        ';
      }

    }
    echo '
    <div class="container">
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-auto">
            <h2 class="page-title">
              '.$_moduleName.'
            </h2>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <label class="form-label">'.Translation::of('upload_mode').'</label>
          <div class="form-selectgroup-boxes row mb-3">
            <div class="col-lg-6">
              <label class="form-selectgroup-item">
                <input type="radio" name="add_asset_mode" class="form-selectgroup-input" value="view_url" checked>
                <span class="form-selectgroup-label d-flex align-items-center p-3">
                  <span class="mr-3">
                    <span class="form-selectgroup-check"></span>
                  </span>
                  <span class="form-selectgroup-label-content">
                    <span class="form-selectgroup-title strong mb-1">'.Translation::of('url').'</span>
                  </span>
                </span>
              </label>
            </div>
            <div class="col-lg-6">
              <label class="form-selectgroup-item">
                <input type="radio" name="add_asset_mode" class="form-selectgroup-input" value="view_upload">
                <span class="form-selectgroup-label d-flex align-items-center p-3">
                  <span class="mr-3">
                    <span class="form-selectgroup-check"></span>
                  </span>
                  <span class="form-selectgroup-label-content">
                    <span class="form-selectgroup-title strong mb-1">'.Translation::of('upload').'</span>
                  </span>
                </span>
              </label>
            </div>
          </div>

          <div class="view_url tab">
            <form id="assetNewForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-multiloader="true">
              <div class="col-md-12">
                <div class="mb-3">
                  <label>'.Translation::of('asset_url').'</label>
                  <input name="url" type="text" pattern="^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&\'\(\)\*\+,;=.]+$" class="form-control" id="InputNewAssetUrl" placeholder="http://www.example.com" autofocus>
                </div>
              </div>
              <div class="col-md-12 mt">
                <div class="mb-3">
                  <label class="form-label">'.Translation::of('players').'</label>
                  <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                    '.$playerList.'
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <input name="mimetype" type="hidden" value="webpage" />
                <input name="newAsset" type="hidden" value="1" />
                <button type="submit" name="saveAsset" class="btn btn-success btn-block">'.Translation::of('upload').'</button>
              </div>
            </form>
          </div>


          <div class="view_upload tab" style="display: none;">
            <form id="dropzoneupload">
              <div class="col-md-12">
                <div class="form-group">
                  <div id="imageUpload" class="dropzoneMulti dropzone"></div>
                </div>
              </div>
              <div class="col-md-12 mt-3">
                <div class="mb-3">
                  <label class="form-label">'.Translation::of('players').'</label>
                  <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                    '.$playerList.'
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <input type="hidden" name="multidrop" id="multidrop" value="1" />
                <input type="hidden" name="test" id="test" value="1" />
                <a id="refresh" href="'.$_moduleLink.'&tab=drop" class="btn btn-info btn-block" style="display:none;">'.Translation::of('reload').'</a>
                <button type="button" id="uploadfiles" class="btn btn-success btn-block">'.Translation::of('upload').'</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
    ';
  }
}
else {
  sysinfo('danger', Translation::of('no_access'));
  redirect($backLink, 2);
}
