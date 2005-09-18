<?php

require 'includes.php';
require_once 'login.php' ;

try
{
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin())
    {
      util::throwException('not authorized') ;
    }

  // Printing results in HTML
  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Name</th><th>Abbr</th><th>Game Type</th><th colspan=2>Actions</th>";

  foreach (map::getAllMaps() as $m)
    {
      $gt = new game_type(array('game_type_id'=>$m->getValue('game_type_id'))) ;
      
      echo "\t<tr>\n";
      echo "\t<td>" . $m->getValue('map_name'). "</td>\n";
      echo "\t<td>" . $m->getValue('map_abbr') . "</td>\n";
      echo "\t<td>" . $gt->getValue('name') . "</td>\n";
      echo "<td><a href='?a=manageMap&amp;mode=edit&amp;map_id=" . $m->getValue('map_id') . "'>Edit</a></td>";
      echo "<td><a href='?a=saveMap&amp;mode=delete&amp;map_id=" . $m->getValue('map_id') . "'>Delete</a></td>";	
      echo "\t</tr>\n";
    }

  echo "</table>\n";
  echo "<p><a href='?a=manageMap'>Create Map</a>";
}
catch (Exception $e) {}
?>
