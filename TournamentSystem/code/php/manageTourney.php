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

  $mode = $_REQUEST['mode'];

  if ($mode == "edit")
    {
      echo "<b><p>Modify a Tournament:</b></p>";

      $tourney_id = $_REQUEST['tourney_id'];
      $tour       = new tourney(array('tourney_id'=>$tourney_id)) ;

      $t_name    = $tour->getValue('name');
      $gt_id     = $tour->getValue('game_type_id');
      $tour_type = $tour->getValue('tourney_type');
      $sstart    = $tour->getValue('signup_start');
      $send      = $tour->getValue('signup_end');
      $team_size = $tour->getValue('team_size');
      $timelimit = $tour->getValue('timelimit');
    }

  else
    {
      echo "<p><b>Create a Tournament:</b></p>";

      $t_name    = '' ;
      $gt_id     = '' ;
      $tour_type = '' ;
      $sstart    = '' ;
      $send      = '' ;
      $team_size = '' ;
      $timelimit = '' ;
    }

  echo "<form action='?a=saveTourney' method=post>";

  if ($mode == "edit")
    {
      echo "<input type='hidden' name='tourney_id' value='$tourney_id'>";
      echo "<input type='hidden' name='mode' value='edit'>";
    }

  echo "<table border=1 cellpadding=2 cellspacing=0>";
  echo "<tr>";
  echo "<td>Name:</td><td>";
  echo "<input type='text' name='tourney_name' maxlength='50' value='" . $t_name . "' size='50'></td>";
  echo "</tr>";

  echo "<tr><td>Game Type:</td><td>";
  echo "<select name='game_type_id'>";
 
  foreach (game_type::getAllGameTypes() as $gt)
    {
      $sel = "";
      if ($gt->getValue('game_type_id') == $gt_id)
	{
	  $sel = "selected";
	}

      echo "<option value='" . $gt->getValue('game_type_id') . "'" . $sel . ">" . $gt->getValue('name') . $state_name ;
    }

  echo "</select></td>";
  echo "</tr>";

  echo "<tr><td>Game Type:</td><td>";
  echo "<select name='tourney_type'>";

  if ($tour_type=='LEAGUE')
    {
      echo "<option value='1' selected>League" ;
    }
  else
    {
      echo "<option value='1'>League" ;
    }

  if ($tour_type=='TOURNAMENT')
    {
      echo "<option value='2' selected>Tournament" ;
    }
  else
    {
      echo "<option value='2'>Tournament" ;
    }

  if ($tour_type=='LADDER')
    {
      echo "<option value='3' selected>Ladder" ;
    }
  else
    {
      echo "<option value='3'>Ladder" ;
    }

  echo "</select></td>";
  echo "</tr>";

  echo "<tr>";
  echo "<td>Signup Start:</td><td>";
  echo "<input type='text' name='signup_start' maxlength='50' value='" . $sstart . "' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Signup End:</td><td>";
  echo "<input type='text' name='signup_end' maxlength='50' value='" . $send . "' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Team size:</td><td>";
  echo "<input type='text' name='team_size' maxlength='50' value='" . $team_size . "' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Timelimit:</td><td>";
  echo "<input type='text' name='timelimit' maxlength='50' value='" . $timelimit . "' size='50'></td>";
  echo "</tr>";

  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";
  echo "</form>" ;

  include 'listTourneys.php';
}
catch (Exception $e) {print $e;}
?>