<?php

global $loggedIn, $loginUserID, $loginUsername, $loginPassword, $loginGroupID;
$loginUserID          = 0;
$loggedIn             = FALSE;
$loginUsername        = '';
$loginPassword        = '';
$loginGroupID         = 0;
$adminUserManagement  = '';
$adminSettings        = '';

function getGroupID($userID){
  global $db;
  $sql     = $db->query("SELECT * FROM `userGroupMapping` WHERE userID='".$userID."'");
  $return  = $sql->fetchArray(SQLITE3_ASSOC);
  return $return['groupID'];
}

function getGroupName($userID){
  global $db;
  $id = getGroupID($userID);
  $sql   = $db->query("SELECT * FROM `userGroups` WHERE groupID='".$id."'");
  $return   = $sql->fetchArray(SQLITE3_ASSOC);
  return $return['name'];
}

function getFullname($userID){
  global $db;
  $sql    = $db->query("SELECT * FROM `users` WHERE userID='".$userID."'");
  $sql    = $sql->fetchArray(SQLITE3_ASSOC);
  isset($sql['firstname']) ? $f = $sql['firstname'].' ' : $f = '';
  isset($sql['name']) ? $n = $sql['name'] : $n = '';
  $return = $f.$n;
  return $return;
}

function lastLogin($userID){
  global $db;
  $sql   = $db->query("SELECT last_login FROM `users` WHERE userID='".$userID."'");
  $return   = $sql->fetchArray(SQLITE3_ASSOC);
  return $return['last_login'];
}

function isActive($userID){
  global $db;
  $sql   = $db->query("SELECT active FROM `users` WHERE userID='".$userID."'");
  $return   = $sql->fetchArray(SQLITE3_ASSOC);
  if($return['active'] == 1) return TRUE;
  else return FALSE;
}

if(isset($_POST['Login']) AND isset($_POST['user']) AND isset($_POST['password'])){
  $user           = $_POST['user'];
  $pass           = md5($_POST['password']);
  $userSQL			  = $db->query("SELECT * FROM users WHERE username='".$user."' AND password='".$pass."'");
  $userSQL 			  = $userSQL->fetchArray(SQLITE3_ASSOC);
  $loginUsername 	= $userSQL['username'];
  $loginPassword 	= $userSQL['password'];
  $loginActive   	= $userSQL['active'];
  if($user == $loginUsername && $pass == $loginPassword && $loginActive == 1){
    $now = time();
    $_SESSION['user'] 			= $user;
    $_SESSION['password'] 	= $pass;
    $db->exec("UPDATE `users` SET last_login='".$now."' WHERE userID=".$userSQL['userID']);
  }
 redirect('index.php', 0);
}

if(isset($_GET['action']) && $_GET['action'] == 'logout'){
  if(session_destroy()){
    $logedout = true;
    $_SESSION['password'] = '';
  } else $logedout = FALSE;
  redirect('index.php', 0);
}

if(isset($_SESSION['user']) AND isset($_SESSION['password'])) {
  $userSQL			    = $db->query("SELECT * FROM `users` WHERE username='".$_SESSION['user']."' AND password='".$_SESSION['password']."'");
  $userSQL 			    = $userSQL->fetchArray(SQLITE3_ASSOC);
  if($_SESSION['user'] == $userSQL['username'] && $_SESSION['password'] == $userSQL['password']){
    $loginUserID 	    = $userSQL['userID'];
    $loginUsername    = $userSQL['username'];
    $loginPassword    = $userSQL['password'];

    $loginFirstname   = $userSQL['firstname'];
    $loginName        = $userSQL['name'];
    $loginRefreshTime = $userSQL['refreshscreen'];
    $loginFullname    = getFullname($loginUserID);

    $loginGroupID     = getGroupID($loginUserID);
    $loginGroupName   = getGroupName($loginUserID);

    if(getGroupID($loginUserID) == 1){
      $adminUserManagement = '
        <li class="nav-link">
          <a href="index.php?site=usermanagement" class="nav-item dropdown-item">User Management</a>
        </li>
      ';

      $adminSettings = '
        <li class="nav-link">
         <a href="javascript:void(0)" data-toggle="modal" data-target="#settings" class="nav-item dropdown-item">Settings</a>
       </li>
      ';
    }

    $loggedIn = TRUE;
  }
  else $loggedIn = FALSE;
}
else $loggedIn = FALSE;
