<?php
$host = '192.168.178.54';
$port = '22';
$user = 'pi';
$pass = 'raspberry';
shell_exec("php /var/www/html/monitor/assets/php/addon_installer.php ".$host." ".$port." ".$user." ".$pass);
