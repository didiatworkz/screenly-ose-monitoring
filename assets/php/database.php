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

$reloadSite = '
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>SOMO - Maintenance mode</title>
    <!-- Tabler Core -->
    <link href="assets/css/tabler.min.css?t=1607864306" rel="stylesheet"/>
    <!-- Tabler Plugins -->
    <link href="assets/css/monitor.css?t=1607864306" rel="stylesheet"/>
  </head>
  <body class="antialiased border-top-wide border-primary d-flex flex-column">
    <div class="flex-fill d-flex align-items-center justify-content-center">
      <div class="container py-6">
        <div class="empty">
          <div class="empty-icon">
            <img src="assets/img/undraw_server_down_s4lk.svg" height="256" class="mb-4"  alt="maintenance">
          </div>
          <p class="empty-title h3">Temporarily down for maintenance</p>
          <p class="empty-subtitle text-muted pt-4">
            This page will update itself in a few moments. <br />If this is not the case after 30 seconds, please refresh this page!
          </p>
        </div>
      </div>
    </div>
  </body>
</html>';

$systemVersion  = file_get_contents('assets/tools/version.txt');

$db 			      = new SQLite3('database.db');
$db             ->busyTimeout(5000);
$set 			      = $db->query("SELECT * FROM settings");
$set 			      = $set->fetchArray(SQLITE3_ASSOC);
$securityToken	= $set['token'];
$updatecheck	  = $set['updatecheck'];



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
    $db->exec("CREATE TABLE `settings` (`settingsID` INTEGER PRIMARY KEY AUTOINCREMENT,`duration`	INTEGER,	`token`	TEXT,	`name`	TEXT,	`end_date`	INTEGER, `firstStart`	INTEGER,	`updatecheck`	INTEGER, `design` INTEGER DEFAULT 0, `uploadMaxSize` INTEGER DEFAULT 50, `timezone` TEXT DEFAULT 'Europe/Berlin', `sessionTime` INTEGER DEFAULT 36000, `debug` INTEGER DEFAULT 0)");
    $db->exec("INSERT INTO `settings`(name,duration,token,end_date,updatecheck) SELECT name,duration,token,end_date,updatecheck FROM `settings_tmp`");
    $db->exec("UPDATE `settings` SET name='SOMO' WHERE settingsID=1");
    $db->exec("DROP TABLE `settings_tmp`");
    $db->exec("CREATE TABLE IF NOT EXISTS `log` (`logID` INTEGER PRIMARY KEY AUTOINCREMENT, `userID`	INTEGER DEFAULT 0, `logTime`	INTEGER, `moduleName`	TEXT, `info`	TEXT, `show`	INTEGER DEFAULT 0, `relevant`	INTEGER DEFAULT 0)");
    $db->exec("ALTER TABLE `userGroups` RENAME TO `userGroups_tmp`");
    $db->exec("CREATE TABLE `userGroups` (`groupID` INTEGER PRIMARY KEY AUTOINCREMENT, `name`	TEXT,	`players`	TEXT,	`players_enable` INTEGER DEFAULT 0,	`modules`	TEXT,	`modules_enable` INTEGER DEFAULT 0,	`ass_add`	INTEGER DEFAULT 0,	`ass_edit`	INTEGER DEFAULT 0,	`ass_delete`	INTEGER DEFAULT 0,	`ass_clean`	INTEGER DEFAULT 0,	`ass_state`	INTEGER DEFAULT 0, `pla_add`	INTEGER DEFAULT 0, `pla_edit`	INTEGER DEFAULT 0,	`pla_delete`	INTEGER DEFAULT 0, 	`pla_reboot`	INTEGER DEFAULT 0,	`mod_multi`	INTEGER DEFAULT 0,	`mod_addon`	INTEGER DEFAULT 0,	`set_system`	INTEGER DEFAULT 0, `set_user`	INTEGER DEFAULT 0,	`set_user_add`	INTEGER DEFAULT 0,	`set_user_edit`	INTEGER DEFAULT 0,	`set_user_delete`	INTEGER DEFAULT 0,	`set_public`	INTEGER DEFAULT 0)");
    $db->exec("INSERT INTO `userGroups`(groupID,name) SELECT groupID,name FROM `userGroups_tmp`");
    $db->exec("UPDATE `userGroups` SET players='".serialize()."', modules='".serialize()."', ass_add=1, ass_edit=1, ass_delete=1, ass_clean=1, ass_state=1, pla_add=1, pla_edit=1,	pla_delete=1, pla_reboot=1,	set_system=1,	set_user_add=1,	set_user_edit=1, set_user_delete=1, set_public=1 WHERE name='Admin'");
    $db->exec("DROP TABLE `userGroups_tmp`");
    $db->exec("ALTER TABLE `users` RENAME TO `users_tmp`");
    $db->exec("CREATE TABLE `users` (`userID` INTEGER PRIMARY KEY AUTOINCREMENT, `username`	TEXT NOT NULL,	`password`	TEXT NOT NULL,	`firstname`	TEXT,	`name`	TEXT,	`refreshscreen`	INTEGER DEFAULT 5,	`updateEntry`	INTEGER, `active`	INTEGER,	`last_login` INTEGER,	`news` INTEGER DEFAULT 0,	`design` INTEGER DEFAULT 0,	`activate_addon` INTEGER DEFAULT 1)");
    $db->exec("INSERT INTO `users`(userID,username,password,firstname,name,refreshscreen,updateEntry,active,last_login) SELECT userID,username,password,firstname,name,refreshscreen,updateEntry,active,last_login FROM `users_tmp`");
    $db->exec("DROP TABLE `users_tmp`");
  }
  if($oldVersion <= '4.2'){			// Update Database to Version 4.2
    // none
  }
  unlink('assets/tools/version_old.txt');
  unlink('update.txt');
  header("Refresh:3");
  die($reloadSite);
}
