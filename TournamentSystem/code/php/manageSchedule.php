<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  $division_id = $_REQUEST['division_id'];

  try
    {
      $div = new division(array('division_id'=>$division_id));
    }
  catch (Exception $e)
    {
      $div = "";
    }

  echo "<br>";
  // Printing results in HTML
  echo "<form action='?a=manageSchedule' method=post>";
  echo "<table border=0 cellpadding=2 cellspacing=0>";
  echo "<tr><td><b>Pick a division:</b></td>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  echo "<td><select name='division_id'>";

  foreach ($t->getDivisions(array('name', SORT_ASC)) as $tmp)
    {
      $sel = "";
      if ($tmp->getValue('division_id') == $division_id)
	{
	  $sel = "selected";
	}

      echo "<option value='",$tmp->getValue('division_id'),"' ",$sel,">",$tmp->getValue('name');
    }

  echo "</select></td></tr>";
  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button'>";
  echo "<br></td></tr>";
  echo "</table></form>";

  if ($div != "")
    {
      echo "<table border=1 cellpadding=2 cellspacing=0>\n";
      echo "<th>Matchup</th><th>Week</th><th>Approved</th><th>Match Date</th><th>Deadline</th><th>Edit</th><th>Delete</th>";

      foreach ($div->getMatchSchedule(array('name', SORT_ASC)) as $ms)
	{
	  foreach($ms->getMatches() as $m)
	    {
	      $teams = $m->getTeams() ;
	      $t1 = $teams[0] ;
	      $t2 = $teams[1] ;

	      if ($m->getValue('approved')==1)
		{
		  $approved = 'yes' ;
		}
	      else
		{
		  $approved = 'no' ;
		}

	      echo "\t<tr>\n";

	      echo "\t<td><a href='?a=detailsMatch&amp;tourney_id=" . $t->getValue('tourney_id') . "&amp;match_id=" . $m->getValue('match_id'). "'>" ;
	      echo "". $t1->getValue('name') . " vs. ";
	      echo "" . $t2->getValue('name') . "</a></td>\n";
	      echo "\t<td>" . $ms->getValue('name') . "</td>\n";
	      echo "\t<td>" . $approved . "</td>\n";
	      echo "\t<td>" . $m->getValue('match_date') . "</td>\n";
	      echo "\t<td>" . $ms->getValue('deadline') . "</td>\n";
	      echo "<td><a href='?a=manageSchedule&amp;tourney_id=$tid&amp;mode=edit&amp;division_id=" . $division_id . "&amp;match_id=" . $m->getValue('match_id') . "'>Edit</a></td>";
	      echo "<td><a href='?a=saveSchedule&amp;tourney_id=$tid&amp;mode=delete&amp;division_id=" . $division_id . "&amp;match_id=" . $m->getValue('match_id') . "'>Delete</a></td>";
	    }
	}

      echo "</tr></table>";
	
      // Randomly Generate schedule button
      echo "<form action='?a=saveRandomSchedule' method=post>";
      echo "<input type='hidden' name='tourney_id' value='$tid'>";
      echo "<input type='hidden' name='division_id' value='$division_id'>";
      echo "<table border=1 cellpadding=2 cellspacing=0>";
      echo "<tr><br>";
      echo "<td><b>Games to Play:</b></td>";
      echo "<td><b>",$div->getValue('num_games'),"</b></td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td><b>Schedule length in Weeks:</b></td>";
      echo "<td><input type='text' name='num_weeks' value='' size='10'></td>";
      echo "</tr>";
      echo "<tr><td><b>Generate New Schedule:</b></td><td>";
      echo "<input type='submit' value='Create Schedule' name='B1' class='button'></td>";
      echo "</tr></table></form>";

      // Show teams
      $mode = $_REQUEST['mode'] ;
      if ($mode!='edit')
	{
	  echo "<form action='?a=saveSchedule' method=post>";
	  echo "<input type='hidden' name='tourney_id' value='$tid'>";
	  echo "<input type='hidden' name='division_id' value='$division_id'>";
	  echo "<table border=1 cellpadding=2 cellspacing=0>";
	  echo "<tr><td colspan=2><b>Schedule an individual Match:</b></td></tr>";
	  echo "<tr><td><b>Team 1:</b></td>";

	  $team_list = '' ;
	  foreach ($div->getTeams(array('name', SORT_ASC)) as $tmp)
	    {
	      $team_list .= "<option value='" . $tmp->getValue('team_id') . "'>" . $tmp->getValue('name') ;
	    }

	  echo "<td><select name='team1_id'>";
	  echo $team_list ;
	  echo "</select></td></tr>";

	  echo "<tr><td><b>Team 2:</b></td>";
	  echo "<td><select name='team2_id'>";
	  echo $team_list ;
	  echo "</select></td></tr>";

	  echo "<tr><td><b>Scheduled:</b></td>";
	  echo "<td><select name='schedule_id'>";
	  
	  foreach ($div->getMatchSchedule(array('name', SORT_ASC)) as $tmp)
	    {
	      echo "<option value='" . $tmp->getValue('schedule_id') . "'>" . $tmp->getValue('name') . ":" . $tmp->getValue('deadline');
	    }

	  echo "</select><br>";
	  echo "<a href='?a=manageMatchSchedule&amp;tourney_id=$tid&amp;division_id=",$division_id,"'>Manage Schedule Weeks</a></td></tr>";

	  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Add' name='B1' class='button'>";
	  echo "<br></td></tr></table></form>";
	}
      else
	{
	  $m = new match(array('match_id'=>$_REQUEST['match_id'])) ;

	  echo "<form action='?a=saveSchedule' method=post>";
	  echo "<input type='hidden' name='tourney_id' value='$tid'>";
	  echo "<input type='hidden' name='division_id' value='$division_id'>";
	  echo "<input type='hidden' name='match_id' value='" . $_REQUEST['match_id'] . "'>";
	  echo "<input type='hidden' name='mode' value='edit'>";
	  echo "<table border=1 cellpadding=2 cellspacing=0>";
	  echo "<tr><td colspan=2><b>Update an individual Match:</b></td></tr>";

	  $teams = $m->getTeams() ;

	  echo "<tr><td><b>Team 1:</b></td><td>" . $teams[0]->getValue('name') . "</td></tr>" ;
	  echo "<td><b>Team 2:</b></td><td>" . $teams[1]->getValue('name') . "</td></tr>" ;

	  $ms = $m->getMatchSchedule() ;
	  echo "<tr><td><b>Scheduled:</b></td><td>" . $ms->getValue('name') . ':' . $ms->getValue('deadline') . "</td></tr>";

	  $app = $m->getValue('approved') ;
	  $no_val = '' ;
	  $yes_val = '' ;
	  if ($app==0)
	    {
	      $no_val = ' selected' ;
	    }
	  else
	    {
	      $yes_val = ' selected' ;
	    }

	  echo "<tr><td><b>Approved</b></td>" ;
	  echo "<td><select name='approved'>";
	  echo "<option value='0' $no_val> No" ;
	  echo "<option value='1' $yes_val> Yes" ;
	  echo "</select><br></td></tr>" ;

	  $match_date = $m->getValue('match_date') ;

	  echo "<tr><td><b>Match Date</b></td>" ;
	  echo "<td><input type='text' name='match_date' value='$match_date' size='10'></td><tr>";
      
	  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
	  echo "<br></td></tr></table></form>";
	}
    }
}
catch (Exception $e) {}
?>
