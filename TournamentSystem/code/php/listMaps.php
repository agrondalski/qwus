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

  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Name</th><th>Abbr</th><th>Game Type</th>";

  foreach (map::getAllMaps() as $m)
    {
      $gt = new game_type(array('game_type_id'=>$m->getValue('game_type_id'))) ;
      
      echo "\t<tr>\n";
      echo "\t<td>" . $m->getValue('map_name'). "</td>\n";
      echo "\t<td>" . $m->getValue('map_abbr') . "</td>\n";
      echo "\t<td>" . $gt->getValue('name') . "</td>\n";
      echo "\t</tr>\n";
    }
  echo "</table>\n";
}
catch (Exception $e) {}
?>

