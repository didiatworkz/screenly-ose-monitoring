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

  $_moduleName = Translation::of('multi_uploader');
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
        <input type="checkbox" name="id[]" data-id="'.$player['playerID'].'" data-endpoint="'.checkHTTP($player['address']).$player['address'].'/api/v1/file_asset" value="'.$player["playerID"].'" class="form-selectgroup-input">
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
                <div class="mb-3">
                  <div class="form-label">'.Translation::of('asset_settings').'</div>
                  <label class="form-check form-switch">
                    <input class="form-check-input toggle_div" data-src=".defaults_url" type="checkbox">
                    <span class="form-check-label">'.Translation::of('change_defaults').'</span>
                  </label>
                </div>
              </div>
              <div class="modal-body defaults_url" style="display: none">
                <div class="row">
                  <div class="col-lg-8">
                    <div class="mb-3">
                      <label class="form-label">'.Translation::of('start').'</label>
                      <div class="input-icon caltime-padding">
                        <input name="start_date" type="text" value="'.date('Y-m-d', strtotime('now')).'" class="form-control asset_start" placeholder="'.Translation::of('start_date').'" />
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="mb-3">
                      <label class="form-label">&nbsp;</label>
                      <div class="input-icon caltime-padding">
                        <input name="start_time" type="text" class="form-control asset_start_time" placeholder="'.Translation::of('start_time').'" value="00:00" />
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 9 15"></polyline></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-8">
                    <div class="mb-3">
                      <label class="form-label">'.Translation::of('end').'</label>
                      <div class="input-icon caltime-padding">
                        <input name="end_date" type="date" class="form-control asset_end" placeholder="'.Translation::of('end_date').'" value="'.date('Y-m-d', strtotime('+'.$set['end_date'].' week')).'" />
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="mb-3">
                      <label class="form-label">&nbsp;</label>
                      <div class="input-icon caltime-padding">
                        <input name="end_time" type="time" class="form-control asset_end_time" placeholder="'.Translation::of('end_time').'" value="00:00" />
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 9 15"></polyline></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">'.Translation::of('duration_in_sec').'</label>
                  <input name="duration" type="number" class="form-control" value="'.$set['duration'].'" />
                </div>
                <div class="mb-3">
                  <label class="row">
                    <span class="col">'.Translation::of('active').'</span>
                    <span class="col-auto">
                      <label class="form-check form-check-single form-switch">
                        <input class="form-check-input" name="active" type="checkbox" checked>
                      </label>
                    </span>
                  </label>
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
                <input name="multidropurl" type="hidden" value="1" />
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
              <div class="mb-3">
                <div class="form-label">'.Translation::of('asset_settings').'</div>
                <label class="form-check form-switch">
                  <input class="form-check-input toggle_div" data-src=".defaults_upload" type="checkbox">
                  <span class="form-check-label">'.Translation::of('change_defaults').'</span>
                </label>
              </div>
              <div class="defaults_upload" style="display: none">
                <div class="row">
                  <div class="col-lg-8">
                    <div class="mb-3">
                      <label class="form-label">'.Translation::of('start').'</label>
                      <div class="input-icon caltime-padding">
                        <input name="start_date" type="text" value="'.date('Y-m-d', strtotime('now')).'" class="form-control asset_start" placeholder="'.Translation::of('start_date').'" />
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="mb-3">
                      <label class="form-label">&nbsp;</label>
                      <div class="input-icon caltime-padding">
                        <input name="start_time" type="text" class="form-control asset_start_time" placeholder="'.Translation::of('start_time').'" value="00:00" />
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 9 15"></polyline></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-8">
                    <div class="mb-3">
                      <label class="form-label">'.Translation::of('end').'</label>
                      <div class="input-icon caltime-padding">
                        <input name="end_date" type="date" class="form-control asset_end" placeholder="'.Translation::of('end_date').'" value="'.date('Y-m-d', strtotime('+'.$set['end_date'].' week')).'" />
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="mb-3">
                      <label class="form-label">&nbsp;</label>
                      <div class="input-icon caltime-padding">
                        <input name="end_time" type="time" class="form-control asset_end_time" placeholder="'.Translation::of('end_time').'" value="00:00" />
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 9 15"></polyline></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">'.Translation::of('duration_in_sec').'</label>
                  <input name="duration" type="number" class="form-control" value="'.$set['duration'].'" />
                </div>
                <div class="mb-3">
                  <label class="row">
                    <span class="col">'.Translation::of('active').'</span>
                    <span class="col-auto">
                      <label class="form-check form-check-single form-switch">
                        <input class="form-check-input" name="active" type="checkbox" checked>
                      </label>
                    </span>
                  </label>
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
