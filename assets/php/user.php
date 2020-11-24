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
             User Module
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');


global $loggedIn, $loginUserID, $loginUsername, $loginPassword, $loginGroupID;
$loginUserID          = 0;
$loggedIn             = FALSE;
$loginUsername 	      = '';
$loginPassword 	      = '';
$loginGroupID         = 0;

function getGroupID($userID){
  global $db;
  $sql     = $db->query("SELECT * FROM `userGroupMapping` WHERE userID='".$userID."'");
  $return  = $sql->fetchArray(SQLITE3_ASSOC);
  return $return['groupID'];
}

function getGroupName($groupID){
  global $db;
  $id = getGroupID($groupID);
  $sql   = $db->query("SELECT * FROM `userGroups` WHERE groupID='".$id."'");
  $return   = $sql->fetchArray(SQLITE3_ASSOC);
  return $return['name'];
}

function createGroupsSelect($pre = null){
  global $db;
  $groupsSQL  = $db->query("SELECT groupID, name FROM `userGroups`");
  $options    = NULL;

  while($group = $groupsSQL->fetchArray(SQLITE3_ASSOC)){
    if(isset($pre)){
      if($group['groupID'] == $pre) $select = ' selected="selected"';
      else $select = '';
    }
    else {
      if($group['name'] == 'User') $select = ' selected="selected"';
      else $select = '';
    }
    $options .= '<option value="'.$group['groupID'].'"'.$select.'>'.$group['name'].'</option>
                  ';
  }
  return $options;
}

function createStatusSelect($pre = 0){
  global $db;
  $select = ' selected="selected"';
  $notSelect = '';
    if(isset($pre)){
      if($pre == 0) {
        $op0 = $select;
        $op1 = $notSelect;
      }
      else {
        $op0 = $notSelect;
        $op1 = $select;
      }
    }
    $options = '<option value="0"'.$op0.'>deactivated</option>
                <option value="1"'.$op1.'>activated</option>
                 ';
  return $options;
}


function getUserName($userID){
  global $db;
  $sql     = $db->query("SELECT username FROM `users` WHERE userID='".$userID."'");
  $return  = $sql->fetchArray(SQLITE3_ASSOC);
  return $return['username'];
}

function getFullname($userID){
  global $db;
  $sql    = $db->query("SELECT userID, firstname, name FROM `users` WHERE userID='".$userID."'");
  $sql    = $sql->fetchArray(SQLITE3_ASSOC);
  isset($sql['firstname']) ? $f = $sql['firstname'].' ' : $f = '';
  isset($sql['name']) ? $n = $sql['name'] : $n = '';
  $return = $f.$n;
  return $return;
}

function getNameInitials($userID){
  global $db;
  $sql    = $db->query("SELECT userID, firstname, name FROM `users` WHERE userID='".$userID."'");
  $sql    = $sql->fetchArray(SQLITE3_ASSOC);
  isset($sql['firstname']) ? $f = $sql['firstname'].' ' : $f = '';
  isset($sql['name']) ? $n = $sql['name'] : $n = '';
  $return = substr($f, 0, 1).substr($n, 0, 1);
  $return = strtoupper($return);
  return $return;
}

function getFirstname($userID){
  global $db;
  $sql    = $db->query("SELECT userID, firstname FROM `users` WHERE userID='".$userID."'");
  $sql    = $sql->fetchArray(SQLITE3_ASSOC);
  isset($sql['firstname']) ? $f = $sql['firstname'].' ' : $f = '';
  return $f;
}

function getLastname($userID){
  global $db;
  $sql    = $db->query("SELECT userID, name FROM `users` WHERE userID='".$userID."'");
  $sql    = $sql->fetchArray(SQLITE3_ASSOC);
  isset($sql['name']) ? $l = $sql['name'].' ' : $l = '';
  return $l;
}

function lastLogin($userID){
  global $db;
  $sql      = $db->query("SELECT last_login FROM `users` WHERE userID='".$userID."'");
  $return   = $sql->fetchArray(SQLITE3_ASSOC);
  return date("Y-m-d H:i", $return['last_login']);
}

