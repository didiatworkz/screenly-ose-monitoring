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
        Usermanagement Module
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');


if(getGroupID($loginUserID) == 1 || hasSettingsUserRight($loginUserID)){

  $_moduleName = 'User Management';
  $_moduleLink = 'index.php?site=usermanagement';

  if(isset($_POST['saveUser'])){
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
        systemLog($_moduleName, 'Create User: '.$user, $loginUserID, 1);
      }

      redirect($_moduleLink, 0);
  }

  if(isset($_POST['editUser'])){
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
      systemLog($_moduleName, 'Edit User: '.$user, $loginUserID, 1);
    }
    redirect($backLink, 0);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'deleteUser' && hasSettingsUserDeleteRight($loginUserID)){
    $userID = $_GET['userID'];
    if(isset($userID) AND $userID != $loginUserID){
      systemLog($_moduleName, 'Delete User: '.getUserName($userID), $loginUserID, 1);
      $db->exec("DELETE FROM `users` WHERE userID='".$userID."'");
      $db->exec("DELETE FROM `userGroupMapping` WHERE userID='".$userID."'");
      sysinfo('success', 'User successfully deleted!');
    }
    redirect($backLink, 0);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'newUser' && hasSettingsUserAddRight($loginUserID)){
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

  elseif(isset($_GET['action']) && $_GET['action'] == 'editUser' && hasSettingsUserEditRight($loginUserID)){
    $userID   = $_GET['userID'];
    $userSQL  = $db->query("SELECT * FROM `users` WHERE userID='".$userID."'");
    $user     = $userSQL->fetchArray(SQLITE3_ASSOC);
    if($userID == $loginUserID) {
      $disable  = ' disabled="disabled"';
      $group    = '<input type="hidden" name="group" value="'.getGroupID($user['userID']).'" />';
      $status   = '<input type="hidden" name="status" value="'.$user['active'].'" />';
    }
    else {
      $disable  = '';
      $group    ='';
      $status   = '';
    }
    echo '
    <div class="container-xl">
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-auto">
            <h2 class="page-title">
              Edit User: '.getUserName($userID).'
            </h2>
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
              <li class="breadcrumb-item"><a href="index.php?site=settings">Settings</a></li>
              <li class="breadcrumb-item"><a href="index.php?site=usermanagement">User Settings</a></li>
              <li class="breadcrumb-item active" aria-current="page"><a href="index.php?site=usermanagement&action=editUser&userID='.$userID.'">Edit: '.getUserName($userID).'</a></li>
            </ol>
          </div>
          <div class="col-auto ml-auto d-print-none">
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="d-none d-lg-block col-lg-3 order-lg-1 mb-4">
          <div class="sticky-top">
            <div class="card">
              <div class="card-body text-center">
                <div class="mb-3">
                  <span class="avatar avatar-xl">
                    '.getFirstname($userID)[0].getLastname($userID)[0].'
                  </span>
                </div>
                <div class="card-title mb-1">'.getFullName($userID).'</div>
                <div class="text-muted">'.getGroupName(getGroupID($userID)).'</div>
              </div>
            </div>
            <h5 class="subheader">On this page</h5>
            <ul class="list-unstyled">
              <li class="toc-entry toc-h2"><a href="#personal">Personal Settings</a></li>
              <li class="toc-entry toc-h2"><a href="#rights">User Right Settings</a></li>
              <li class="toc-entry toc-h2"><a href="#account">Account Settings</a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-9">
          <div class="card card-lg">
            <form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
              <div class="card-body">
              <h2 id="personal">Personal</h2>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('firstname').'</label>
                  <div class="col">
                    <input name="firstname" type="text" class="form-control" id="InputFirstname" placeholder="John"  value="'.$user['firstname'].'" required />
                  </div>
                </div>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('name').'</label>
                  <div class="col">
                    <input name="name" type="text" class="form-control" id="InputName" placeholder="Doe"  value="'.$user['name'].'" required />
                  </div>
                </div>
                <hr />
                <h2 id="rights">User Rights</h2>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">Role</label>
                  <div class="col">
                    <select class="form-control" id="InputGroup" name="group"'.$disable.'>
                      '.createGroupsSelect(getGroupID($user['userID'])).'
                    </select>
                  </div>
                </div>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">Status</label>
                  <div class="col">
                  <select class="form-control" id="InputStatus" name="status"'.$disable.'>
                    '.createStatusSelect($user['active']).'
                  </select>
                  </div>
                </div>
                <hr />
                <h2 id="account">Account</h2>
                <div class="form-group mb-3 row">
                  <label class="form-label col-3 col-form-label">'.Translation::of('change_username').'</label>
                  <div class="col">
                    <input name="username" type="text" class="form-control" id="InputUsername" placeholder="Username" value="'.$user['username'].'" required />
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
                <input type="hidden" name="userID" value="'.$user['userID'].'" />
                '.$group.'
                '.$status.'
                <a href="index.php?site=usermanagement" class="btn btn-link mr-auto">'.Translation::of('cancel').'</a>
                <button type="submit" name="editUser" class="btn btn-primary">'.Translation::of('update').'</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
';
  }

  else {
    $userSQL = $db->query("SELECT * FROM `users` ORDER BY userID");
    $userAddBtn = '';
    if(hasSettingsUserAddRight($loginUserID))  $userAddBtn = '<a href="'.$_moduleLink.'&action=newUser" class="btn btn-success">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      New User
    </a>';

    echo '
    <div class="container-xl">
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-auto">
            <h2 class="page-title">
              User Settings
            </h2>
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
              <li class="breadcrumb-item"><a href="index.php?site=settings">Settings</a></li>
              <li class="breadcrumb-item active" aria-current="page"><a href="index.php?site=usermanagement">User Settings</a></li>
            </ol>
          </div>
          <div class="col-auto ml-auto">
          '.$userAddBtn.'
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Users</h3>
        </div>
        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <div class="text-muted">
              Show
              <div class="mx-2 d-inline-block">
                <select class="form-select form-select-sm" id="usersLength_change">
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
                <input type="text" class="form-control form-control-sm" id="usersSearch">
              </div>
            </div>
          </div>
        </div>
        <div class="table-responsive">

      <table class="table vertical-center" id="users">
        <thead class="text-primary">
          <tr>
            <th>Username</th>
            <th>Name</th>
            <th>Group</th>
            <th>Active</th>
            <th>Last Login</th>
            <th><span class="d-none d-sm-block">Options</span></th>
          </tr>
        </thead>
        <tbody>
            ';
while($user = $userSQL->fetchArray(SQLITE3_ASSOC)){
  if(isActive($user['userID']) == 1) $active = '<span class="badge bg-success">  activated  </span>';
  else $active = '<span class="badge bg-dark">  deactivated  </span>';

  $userEditBtn = '';
  $userDeleteBtn = '';

  if(hasSettingsUserEditRight($loginUserID)) $userEditBtn = '<a href="'.$_moduleLink.'&action=editUser&userID='.$user['userID'].'" class="options btn btn-warning btn-icon mb-1" title="edit"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path><path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path><line x1="16" y1="5" x2="19" y2="8"></line></svg></a>';
  if(hasSettingsUserDeleteRight($loginUserID)) $userDeleteBtn = '<a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="'.$_moduleLink.'&action=deleteUser&userID='.$user['userID'].'" class="btn btn-danger btn-icon mb-1" title="delete"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg></a>';

  echo '
            <tr>
              <td>'.$user['username'].'</td>
              <td>'.getFullname($user['userID']).'</td>
              <td>'.getGroupName($user['userID']).'</td>
              <td>'.$active.'</td>
              <td>'.lastLogin($user['userID']).'</td>
              <td>
                '.$userEditBtn.'
                '.$userDeleteBtn.'
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
