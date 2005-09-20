<?php

require 'includes.php';
require_once 'login.php' ;

try
{
  if (!util::isNull($_SESSION['user_id']))
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

      if (!$p->isSuperAdmin())
	{
	  util::throwException('not authorized') ;
	}

      // Printing results in HTML
      echo "<table border=1 cellpadding=2 cellspacing=0>\n";
      echo "<th>Name</th><th>Abbr</th><th>Email</th><th>IRC</th><th>Locaction</th><th>Approved?</th><th colspan=2>Actions</th>";
      
      foreach (team::getAllTeams() as $t)
	{
	  $loc = new location(array('location_id'=>$t->getValue('location_id')));
	  $loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
	  echo "\t<tr>\n";
	  echo "\t<td>",$t->getValue('name'),"</td>\n";
	  echo "\t<td>",$t->getValue('name_abbr'),"</td>\n";
	  echo "\t<td>",$t->getValue('email'),"</td>\n";
	  echo "\t<td>",$t->getValue('irc_channel'),"</td>\n";
	  echo "\t<td>",$loc_name,"</td>\n";
	  //      echo "\t<td>(encrypted)</td>\n";
	  echo "\t<td>",util::strbool($t->getValue('approved')),"</td>\n";
	  echo "<td><a href='?a=manageTeam&amp;mode=edit&amp;team_id=",$t->getValue('team_id'),"'>Edit</a></td>";
	  echo "<td><a href='?a=saveTeam&amp;mode=delete&amp;team_id=",$t->getValue('team_id'),"'>Delete</a></td>";
	  echo "\t</tr>\n";
	}
      echo "</table>\n";

      echo "<p><a href='?a=manageTeam'>Create Team</a>";
    }
}
catch (Exception $e) {}
?>

