<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $tid = $_REQUEST['tourney_id']; 

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  echo "<br>";
  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Div Name</th><th># of Games</th><th>Playoff Spots</th>";
  echo "<th>Elim Losses</th><th>Edit</th><th>Delete</th>";

  foreach ($t->getDivisions() as $div)
    {
      echo "\t<tr>\n";
      echo "\t<td>",$div->getValue('name'),"</td>\n";
      echo "\t<td>",$div->getValue('num_games'),"</td>\n";
      echo "\t<td>",$div->getValue('playoff_spots'),"</td>\n";
      echo "\t<td>",$div->getValue('elim_losses'),"</td>\n";
      echo "<td><a href='?a=manageDivision&amp;tourney_id=$tid&amp;mode=edit&amp;did=",$div->getValue('division_id'),"'>";
      echo "Edit</a></td>";
      echo "<td><a href='?a=saveDivision&amp;tourney_id=$tid&amp;mode=delete&amp;did=",$div->getValue('division_id'),"'>";
      echo "Delete</a></td>";
      echo "\t</tr>\n";
    }
  echo "</table>\n";
  echo "<p><a href='?a=manageDivision&amp;tourney_id=$tid'>Create Division</a>&nbsp;-&nbsp;";
  echo "<a href='?a=assignTeamsToDiv&tourney_id=$tid'>Assign Teams to Division</a>";
}
catch (Exception $e) {}
?>
