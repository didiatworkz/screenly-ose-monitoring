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
          Database Module
_______________________________________
*/

$dbase_key		= $_SERVER['DOCUMENT_ROOT'].'/assets/tools/key.php';
if(!@file_exists($dbase_key)) {
  $dbase_file = $_SERVER['DOCUMENT_ROOT'].'/dbase.db';
} else {
  include_once($dbase_key);
  $dbase_file = $db_cryproKey;
  if(@file_exists($_SERVER['DOCUMENT_ROOT'].'/dbase.db')) {
    unlink($_SERVER['DOCUMENT_ROOT'].'/dbase.db');
  }

}

if(!@file_exists($dbase_key)){
  $token = md5($systemVersion.time().$loginPassword).'.db';
  $keyFile = '<?php
  $db_cryproKey = "'.$token.'";';
  $current = file_get_contents($dbase_key);
  file_put_contents($dbase_key, $keyFile);
  rename($_SERVER['DOCUMENT_ROOT'].'/dbase.db',$token);
  header("Refresh:0");
  die("Reload this page");
}

$db 			      = new SQLite3($dbase_file);
$set 			      = $db->query("SELECT * FROM settings");
$set 			      = $set->fetchArray(SQLITE3_ASSOC);
$securityToken	= $set['token'];
$updatecheck	  = $set['updatecheck'];
$systemVersion  = file_get_contents('assets/tools/version.txt');


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
  if($oldVersion <= '3.3'){			// Update Database to Version 3.3
    $db->exec("ALTER TABLE `settings` RENAME TO `settings_tmp`");
    $db->exec("CREATE TABLE `settings` (`settingsID` INTEGER PRIMARY KEY AUTOINCREMENT,`duration`	INTEGER,	`token`	TEXT,	`end_date`	INTEGER, `firstStart`	INTEGER,	`updatecheck`	INTEGER)");
    $db->exec("INSERT INTO `settings`(duration,token,end_date,updatecheck) SELECT duration,token,end_date,updatecheck FROM `settings_tmp`");
    $db->exec("CREATE TABLE `users` (`userID` INTEGER PRIMARY KEY AUTOINCREMENT, `username`	TEXT NOT NULL,`password`	TEXT NOT NULL, `firstname`	TEXT, `name`	TEXT, 	`refreshscreen`	INTEGER, `new` INTEGER,	`updateEntry`	INTEGER, `active`	INTEGER, `last_login`	INTEGER)");
    $db->exec("INSERT INTO `users`(username,password,refreshscreen) SELECT username,password,refreshscreen FROM `settings_tmp`");
    $db->exec("UPDATE `users` SET updateEntry=0 WHERE userID=1");
    $db->exec("UPDATE `users` SET firstname='John' WHERE userID=1");
    $db->exec("UPDATE `users` SET name='Doe' WHERE userID=1");
    $db->exec("UPDATE `users` SET active=1 WHERE userID=1");
    $db->exec("UPDATE `users` SET updateEntry='".time()."' WHERE userID=1");
    $db->exec("DROP TABLE `settings_tmp`");
    $db->exec("CREATE TABLE `userGroups` (`groupID` INTEGER PRIMARY KEY AUTOINCREMENT,`name`	TEXT)");
    $db->exec("INSERT INTO `userGroups` (name) VALUES('Admin')");
    $db->exec("INSERT INTO `userGroups` (name) VALUES('User')");
    $db->exec("CREATE TABLE `userGroupMapping` (`mappingID` INTEGER PRIMARY KEY AUTOINCREMENT,`userID`	INTEGER, `groupID`	INTEGER)");
    $db->exec("INSERT INTO `userGroupMapping` (userID,groupID) VALUES(1,1)");
  }
  if($oldVersion <= '3.4'){			// Update Database to Version 3.4
    $db->exec("ALTER TABLE `settings` RENAME TO `settings_tmp`");
    $db->exec("CREATE TABLE `settings` (`settingsID` INTEGER PRIMARY KEY AUTOINCREMENT,`duration`	INTEGER,	`token`	TEXT,	`name`	TEXT,	`end_date`	INTEGER, `firstStart`	INTEGER,	`updatecheck`	INTEGER)");
    $db->exec("INSERT INTO `settings`(duration,token,end_date,updatecheck) SELECT duration,token,end_date,updatecheck FROM `settings_tmp`");
    $db->exec("UPDATE `settings` SET name='Screenly OSE Monitoring' WHERE settingsID=1");
    $db->exec("DROP TABLE `settings_tmp`");
  }
  if($oldVersion <= '4.0'){			// Update Database to Version 4.0
    $db->exec("ALTER TABLE `player` RENAME TO `player_tmp`");
    $db->exec("CREATE TABLE `player` (`playerID` INTEGER PRIMARY KEY AUTOINCREMENT,`userID`	INTEGER,	`name`	TEXT,	`address`	TEXT UNIQUE,	`location`	TEXT, `player_user`	TEXT,	`player_password`	TEXT,	`monitorOutput`	TEXT DEFAULT 0,	`deviceInfo`	TEXT DEFAULT 0, `status` INTEGER DEFAULT 0, `assets` TEXT, `logOutput`	TEXT,	`sync`	TEXT,	`bg_sync`	TEXT,	`created`	TEXT DEFAULT CURRENT_TIMESTAMP)");
    $db->exec("INSERT INTO `player`(userID,name,location,address,player_user,player_password,sync,created) SELECT userID,name,location,address,player_user,player_password,sync,created FROM `player_tmp`");
    $db->exec("DROP TABLE `player_tmp`");
    $db->exec("ALTER TABLE `settings` RENAME TO `settings_tmp`");
    $db->exec("CREATE TABLE `settings` (`settingsID` INTEGER PRIMARY KEY AUTOINCREMENT,`duration`	INTEGER,	`token`	TEXT,	`name`	TEXT,	`end_date`	INTEGER, `firstStart`	INTEGER,	`updatecheck`	INTEGER, `design` INTEGER DEFAULT 0, `timezone` TEXT DEFAULT 'Europe/Berlin', `sessionTime` INTEGER DEFAULT 36000)");
    $db->exec("INSERT INTO `settings`(name,duration,token,end_date,updatecheck) SELECT name,duration,token,end_date,updatecheck FROM `settings_tmp`");
    $db->exec("UPDATE `settings` SET name='SOMO' WHERE settingsID=1");
    $db->exec("DROP TABLE `settings_tmp`");

    $db->exec("CREATE TABLE `log` (`logID` INTEGER PRIMARY KEY AUTOINCREMENT, `userID`	INTEGER DEFAULT 0, `logTime`	INTEGER, `moduleName`	TEXT, `info`	TEXT, `show`	INTEGER DEFAULT 0, `relevant`	INTEGER DEFAULT 0)");

    $db->exec("ALTER TABLE `userGroups` RENAME TO `userGroups_tmp`");
    $db->exec("CREATE TABLE `userGroups` (`groupID` INTEGER PRIMARY KEY AUTOINCREMENT, `name`	TEXT,	`players`	TEXT,	`modules`	TEXT,	`addFunction`	INTEGER DEFAULT 0, `editFunction`	INTEGER DEFAULT 0, `deleteFunction`	INTEGER DEFAULT 0)");
    $db->exec("INSERT INTO `userGroups`(groupID,name) SELECT groupID,name FROM `userGroups_tmp`");
    $db->exec("UPDATE `userGroups` SET addFunction=1, editFunction=1, deleteFunction=1 WHERE name='Admin'");
    $db->exec("DROP TABLE `userGroups_tmp`");

    $db->exec("ALTER TABLE `users` RENAME TO `users_tmp`");
    $db->exec("CREATE TABLE `users` (`userID` INTEGER PRIMARY KEY AUTOINCREMENT, `username`	TEXT NOT NULL,	`password`	TEXT NOT NULL,	`firstname`	TEXT,	`name`	TEXT,	`refreshscreen`	INTEGER DEFAULT 5,	`updateEntry`	INTEGER, `active`	INTEGER,	`last_login` INTEGER,	`news` INTEGER DEFAULT 0,	`design` INTEGER DEFAULT 0)");
    $db->exec("INSERT INTO `users`(userID,username,password,firstname,name,refreshscreen,updateEntry,active,last_login) SELECT userID,username,password,firstname,name,refreshscreen,updateEntry,active,last_login FROM `users_tmp`");
    $db->exec("DROP TABLE `users_tmp`");

  }
  unlink('assets/tools/version_old.txt');
  unlink('update.txt');
  header("Refresh:0");
  die("Reload this page");
}
