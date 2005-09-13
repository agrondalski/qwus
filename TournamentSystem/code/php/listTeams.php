<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$teams = team::getAllTeams();

include 'tourneyLinks.php';

// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Name</th><th>Abbr</th><th>Email</th><th>IRC</th><th>Loc</th><th>Password</th><th>Approved?</th><th colspan=2>Actions</th>";
foreach ($teams as $t) {
    $loc = new location(array('location_id'=>$t->getValue('location_id')));
    $loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
	echo "\t<tr>\n";
	echo "\t<td>",$t->getValue('name'),"</td>\n";
	echo "\t<td>",$t->getValue('name_abbr'),"</td>\n";
	echo "\t<td>",$t->getValue('email'),"</td>\n";
	echo "\t<td>",$t->getValue('irc_channel'),"</td>\n";
	echo "\t<td>",$loc_name,"</td>\n";
	echo "\t<td>(encrypted)</td>\n";
	echo "\t<td>",$t->getValue('approved'),"</td>\n";
	echo "<td><a href='?a=manageTeam&amp;tourney_id=$tid&amp;mode=edit&amp;team_id=",$t->getValue('team_id'),"'>
Edit</a></td>";
	echo "<td><a href='?a=saveTeam&amp;tourney_id=$tid&amp;mode=delete&amp;team_id=",$t->getValue('team_id'),"'>
Delete</a></td>";
	echo "\t</tr>\n";
}
echo "</table>\n";
?>

