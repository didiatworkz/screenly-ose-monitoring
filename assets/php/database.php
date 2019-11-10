<?php $dbase_key		= 'assets/tools/key.php';
if(!@file_exists($dbase_key)) {
  $dbase_file = 'dbase.db';
} else {
  include_once($dbase_key);
  $dbase_file = $db_cryproKey;
  if(@file_exists('dbase.db')) {
    unlink('dbase.db');
  }

}

$db 			= new SQLite3($dbase_file);
$set 			= $db->query("SELECT * FROM settings WHERE userID = 1");
$set 			= $set->fetchArray(SQLITE3_ASSOC);
$loginUsername 	= $set['username'];
$loginPassword 	= $set['password'];
$loginUserID 	= $set['userID'];
$securityToken	= $set['token'];
$updatecheck	= $set['updatecheck'];
$systemVersion  = file_get_contents('assets/tools/version.txt');
$apiVersion		= 'v1.2';

if(!@file_exists($dbase_key)){
  $token = md5($systemVersion.time().$loginPassword).'.db';
  $keyFile = '<?php
  $db_cryproKey = "'.$token.'";';
  $current = file_get_contents($dbase_key);
  file_put_contents($dbase_key, $keyFile);
  rename("dbase.db",$token);
  header("Refresh:0");
  die("Reload this page");
}

if(@file_exists('assets/tools/version_old.txt')){
  $oldVersion = file_get_contents('assets/tools/version_old.txt');
  if($oldVersion <= '2.0'){			// Update Database to Version 2.0
    $db->exec("ALTER TABLE `settings` ADD COLUMN `token` TEXT");
    $db->exec("ALTER TABLE `settings` ADD COLUMN `end_date` INTEGER");
    $db->exec("ALTER TABLE `settings` ADD COLUMN `duration` INTEGER");
    $db->exec("UPDATE `settings` SET token='d1bf93299de1b68e6d382c893bf1215f' WHERE userID=1");
    $db->exec("UPDATE `settings` SET end_date=1 WHERE userID=1");
    $db->exec("UPDATE `settings` SET duration=30 WHERE userID=1");
  }
  if($oldVersion <= '2.1'){			// Update Database to Version 2.1
    $db->exec("ALTER TABLE `settings` ADD COLUMN `updatecheck` INTEGER");
    $db->exec("ALTER TABLE `settings` ADD COLUMN `refreshscreen` INTEGER");
    $db->exec("UPDATE `settings` SET updatecheck=0 WHERE userID=1");
    $db->exec("UPDATE `settings` SET refreshscreen=5 WHERE userID=1");
  }
  if($oldVersion <= '3.0'){			// Update Database to Version 3.0
    //Nothing
  }
  unlink('assets/tools/version_old.txt');
  unlink('update.txt');
  header("Refresh:0");
  die("Reload this page");
}
