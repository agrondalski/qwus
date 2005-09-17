<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && $p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  echo "<br>";
  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Map</th><th>Full Name</th>";

  foreach ($t->getMaps() as $map)
    {
      echo "\t<tr>\n";
      echo "\t<td>",$map->getValue('map_abbr'),"</td>\n";
      echo "\t<td>",$map->getValue('map_name'),"</td>\n";
      echo "<td><a href='?a=saveTourneyMap&amp;tourney_id=$tid&amp;mode=delete&amp;map_id=",$map->getValue('map_id'),"'>Delete</a></td>";
      echo "\t</tr>\n";
    }
  echo "</table>\n";

  echo "<form action='?a=saveTourneyMap' method=post>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  echo "<select name='map_id'>";

  foreach ($t->getGameTypeMaps() as $m)
    {
      echo "<option value='" . $m->getValue('map_id') . "'>" . $m->getValue('map_abbr') . ":" . $m->getValue('map_name');
    }

  echo "</select>&nbsp;&nbsp;";
  echo "<input type='submit' value='Add Map' name='B1' class='button'>";
  echo "<br>";
}
catch (Exception $e) {}

?>
