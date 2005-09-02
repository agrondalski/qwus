<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

include 'tourneyLinks.php';

// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Div Name</th><th>Max Teams</th><th># of Games</th>";
foreach ($t->getDivisions() as $div) {
   echo "\t<tr>\n";
   echo "\t<td>",$div->getValue('name'),"</td>\n";
   echo "\t<td>",$div->getValue('max_teams'),"</td>\n";
   echo "\t<td>",$div->getValue('num_games'),"</td>\n";
   echo "\t</tr>\n";
}
echo "</table>\n";
?>
