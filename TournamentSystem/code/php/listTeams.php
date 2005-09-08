<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$teams = team::getAllTeams();

include 'tourneyLinks.php';

// Printing results in HTML
echo "<table border=1 class=admintbl cellpadding=2 cellspacing=0>\n";
echo "<th>Team Name</th><th>Email</th><th>IRC</th><th>Location</th><th>Password</th><th>Approved?</th>";
foreach ($teams as $t) {
   echo "\t<tr>\n";
   echo "\t<td>",$t->getValue('name'),"</td>\n";
   echo "\t<td>",$t->getValue('email'),"</td>\n";
   echo "\t<td>",$t->getValue('irc_channel'),"</td>\n";
   echo "\t<td>",$t->getValue('location_id'),"</td>\n";
   echo "\t<td>",$t->getValue('password'),"</td>\n";
   echo "\t<td>",$t->getValue('approved'),"</td>\n";
   echo "\t</tr>\n";
}
echo "</table>\n";
?>

