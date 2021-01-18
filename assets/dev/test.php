<?php

include('sqlite.class.php');

$db = new SQLite3Database('../xxxx.db');
$sql = 'SELECT * FROM `player`';
$test = $db->get_rows($sql);

$test2 = $db->query($sql);
#$test = $db->fetch_row($bla);
echo '<pre>';
#print_r($test->username);
print_r($test);
print_r($test2);
echo '</pre>';

//
// while($player = $db->get_rows($sql, TRUE)){
//   $player->name;
// }
