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

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');


if(getGroupID($loginUserID) == 1){

  $_moduleName = 'Group Management';
  $_moduleLink = 'index.php?site=groupmanagement';

  if(isset($_POST['saveGroup'])){
      $firstname  = $_POST['firstname'];
      $name       = $_POST['name'];
      $user       = $_POST['username'];
      $pass1      = md5($_POST['password1']);
      $pass2      = md5($_POST['password2']);
      $status     = $_POST['status'];
      $group      = $_POST['group'];

      if($user && ($pass1 == $pass2)){
        $db->exec("INSERT INTO `users` (username, password, firstname, name, refreshscreen, active) values('".$user."', '".$pass2."', '".$firstname."', '".$name."', 5, '".$status."')");
        $userSQL = $db->query("SELECT userID FROM `users` WHERE username='".$user."' AND password='".$pass2."'");
        $userSQL = $userSQL->fetchArray(SQLITE3_ASSOC);
        $db->exec("INSERT INTO `userGroupMapping` (userID, groupID) values('".$userSQL['userID']."', '".$group."')");
        sysinfo('success', $user.' updated successfully!');
      }

      redirect($_moduleLink, 0);
  }

  if(isset($_POST['editGroup'])){
    $firstname  = $_POST['firstname'];
    $name       = $_POST['name'];
    $user       = $_POST['username'];
    $status     = $_POST['status'];
    $group      = $_POST['group'];
    $userID     = $_POST['userID'];

    if($user && $userID){
      if($_POST['password1'] != '' && $_POST['password2'] != ''){
        $pass1 = md5($_POST['password1']);
        $pass2 = md5($_POST['password2']);
        if($pass1 == $pass2) $db->exec("UPDATE `users` SET username='".$user."', password='".$pass2."', firstname='".$firstname."', name='".$name."', active='".$status."' WHERE userID='".$userID."'");
      }
      else {
        $db->exec("UPDATE `users` SET username='".$user."', firstname='".$firstname."', name='".$name."', active='".$status."' WHERE userID='".$userID."'");
      }
      $db->exec("UPDATE `userGroupMapping` SET groupID='".$group."' WHERE userID='".$userID."'");
      sysinfo('success', $user.' updated successfully!');
    }
    redirect($backLink, 0);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'deleteGroup'){
    $userID = $_GET['userID'];
    if(isset($userID) AND $userID != $loginUserID){
      $db->exec("DELETE FROM `users` WHERE userID='".$userID."'");
      $db->exec("DELETE FROM `userGroupMapping` WHERE userID='".$userID."'");
      sysinfo('success', 'User successfully deleted!');
    }
    redirect($backLink, 0);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'newGroup'){
    echo '
    <div class="container-xl">
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-auto">
            <h2 class="page-title">
              New User
            </h2>
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
              <li class="breadcrumb-item"><a href="index.php?site=settings">Settings</a></li>
              <li class="breadcrumb-item"><a href="index.php?site=usermanagement">User Settings</a></li>
              <li class="breadcrumb-item active" aria-current="page"><a href="index.php?site=usermanagement&action=newUser">New User</a></li>
            </ol>
          </div>
          <div class="col-auto ml-auto d-print-none">
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-12">
          <div class="card card-lg">
            <form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
              <div class="card-body">
              <h2 id="personal">Personal</h2>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('firstname').'</label>
                  <div class="col">
                    <input name="firstname" type="text" class="form-control" id="InputFirstname" placeholder="John" required />
                  </div>
                </div>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('name').'</label>
                  <div class="col">
                    <input name="name" type="text" class="form-control" id="InputName" placeholder="Doe" required />
                  </div>
                </div>
                <hr />
                <h2 id="rights">User Rights</h2>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">Role</label>
                  <div class="col">
                    <select class="form-control" id="InputGroup" name="group">
                      '.createGroupsSelect(0).'
                    </select>
                  </div>
                </div>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">Status</label>
                  <div class="col">
                  <select class="form-control" id="InputStatus" name="status">
                    '.createStatusSelect(0).'
                  </select>
                  </div>
                </div>
                <hr />
                <h2 id="account">Account</h2>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('change_username').'</label>
                  <div class="col">
                    <input name="username" type="text" class="form-control" id="InputUsername" placeholder="Username" required />
                    <div class="help-block with-errors"></div>
                  </div>
                </div>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('change_username').'</label>
                  <div class="col">
                    <input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="Enter Password" />
                  </div>
                </div>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('change_username').'</label>
                  <div class="col">
                  <input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="Confirm Password" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" />
                  <div class="help-block with-errors"></div>
                  </div>
                </div>
              </div>
              <div class="card-footer d-flex align-items-center">
                <a href="index.php?site=usermanagement" class="btn btn-link mr-auto">'.Translation::of('cancel').'</a>
                <button type="submit" name="saveUser" class="btn btn-primary">'.Translation::of('save').'</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>';
  }

  elseif(isset($_GET['action']) && $_GET['action'] == 'editGroup'){
    function checkboxState($value){
      if($value == 1) $output = ' checked="1"';
      else $output = '';
      return $output;
    }

    $groupID   = $_GET['groupID'];
    if(TRUE) {
    //if($groupID != 1) {
      $groupSQL  = $db->query("SELECT * FROM `userGroups` WHERE groupID='".$groupID."'");
      $group     = $groupSQL->fetchArray(SQLITE3_ASSOC);

      $mappingLists = NULL;
      $mapSQL = $db->query("SELECT * FROM `userGroupMapping` WHERE groupID='".$groupID."'");
      while($map = $mapSQL->fetchArray(SQLITE3_ASSOC)){
        $mappingLists .= '
        <div class="col-6 row g-2 mb-3 align-items-center">
          <a href="index.php?site=usermanagement&action=editUser&userID='.$map['userID'].'" target="_blank" class="col-auto">
            '.getUserAvatar($map['userID']).'
          </a>
          <div class="col text-truncate">
            <a href="index.php?site=usermanagement&action=editUser&userID='.$map['userID'].'" target="_blank" class="text-body d-block text-truncate">'.getFullname($map['userID']).'</a>
            <small class="d-block text-muted text-truncate mt-n1">'.getUsername($map['userID']).'</small>
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
          <a href="#" target="_blank" class="text-body d-block text-truncate">No User </a>
          <small class="d-block text-muted text-truncate mt-n1">in this Group</small>
        </div>
      </div>
      ';

      $playerRestrictionList = NULL;
      $playerRestriction = unserialize($group['players']);

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

      $moduleArray = array(
        array('addon', 'Add-On'),
        array('multi', 'Multi Uploader'),
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




      echo '
      <div class="container-xl">
        <div class="page-header">
          <div class="row align-items-center">
            <div class="col-auto">
              <h2 class="page-title">
                Edit Group: '.$group['name'].'
              </h2>
              <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="index.php?site=settings">Settings</a></li>
                <li class="breadcrumb-item"><a href="index.php?site=groupmanagement">Group Settings</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="index.php?site=groupmanagement&action=editGroup&groupID='.$groupID.'">Edit: '.$group['name'].'</a></li>
              </ol>
            </div>
            <div class="col-auto ml-auto d-print-none">
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="d-none d-lg-block col-lg-4 order-lg-1 mb-4">
            <div class="sticky-top">
              <div class="card">
                <div class="card-body">
                  <label class="form-label">User List</label>
                  <div class="row mb-n3">
                    '.$mappingLists.'
                  </div>
                </div>
              </div>
              <h5 class="subheader">On this page</h5>
              <ul class="list-unstyled">
                <li class="toc-entry toc-h2"><a href="#gSettings">Group Settings</a></li>
                <li class="toc-entry toc-h2"><a href="#asset">Asset Restrictions</a></li>
                <li class="toc-entry toc-h2"><a href="#player">Player Restrictions</a></li>
                <li class="toc-entry toc-h2"><a href="#setting">Setting Restrictions</a></li>
                <li class="toc-entry toc-h2"><a href="#module">Module Restrictions</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="card card-lg">
              <form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
                <div class="card-body">
                <h2 id="gSettings">Group Settings</h2>
                  <div class="form-group mb-3 row">
                    <label class="form-label col-3 col-form-label">Group name</label>
                    <div class="col">
                      <input name="name" type="text" class="form-control" id="InputName" placeholder="Doe"  value="'.$group['name'].'" required />
                    </div>
                  </div>
                  <hr />
                  <h2 id="rights">Group Properties</h2>
                  <div class="mb-3">
                    <label id="asset" class="form-label">Asset Restrictions</label>
                    <div class="divide-y">
                      <div>
                        <label class="row">
                          <span class="col">Add asset</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_add" type="checkbox"'.checkboxState($group['ass_add']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Edit assets</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_edit" type="checkbox"'.checkboxState($group['ass_edit']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Delete assets</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_delete" type="checkbox"'.checkboxState($group['ass_delete']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Change Asset state</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="ass_state" type="checkbox"'.checkboxState($group['ass_state']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Clean Assets</span>
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
                    <label id="player" class="form-label">Player Restrictions</label>
                    <div class="divide-y">
                      <div>
                        <label class="row">
                          <span class="col">Add player</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name ="pla_add" type="checkbox"'.checkboxState($group['pla_add']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Edit player</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="pla_edit" type="checkbox"'.checkboxState($group['pla_edit']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Delete player</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="pla_delete" type="checkbox"'.checkboxState($group['pla_delete']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Reboot player</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="pla_reboot" type="checkbox"'.checkboxState($group['pla_reboot']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Restrict individual player</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input toggle_div" data-src=".player_restriction" name="players_enable" type="checkbox"'.checkboxState($group['players_enable']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div class="player_restriction" style="display: none;">
                        <label class="form-label">Choose Player which are allowed to use</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                          '.$playerRestrictionList.'
                        </div>
                      </div>

                    </div>
                  </div>
                  <hr />
                  <div class="mb-3">
                    <label id="setting" class="form-label">Setting Restrictions</label>
                    <div class="divide-y">
                      <div>
                        <label class="row">
                          <span class="col">Add User</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="set_user_add" type="checkbox"'.checkboxState($group['set_user_add']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Edit User</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="set_user_edit" type="checkbox"'.checkboxState($group['set_user_edit']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Delete User</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="set_user_delete" type="checkbox"'.checkboxState($group['set_user_delete']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Change System parameter</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input" name="set_system" type="checkbox"'.checkboxState($group['set_system']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div>
                        <label class="row">
                          <span class="col">Change Public access</span>
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
                    <label id="module" class="form-label">Module Restrictions</label>
                    <div class="divide-y">
                      <div>
                        <label class="row">
                          <span class="col">Restrict individual modules</span>
                          <span class="col-auto">
                            <label class="form-check form-check-single form-switch">
                              <input class="form-check-input toggle_div" data-src=".module_restriction" name="modules_enable" type="checkbox"'.checkboxState($group['modules_enable']).'>
                            </label>
                          </span>
                        </label>
                      </div>
                      <div class="module_restriction" style="display: none;">
                        <label class="form-label">Choose which Modules are allowed to use</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                          '.$moduleRestrictionList.'
                        </div>
                      </div>

                    </div>
                  </div>




                </div>


                <div class="card-footer d-flex align-items-center">
                  <input type="hidden" name="groupID" value="'.$group['groupID'].'" />
                  <a href="index.php?site=groupmanagement" class="btn btn-link mr-auto">'.Translation::of('cancel').'</a>
                  <button type="submit" name="editGroup" class="btn btn-primary">'.Translation::of('update').'</button>
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
        <p class="empty-title h3">Action not allowed</p>
        <p class="empty-subtitle text-muted">
          You cannot change the Administration Group!
        </p>
        <a href="'.$backLink.'" class="btn btn-info btn-sm pr-5 pl-5">go back</a>
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
              Group Settings
            </h2>
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
              <li class="breadcrumb-item"><a href="index.php?site=settings">Settings</a></li>
              <li class="breadcrumb-item active" aria-current="page"><a href="index.php?site=groupmanagement">Group Settings</a></li>
            </ol>
          </div>
          <div class="col-auto ml-auto">
          <a href="'.$_moduleLink.'&action=newGroup" class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            New Group
          </a>
          <a href="'.$_moduleLink.'&action=newGroup" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="5" y1="12" x2="19" y2="12" /><line x1="15" y1="16" x2="19" y2="12" /><line x1="15" y1="8" x2="19" y2="12" /></svg>
            Goto User Settings
          </a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Groups</h3>
        </div>
        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <div class="text-muted">
              Show
              <div class="mx-2 d-inline-block">
                <select class="form-select form-select-sm" id="groupsLength_change">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="-1">All</option>
                </select>
              </div>
              entries
            </div>
            <div class="ml-auto text-muted">
              Search:
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
            <th>Name</th>
            <th>Users</th>
            <th><span class="d-none d-sm-block">Options</span></th>
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
    <a href="'.$_moduleLink.'&action=editGroup&groupID='.$group['groupID'].'" class="options btn btn-warning btn-icon mb-1" title="edit"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path><path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path><line x1="16" y1="5" x2="19" y2="8"></line></svg></a>
    <a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="'.$_moduleLink.'&action=deleteGroup&groupID='.$group['groupID'].'" class="btn btn-danger btn-icon mb-1" title="delete"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg></a>
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
  sysinfo('danger', 'No Access to this module!');
  redirect($backLink, 0);
}
