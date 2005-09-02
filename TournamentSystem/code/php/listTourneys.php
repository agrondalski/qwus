<?php
require 'includes.php' ;

$tid = $_REQUEST['tourney_id'];
$tourneys = tourney::getAllTourneys();

include 'tourneyLinks.php';

// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>tourney_id</th><th>Tourney Name</th>";
foreach ($tourneys as $t) {
  echo "\t<tr>\n";
  echo "\t<td>",$t->getValue('tourney_id'),"</td>\n";
  echo "\t<td><a href='?a=tourneyHome&tourney_id=",$t->getValue('tourney_id'),"'>",$t->getValue('name'),"</a></td>\n";
  echo "\t</tr>\n";

}
echo "</table>\n";

?>
