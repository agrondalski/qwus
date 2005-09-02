<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$maps = map::getAllMaps();

include 'tourneyLinks.php';

// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Name</th><th>Abbr</th><th>Game_Type_id</th>";
foreach ($maps as $m) {
   echo "\t<tr>\n";
   echo "\t<td>",$m->getValue('map_name'),"</td>\n";
   echo "\t<td>",$m->getValue('map_abbr'),"</td>\n";
   echo "\t<td>",$m->getValue('game_type_id'),"</td>\n";
   echo "\t</tr>\n";
}
echo "</table>\n";
?>

