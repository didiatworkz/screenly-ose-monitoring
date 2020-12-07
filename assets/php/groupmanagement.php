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
       Groupmanagement Module
_______________________________________
*/

// Translation DONE

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');


if(getGroupID($loginUserID) == 1){

  $_moduleName = Translation::of('group_management');
  $_moduleLink = 'index.php?site=groupmanagement';

  if(isset($_POST['saveGroup'])){
      $name       = $_POST['name'];
      if($name){
        $db->exec("INSERT INTO `userGroups` (name) values('".$name."')");
        sysinfo('success', Translation::of('msg.group_created_successfully'));
        systemLog($_moduleName, Translation::of('create_group', ['groupname' => $name]), $loginUserID, 1);
      }
      redirect($_moduleLink, 0);
  }

  if(isset($_POST['editGroup'])){

    $groupID          = $_POST['groupID'];
    $name             = $_POST['name'];
    $ass_add          = isset($_POST['ass_add']) ? 1 : 0;
    $ass_edit         = isset($_POST['ass_edit']) ? 1 : 0;
    $ass_delete       = isset($_POST['ass_delete']) ? 1 : 0;
    $ass_clean        = isset($_POST['ass_clean']) ? 1 : 0;
    $ass_state        = isset($_POST['ass_state']) ? 1 : 0;

    $pla_add          = isset($_POST['pla_add']) ? 1 : 0;
    $pla_edit         = isset($_POST['pla_edit']) ? 1 : 0;
    $pla_delete       = isset($_POST['pla_delete']) ? 1 : 0;
    $pla_reboot       = isset($_POST['pla_reboot']) ? 1 : 0;

    $set_system       = isset($_POST['set_system']) ? 1 : 0;
    $set_public       = isset($_POST['set_public']) ? 1 : 0;
    $set_user         = isset($_POST['set_user']) ? 1 : 0;
    $set_user_add     = isset($_POST['set_user_add']) ? 1 : 0;
    $set_user_edit    = isset($_POST['set_user_edit']) ? 1 : 0;
    $set_user_delete  = isset($_POST['set_user_delete']) ? 1 : 0;

    $players          = isset($_POST['player_restriction']) ? serialize($_POST['player_restriction']) : '';
    $players_enable   = isset($_POST['players_enable']) ? 1 : 0;

    $modules          = isset($_POST['module_restriction']) ? serialize($_POST['module_restriction']) : '';
    $modules_enable   = isset($_POST['modules_enable']) ? 1 : 0;

    if($players_enable == 0) $players = serialize('');
    if($modules_enable == 0) $modules = serialize('');
    if($set_user == 0) {
      $set_user_add = 0;
      $set_user_edit = 0;
      $set_user_delete = 0;
    }


    if($groupID && $name){
      $db->exec("UPDATE `userGroups` SET
        name='".$name."',
        ass_add='".$ass_add."',
        ass_edit='".$ass_edit."',
        ass_delete='".$ass_delete."',
        ass_clean='".$ass_clean."',
        ass_state='".$ass_state."',
        pla_add='".$pla_add."',
        pla_edit='".$pla_edit."',
        pla_delete='".$pla_delete."',
        pla_reboot='".$pla_reboot."',
        set_user='".$set_user."',
        set_system='".$set_system."',
        set_public='".$set_public."',
        set_user_add='".$set_user_add."',
        set_user_edit='".$set_user_edit."',
        set_user_delete='".$set_user_delete."',
        players='".$players."',
        players_enable='".$players_enable."',
        modules='".$modules."',
        modules_enable='".$modules_enable."' WHERE groupID='".$groupID."'");
      sysinfo('success', Translation::of('msg.group_updated_successfully'));
      systemLog($_moduleName, Translation::of('change_rights_of_group', ['groupname' => $name]), $loginUserID, 1);
    }
    redirect($backLink, 0);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'deleteGroup'){
    $groupID = $_GET['groupID'];
    if(isset($groupID) AND isAdmin($loginUserID)){
      systemLog($_moduleName, Translation::of('delete_group', ['groupname' => getGroupName($groupID)]), $loginUserID, 1);
      $db->exec("UPDATE `userGroupMapping` SET groupID='0' WHERE groupID='".$groupID."'");
      $db->exec("DELETE FROM `userGroups` WHERE groupID='".$groupID."'");
      sysinfo('success', Translation::of('msg.group_deleted_successfully'));
    }
    redirect($_moduleLink, 0);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'newGroup'){
    echo '
    <div class="container-xl">
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-auto">
            <h2 class="page-title">
              '.Translation::of('new_group').'
            </h2>
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
              <li class="breadcrumb-item"><a href="index.php?site=settings">'.Translation::of('settings').'</a></li>
              <li class="breadcrumb-item"><a href="'.$_moduleLink.'">'.Translation::of('group_settings').'</a></li>
              <li class="breadcrumb-item active" aria-current="page"><a href="'.$_moduleLink.'&action=newGroup">'.Translation::of('new_group').'</a></li>
            </ol>
          </div>
          <div class="col-auto ml-auto d-print-none">
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-12">
          <div class="card card-lg">
            <form id="groupForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
              <div class="card-body">
              <h2 id="personal">'.Translation::of('group').'</h2>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('name').'</label>
                  <div class="col">
                    <input name="name" type="text" class="form-control" id="InputName" autofocus required />
                  </div>
                </div>
              </div>
              <div class="card-footer d-flex align-items-center">
                <a href="'.$_moduleLink.'" class="btn btn-link mr-auto">'.Translation::of('cancel').'</a>
                <button type="submit" name="saveGroup" value="1" class="btn btn-primary">'.Translation::of('save').'</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>';
  }

  elseif(isset($_GET['action']) && $_GET['action'] == 'editGroup'){

    $groupID   = $_GET['groupID'];

    if($groupID != 1) {
      $groupSQL  = $db->query("SELECT * FROM `userGroups` WHERE groupID='".$groupID."'");
      $group     = $groupSQL->fetchArray(SQLITE3_ASSOC);

      $mappingLists = NULL;
      $mapSQL = $db->query("SELECT * FROM `userGroupMapping` WHERE groupID='".$groupID."'");
      while($map = $mapSQL->fetchArray(SQLITE3_ASSOC)){
        $userName = getUsername($map['userID']);
        $mappingLists .= '
        <div class="col-6 row g-2 mb-3 align-items-center">
          <a href="index.php?site=usermanagement&action=editUser&userID='.$map['userID'].'" target="_blank" class="col-auto" title="'.Translation::of('edit_user', ['username' => $userName]).'">
            '.getUserAvatar($map['userID']).'
          </a>
          <div class="col text-truncate">
            <a href="index.php?site=usermanagement&action=editUser&userID='.$map['userID'].'" target="_blank" class="text-body d-block text-truncate" title="'.Translation::of('edit_user', ['username' => $userName]).'">'.getFullname($map['userID']).'</a>
            <small class="d-block text-muted text-truncate mt-n1">'.$userName.'</small>
          </div>
        </div>
        ';
      }

      if($mappingLists == '') $mappingLists = '
      <div class="col-6 row g-2 mb-3 align-items-center">
        <a href="#" target="_blank" class="col-auto">
          <span class="avatar">XX</span>
        </a>
        <div class="col text-truncate">
          <a href="#" target="_blank" class="text-body d-block text-truncate">'.Translation::of('no_user').' </a>
          <small class="d-block text-muted text-truncate mt-n1">'.Translation::of('in_this_group').'</small>
        </div>
      </div>
      ';

      $playerRestrictionList = NULL;
      $playerRestriction = unserialize($group['players']);
      if(empty($playerRestriction)) $playerRestriction = array('0');

      $playerSQL  = $db->query("SELECT playerID, name, address FROM `player`");

      while($player = $playerSQL->fetchArray(SQLITE3_ASSOC)){
        if(in_array($player['playerID'], $playerRestriction)) $restrictionExists=1;
        else $restrictionExists = 0;

        $playerRestrictionList .= '
            <label class="form-selectgroup-item flex-fill">
              <input type="checkbox" name="player_restriction[]" value="'.$player['playerID'].'" class="form-selectgroup-input"'.checkboxState($restrictionExists).'>
              <div class="form-selectgroup-label d-flex align-items-center p-3">
                <div class="mr-3">
                  <span class="form-selectgroup-check"></span>
                </div>
                <div class="form-selectgroup-label-content d-flex align-items-center">
                  <div>
                    <div class="font-weight-medium">'.$player['name'].'</div>
                    <div class="text-muted">'.$player['address'].'</div>
                  </div>
                </div>
              </div>
            </label>
        ';
      }

      $moduleRestrictionList = NULL;
      $moduleRestriction = unserialize($group['modules']);
      if(empty($moduleRestriction)) $moduleRestriction = array ('0');

      $moduleArray = array(
        array('addon', Translation::of('addon')),
        array('multi', Translation::of('multi_uploader')),
      );

      for ($i=0; $i < count($moduleArray); $i++) {

        if(in_array($moduleArray[$i]['0'], $moduleRestriction)) $restrictionExists=1;
        else $restrictionExists = 0;

        $moduleRestrictionList .= '
        <label class="form-selectgroup-item flex-fill">
          <input type="checkbox" name="module_restriction[]" value="'.$moduleArray[$i]['0'].'" class="form-selectgroup-input"'.checkboxState($restrictionExists).'>
          <div class="form-selectgroup-label d-flex align-items-center p-3">
            <div class="mr-3">
              <span class="form-selectgroup-check"></span>
            </div>
            <div class="form-selectgroup-label-content d-flex align-items-center">
              <div>
                <div class="font-weight-medium">'.$moduleArray[$i]['1'].'</div>
              </div>
            </div>
          </div>
        </label>
        ';

      }

      $groupName = $group['name'];

      echo '
      <div class="container-xl">
        <div class="page-header">
          <div class="row align-items-center">
            <div class="col-auto">
              <h2 class="page-title">
                '.Translation::of('edit_group', ['groupname' => $groupName]).'
              </h2>
              <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="index.php?site=settings">'.Translation::of('settings').'</a></li>
                <li class="breadcrumb-item"><a href="'.$_moduleLink.'">'.Translation::of('group_settings').'</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="'.$_moduleLink.'&action=editGroup&groupID='.$groupID.'">'.Translation::of('edit_group', ['groupname' => $groupName]).'</a></li>
              </ol>
            </div>
            <div class="col-auto ml-auto d-print-none">
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-lg-4 order-lg-1 mb-4">
            <div class="sticky-top">
              <div class="card">
                <div class="card-body">
                  <label class="form-label">'.Translation::of('user_list').'</label>
                  <div class="row mb-n3">
                    '.$mappingLists.'
                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-body">
                  <label class="form-label">'.Translation::of('quick_rights').'</label>
                  <div class="row mb-3">
                    <button class="btn btn-outline-success quick_rights mb-2" data-src="add">'.Translation::of('select_all_rights_add').'</button>
                    <button class="btn btn-outline-warning quick_rights mb-2" data-src="edit">'.Translation::of('select_all_rights_edit').'</button>
                    <button class="btn btn-outline-danger quick_rights mb-2" data-src="delete">'.Translation::of('select_all_rights_delete').'</button>
                    <button class="btn btn-outline-primary quick_rights mb-2" data-src="special">'.Translation::of('select_all_rights_special').'</button>
                    <button class="btn btn-outline-secondary quick_rights mb-2" data-src="reset">'.Translation::of('reset_all_rights').'</button>
                  </div>
                </div>
              </div>
              <h5 class="subheader">'.Translation::of('on_this_page').'</h5>
              <ul class="list-unstyled">
                <li class="toc-entry toc-h2"><a href="#gSettings">'.Translation::of('group_settings').'</a></li>
                <li class="toc-entry toc-h2"><a href="#asset">'.Translation::of('asset_restrictions').'</a></li>
                <li class="toc-entry toc-h2"><a href="#player">'.Translation::of('player_restriction').'</a></li>
                <li class="toc-entry toc-h2"><a href="#setting">'.Translation::of('setting_restrictions').'</a></li>
                <li class="toc-entry toc-h2"><a href="#moduleR">'.Translation::of('module_restrictions').'</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="card card-lg">
              <form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
                <div class="card-body">
                <h2 id="gSettings">'.Translation::of('group_settings').'</h2>
                  <div class="form-group mb-3 row">
                    <label class="form-label col-3 col-form-label">'.Translation::of('group_name').'</label>
                    <div class="col">
                      <input name="name" type="text" class="form-control" id="InputName" placeholder="Doe"  value="'.$group['name'].'" required />
                    </div>
                  </div>
                  <hr />
                  <h2 id="rights">'.Translation::of('group_properties').'</h2>
                  <div class="mb-3">
                    <label id="asset" class="form-label">'.Translation::of('asset_restrictions').'</label>
                    <div class="divide-y">
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('add_asset').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_add" type="checkbox"'.checkboxState($group['ass_add']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('edit_asset').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_edit" type="checkbox"'.checkboxState($group['ass_edit']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('delete_asset').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_delete" type="checkbox"'.checkboxState($group['ass_delete']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('change_asset_state').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_state" type="checkbox"'.checkboxState($group['ass_state']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('clean_assets').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_clean" type="checkbox"'.checkboxState($group['ass_clean']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <hr />
                  <div class="mb-3">
                    <label id="player" class="form-label">'.Translation::of('player_restriction').'</label>
                    <div class="divide-y">
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('add_player').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="pla_add" type="checkbox"'.checkboxState($group['pla_add']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('edit_player').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="pla_edit" type="checkbox"'.checkboxState($group['pla_edit']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('delete_player').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="pla_delete" type="checkbox"'.checkboxState($group['pla_delete']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('reboot_player').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="pla_reboot" type="checkbox"'.checkboxState($group['pla_reboot']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('restrict_individual_player').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input toggle_div" data-src=".player_restriction" name="players_enable" type="checkbox"'.checkboxState($group['players_enable']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div class="player_restriction" style="display: none;">
                        <label class="form-label">'.Translation::of('choose_player_are_allowed').'</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                          '.$playerRestrictionList.'
                        </div>
                      </div>

                    </div>
                  </div>
                  <hr />
                  <div class="mb-3">
                    <label id="setting" class="form-label">'.Translation::of('setting_restrictions').'</label>
                    <div class="divide-y">
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('allow_user').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input toggle_div" data-src=".setting_options" name="set_user" type="checkbox"'.checkboxState($group['set_user']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div class="setting_options" style="display: none;">
                        <div>
                          <label class="row">
                            <span class="col ml-3">'.Translation::of('add_user').'</span>
                            <span class="col-auto">
                              <label class="form-check form-check-single form-switch">
                                <input class="form-check-input" name="set_user_add" type="checkbox"'.checkboxState($group['set_user_add']).'>
                              </label>
                            </span>
                          </label>
                        </div>
                        <div>
                          <label class="row">
                            <span class="col ml-3">'.Translation::of('edit_user2').'</span>
                            <span class="col-auto">
                              <label class="form-check form-check-single form-switch">
                                <input class="form-check-input" name="set_user_edit" type="checkbox"'.checkboxState($group['set_user_edit']).'>
                              </label>
                            </span>
                          </label>
                        </div>
                        <div>
                          <label class="row">
                            <span class="col ml-3">'.Translation::of('delete_user2').'</span>
                            <span class="col-auto">
                              <label class="form-check form-check-single form-switch">
                                <input class="form-check-input" name="set_user_delete" type="checkbox"'.checkboxState($group['set_user_delete']).'>
                              </label>
                            </span>
                          </label>
                        </div>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('allow_settings').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="set_system" type="checkbox"'.checkboxState($group['set_system']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('allow_public_access').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="set_public" type="checkbox"'.checkboxState($group['set_public']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <hr />
                  <div class="mb-3">
                    <label id="moduleR" class="form-label">'.Translation::of('module_restrictions').'</label>
                    <div class="divide-y">
                      <div>
                        <label class="row">
                          <span class="col">'.Translation::of('restrict_individual_modules').'</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input toggle_div" data-src=".module_restriction" name="modules_enable" type="checkbox"'.checkboxState($group['modules_enable']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div class="module_restriction" style="display: none;">
                        <label class="form-label">'.Translation::of('choose_module_are_allowed').'</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                          '.$moduleRestrictionList.'
                        </div>
                      </div>

                    </div>
                  </div>

                </div>

                <div class="card-footer d-flex align-items-center">
                  <input type="hidden" name="groupID" value="'.$group['groupID'].'" />
                  <a href="'.$_moduleLink.'" class="btn btn-link mr-auto">'.Translation::of('cancel').'</a>
                  <button type="submit" name="editGroup" value="1" class="btn btn-primary">'.Translation::of('update').'</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
  ';
} else echo  '
    <div class="container-xl d-flex flex-column justify-content-center">
      <div class="empty">
        <div class="empty-icon">
          <img src="assets/img/undraw_cancel_u1it.svg" height="256" class="mb-4"  alt="">
        </div>
        <p class="empty-title h3">'.Translation::of('action_not_allowed').'</p>
        <p class="empty-subtitle text-muted">
          '.Translation::of('msg.you_cannot_change_admin').'
        </p>
        <a href="'.$backLink.'" class="btn btn-info btn-sm pr-5 pl-5">'.strtolower(Translation::of('go_back')).'</a>
      </div>
    </div>
';
  }

  else {
    $groupSQL = $db->query("SELECT * FROM `userGroups` ORDER BY name");
    echo '
    <div class="container-xl">
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-auto">
            <h2 class="page-title">
              '.Translation::of('group_settings').'
            </h2>
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
              <li class="breadcrumb-item"><a href="index.php?site=settings">'.Translation::of('settings').'</a></li>
              <li class="breadcrumb-item active" aria-current="page"><a href="'.$_moduleLink.'">'.Translation::of('group_settings').'</a></li>
            </ol>
          </div>
          <div class="col-auto ml-auto">
          <a href="'.$_moduleLink.'&action=newGroup" class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            '.Translation::of('new_group').'
          </a>
          <a href="index.php?site=usermanagement" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="5" y1="12" x2="19" y2="12" /><line x1="15" y1="16" x2="19" y2="12" /><line x1="15" y1="8" x2="19" y2="12" /></svg>
            '.Translation::of('goto_users').'
          </a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">'.Translation::of('groups').'</h3>
        </div>
        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <div class="text-muted">
              '.Translation::of('show').'
              <div class="mx-2 d-inline-block">
                <select class="form-select form-select-sm" id="groupsLength_change">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="-1">'.Translation::of('all').'</option>
                </select>
              </div>
              '.strtolower(Translation::of('entries')).'
            </div>
            <div class="ml-auto text-muted">
              '.Translation::of('search').':
              <div class="ml-2 d-inline-block">
                <input type="text" class="form-control form-control-sm" id="groupsSearch">
              </div>
            </div>
          </div>
        </div>
        <div class="table-responsive">

      <table class="table vertical-center" id="groups">
        <thead class="text-primary">
          <tr>
            <th>'.Translation::of('name').'</th>
            <th>'.Translation::of('users').'</th>
            <th><span class="d-none d-sm-block">'.Translation::of('options').'</span></th>
          </tr>
        </thead>
        <tbody>
            ';
while($group = $groupSQL->fetchArray(SQLITE3_ASSOC)){
  $mappingLists = NULL;
  $groupID = $group['groupID'];
  $mapSQL = $db->query("SELECT * FROM `userGroupMapping` WHERE groupID='".$groupID."'");
  while($map = $mapSQL->fetchArray(SQLITE3_ASSOC)){
    $mappingLists .= getUserAvatar($map['userID'], 'avatar-rounded');
  }

  if($groupID != 1) $optionBtn = '
    <a href="'.$_moduleLink.'&action=editGroup&groupID='.$group['groupID'].'" class="options btn btn-warning btn-icon mb-1" title="'.Translation::of('edit').'"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path><path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path><line x1="16" y1="5" x2="19" y2="8"></line></svg></a>
    <a href="#" data-toggle="modal" data-target="#confirmMessage" data-status="danger" data-text="'.Translation::of('msg.delete_really_group').'" data-href="'.$_moduleLink.'&action=deleteGroup&groupID='.$group['groupID'].'" class="btn btn-danger btn-icon mb-1" title="'.Translation::of('delete').'"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg></a>
  ';
  else $optionBtn = '';

  echo '
            <tr>
              <td>'.$group['name'].'</td>
              <td>
                <div class="avatar-list avatar-list-stacked">
                  '.$mappingLists.'
                </div>
              </td>
              <td>
                '.$optionBtn.'
              </td>
            </tr>
            ';
  }
echo '
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex align-items-center">
      <p class="m-0 text-muted" id="dataTables_info"></p>
      <span class="pagination m-0 ml-auto" id="dataTables_paginate"></span>
    </div>
';
echo '



  </div>
</div>
';
  }
}
else {
  sysinfo('danger', Translation::of('no_access'));
  redirect($backLink, 0);
}