function lastLoginTimestamp($userID){
  global $db;
  $sql      = $db->query("SELECT last_login FROM `users` WHERE userID='".$userID."'");
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

function getUserAvatar($userID, $extraClass = ''){
  global $db;
  if($extraClass != '') $extraClass = ' '.$extraClass;
  $sql     = $db->query("SELECT username FROM `users` WHERE userID='".$userID."'");
  $return  = $sql->fetchArray(SQLITE3_ASSOC);
  $imagePath = 'assets/img/avatars/'.md5($return['username']).'.txt';
  if (file_exists($imagePath)) {
    $data = file_get_contents($imagePath);
    $output = '<span class="avatar'.$extraClass.'" style="background-image: url('.$data.')"></span>';
  }
  else $output = '<span class="avatar '.$extraClass.'">'.getNameInitials($userID).'</span>';
  return $output;
}

// Group Rights
function hasAddRight($userID){
  global $db;
  $groupID = getGroupID($userID);
  $sql      = $db->query("SELECT addFunction FROM `userGroups` WHERE groupID='".$groupID."'");
  $return   = $sql->fetchArray(SQLITE3_ASSOC);
  if ($return['addFunction'] == 1) return TRUE;
  return FALSE;
}

function hasEditRight($userID){
  global $db;
  $groupID = getGroupID($userID);
  $sql      = $db->query("SELECT editFunction FROM `userGroups` WHERE groupID='".$groupID."'");
  $return   = $sql->fetchArray(SQLITE3_ASSOC);
  if ($return['editFunction'] == 1) return TRUE;
  return FALSE;
}

function hasDeleteRight($userID){
  global $db;
  $groupID = getGroupID($userID);
  $sql      = $db->query("SELECT deleteFunction FROM `userGroups` WHERE groupID='".$groupID."'");
  $return   = $sql->fetchArray(SQLITE3_ASSOC);
  if ($return['deleteFunction'] == 1) return TRUE;
  return FALSE;
}

if(isset($_POST['Login']) && isset($_POST['user']) && isset($_POST['password'])){
  $user           = $_POST['user'];
  $pass           = md5(stripslashes($_POST['password']));
  $userSQL			  = $db->query("SELECT * FROM `users` WHERE username='".$user."'");
  $userSQL 			  = $userSQL->fetchArray(SQLITE3_ASSOC);
  $loginID 	      = $userSQL['userID'];
  $loginUsername 	= $userSQL['username'];
  $loginPassword 	= $userSQL['password'];
  $loginActive   	= $userSQL['active'];
  if($user == $loginUsername && $pass == $loginPassword){
    if($loginActive == 1){
      $now = time();
      $_SESSION['somo_auth'] = $loginID.":".$loginPassword;
			$_SESSION['somo_referer'] = $_SERVER['HTTP_REFERER'];
      $db->exec("UPDATE `users` SET last_login='".$now."' WHERE userID=".$userSQL['userID']);
      systemLog('Login', $loginUsername.' logged in');
      redirect($_SESSION['somo_referer'], 0);
      die();
    } else {
        sysinfo('warning', 'User not activated!');
        systemLog('Login', $loginUsername.' not activated!');
    }
  }  else {
       sysinfo('danger', 'The entered login data are not correct!');
       systemLog('Login', $loginUsername.' data are not correct!');
  }
}

$loggedIn     = FALSE;
$loginUserID  = 0;

if(isset($_SESSION['somo_auth'])) {
  if(stristr($_SESSION['somo_auth'], "userid") === false){
		$authent = explode(":", $_SESSION['somo_auth']);

		$somo_user = $authent['0'];
		$somo_pass = $authent['1'];

    if(isset($somo_user) and isset($somo_pass)) {
      $userSQL = $db->query("SELECT * FROM `users` WHERE userID='".$somo_user."' AND password='".$somo_pass."'");

      while($user = $userSQL->fetchArray(SQLITE3_ASSOC)) {
        $loginUserID 	    = $user['userID'];
        $loginUsername    = $user['username'];
        $loginPassword    = $user['password'];
        $loginFirstname   = $user['firstname'];
        $loginName        = $user['name'];
        $loginRefreshTime = $user['refreshscreen'];
        $loginFullname    = getFullname($loginUserID);
        $loginGroupID     = getGroupID($loginUserID);
        $loginGroupName   = getGroupName($loginUserID);
        $loggedIn = TRUE;
      }
    }
  }
}

if(isset($_GET['action']) && $_GET['action'] == 'logout'){
  systemLog('Login', $loginUsername.' logged out');
  if(session_destroy()){
    $logedout = true;
    $_SESSION['password'] = '';
  } else $logedout = FALSE;
  redirect('index.php', 0);
}

if(isset($_POST['saveAccount'])){
  $firstname  = $_POST['firstname'];
  $name       = $_POST['name'];
  $user       = $_POST['username'];
  $firstStart = $_POST['firstStartUser'];
  if($_POST['password1'] != '' && $_POST['password2'] != ''){
    $pass1 = md5($_POST['password1']);
    $pass2 = md5($_POST['password2']);
  }
  else {
    $pass1 = $loginPassword;
    $pass2 = $loginPassword;
  }

  if($user && ($pass1 == $pass2)){
    $db->exec("UPDATE `users` SET username='".$user."', password='".$pass2."', firstname='".$firstname."', name='".$name."' WHERE userID='".$loginUserID."'");
    if($firstStart == 1){
      $db->exec("UPDATE settings SET firstStart='2' WHERE settingsID='1'");
    }
    sysinfo('success', 'Account data saved!', 0);
  }
  else sysinfo('danger', 'Error!');
  redirect($backLink);
}
