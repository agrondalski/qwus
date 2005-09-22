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
  echo "<th>Name</th><th>Super Admin</th><th>Location</th><th>Column</th><th colspan=2>Actions</th>";

  foreach (player::getAllPlayers() as $p)
    {
      $loc = new location(array('location_id'=>$p->getValue('location_id')));
      $loc_name = $loc->getValue('country_name') ;

      echo "\t<tr>\n";
      echo "\t<td>",$p->getValue('name'),"</td>\n";
      echo "\t<td>",util::strbool($p->getValue('superAdmin')),"</td>\n";
      echo "\t<td>",$loc_name,"</td>\n";
      echo "\t<td>",util::strbool($p->getValue('hasColumn')),"</td>\n";
      echo "<td><a href='?a=managePlayer&amp;mode=edit&amp;player_id=",$p->getValue('player_id'),"'>Edit</a></td>";
      echo "<td><a href='?a=savePlayer&amp;mode=delete&amp;player_id=",$p->getValue('player_id'),"'>Delete</a></td>";	
      echo "\t</tr>\n";
    }

  echo "</table>\n";
  echo "<p><a href='?a=managePlayer'>Create Player</a>";
}
catch (Exception $e) {}
?>
