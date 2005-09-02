<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

include 'tourneyLinks.php';
echo "<br>";
// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>id</th><th>Div Name</th><th>Max Teams</th><th># of Games</th><th>Playoff Spots</th>";
echo "<th>Elim Losses</th><th>Edit</th><th>Delete</th>";
foreach ($t->getDivisions() as $div) {
   echo "\t<tr>\n";
   echo "\t<td>",$div->getValue('division_id'),"</td>\n";
   echo "\t<td>",$div->getValue('name'),"</td>\n";
   echo "\t<td>",$div->getValue('max_teams'),"</td>\n";
   echo "\t<td>",$div->getValue('num_games'),"</td>\n";
   echo "\t<td>",$div->getValue('playoff_spots'),"</td>\n";
   echo "\t<td>",$div->getValue('elim_losses'),"</td>\n";
   echo "<td><a href='?a=addDivision&amp;tourney_id=$tid&mode=edit&did=",$div->getValue('division_id'),"'>
Edit</a></td>";
   echo "<td><a href='?a=saveDivision&amp;tourney_id=$tid&mode=delete&did=",$div->getValue('division_id'),"'>
Delete</a></td>";

   echo "\t</tr>\n";
}
echo "</table>\n";
?>
