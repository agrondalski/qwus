<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$players = player::getAllPlayers();

include 'tourneyLinks.php';

// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Name</th><th>Superadmin?</th><th>Location</th><th>Password</th>";
foreach ($players as $p) {
   echo "\t<tr>\n";
   echo "\t<td>",$p->getValue('name'),"</td>\n";
   echo "\t<td>",$p->getValue('superAdmin'),"</td>\n";
   echo "\t<td>",$p->getValue('location_id'),"</td>\n";
   echo "\t<td>",$p->getValue('password'),"</td>\n";
   echo "\t</tr>\n";
}
echo "</table>\n";
?>

