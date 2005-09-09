<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$players = player::getAllPlayers();

include 'tourneyLinks.php';

// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Name</th><th>S.A.?</th><th>Location</th><th>Password</th><th>Has Column?</th><th colspan=2>Actions</th>";
foreach ($players as $p) {
	$loc = new location(array('location_id'=>$p->getValue('location_id')));
    $loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
	echo "\t<tr>\n";
	echo "\t<td>",$p->getValue('name'),"</td>\n";
	echo "\t<td>",$p->getValue('superAdmin'),"</td>\n";
	echo "\t<td>",$loc_name,"</td>\n";
	echo "\t<td>(encrypted)</td>\n";
	echo "\t<td>",$p->getValue('hasColumn'),"</td>\n";
	echo "<td><a href='?a=managePlayer&amp;tourney_id=$tid&amp;mode=edit&amp;player_id=",$p->getValue('player_id'),"'>
Edit</a></td>";
	echo "<td><a href='?a=savePlayer&amp;tourney_id=$tid&amp;mode=delete&amp;player_id=",$p->getValue('player_id'),"'>
Delete</a></td>";	
	echo "\t</tr>\n";
}
echo "</table>\n";
?>

