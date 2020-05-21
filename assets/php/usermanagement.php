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
      Usermanagement Module
________________________________________
*/

if(getGroupID($loginUserID) == 1){

  $_moduleName = 'User Management';
  $_moduleLink = 'index.php?site=usermanagement';

  if(isset($_POST['save'])){
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
      }
      redirect($_moduleLink, 0);
  }

  if(isset($_POST['edit'])){
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
    }
  }

  if(isset($_GET['action']) && $_GET['action'] == 'deleteUser'){
    $userID = $_GET['userID'];
    if(isset($userID) AND $userID != $loginUserID){
      $db->exec("DELETE FROM `users` WHERE userID='".$userID."'");
      $db->exec("DELETE FROM `userGroupMapping` WHERE userID='".$userID."'");
    }
    redirect($backLink, 0);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'newUser'){
    echo '
    <div class="row justify-content-md-center">
      <div class="col-md-10">
        <div class="card">
          <div class="card-header">
            <h5 class="title">'.$_moduleName.' > Create User</h5>
          </div>
          <div class="card-body">
            <form id="accountForm" action="'.$_moduleLink.'" method="POST" data-toggle="validator">
              <div class="form-group">
                <label for="InputUsername">Username</label>
                <input name="username" type="text" class="form-control" id="InputUsername" placeholder="Username" required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group">
                <label for="InputPassword1">Password</label>
                <input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="Enter Password" required />
              </div>
              <div class="form-group">
                <input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="Confirm Password" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group">
                <label for="InputGroup">Role</label>
                <select class="form-control" id="InputGroup" name="group">
                  '.createGroupsSelect().'
                </select>
              </div>
              <div class="form-group">
                <label for="InputStatus">Status</label>
                <select class="form-control" id="InputStatus" name="status">
                  '.createStatusSelect(0).'
                </select>
              </div>
              <hr />
              <div class="form-group">
                <label for="InputFirstname">Firstname</label>
                <input name="firstname" type="text" class="form-control" id="InputFirstname" placeholder="John" required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group">
                <label for="InputName">Name</label>
                <input name="name" type="text" class="form-control" id="InputName" placeholder="Doe" required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group text-right">
                <button type="submit" name="save" class="btn btn-sm btn-primary">Create User</button>
                <a href="'.$_moduleLink.'" class="btn btn-sm btn-danger">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>';
  }

  elseif(isset($_GET['action']) && $_GET['action'] == 'editUser'){
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
    <div class="row justify-content-md-center">
      <div class="col-md-10">
        <div class="card">
          <div class="card-header">
            <h5 class="title">'.$_moduleName.' > Edit: '.getUserName($userID).'</h5>
          </div>
          <div class="card-body">
            <form id="accountForm" action="'.$_moduleLink.'" method="POST" data-toggle="validator">
              <div class="form-group">
                <label for="InputUsername">Username</label>
                <input name="username" type="text" class="form-control" id="InputUsername" placeholder="Username" value="'.$user['username'].'" required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group">
                <label for="InputPassword1">Password</label>
                <input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="Enter Password" />
              </div>
              <div class="form-group">
                <input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="Confirm Password" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" />
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group">
                <label for="InputGroup">Role</label>
                <select class="form-control" id="InputGroup" name="group"'.$disable.'>
                  '.createGroupsSelect(getGroupID($user['userID'])).'
                </select>
              </div>
              <div class="form-group">
                <label for="InputStatus">Status</label>
                <select class="form-control" id="InputStatus" name="status"'.$disable.'>
                  '.createStatusSelect($user['active']).'
                </select>
              </div>
              <hr />
              <div class="form-group">
                <label for="InputFirstname">Firstname</label>
                <input name="firstname" type="text" class="form-control" id="InputFirstname" placeholder="John"  value="'.$user['firstname'].'" required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group">
                <label for="InputName">Name</label>
                <input name="name" type="text" class="form-control" id="InputName" placeholder="Doe"  value="'.$user['name'].'" required />
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group text-right">
                <input type="hidden" name="userID" value="'.$user['userID'].'" />
                '.$group.'
                '.$status.'
                <button type="submit" name="edit" class="btn btn-sm btn-primary">Update User</button>
                <a href="'.$_moduleLink.'" class="btn btn-sm btn-danger">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>';
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
              <div class="col-md-2 float-right">
                <a href="'.$_moduleLink.'&action=newUser" class="btn btn-success btn-sm btn-block"><i class="tim-icons icon-simple-add"></i> New User</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table class="table" id="users">
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
              <tbody>';
    while($user = $userSQL->fetchArray(SQLITE3_ASSOC)){
      if(isActive($user['userID']) == 1) $active = '<span class="badge badge-success">  activated  </span>';
      else $active = '<span class="badge badge-danger">  deactivated  </span>';

      echo '
                <tr>
                  <td>'.$user['username'].'</td>
                  <td>'.getFullname($user['userID']).'</td>
                  <td>'.getGroupName($user['userID']).'</td>
                  <td>'.$active.'</td>
                  <td>'.lastLogin($user['userID']).'</td>
                  <td>
                    <a href="'.$_moduleLink.'&action=editUser&userID='.$user['userID'].'" class="options btn btn-warning btn-sm mb-1" title="edit"><i class="tim-icons icon-pencil"></i></a>
                    <a href="#" data-toggle="modal" data-target="#confirmDelete" data-href="'.$_moduleLink.'&action=deleteUser&userID='.$user['userID'].'" class="btn btn-danger btn-sm mb-1" title="delete"><i class="tim-icons icon-simple-remove"></i></a>
                  </td>
                </tr>
                ';
      }

      echo '
              </tbody>
            </table>
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
