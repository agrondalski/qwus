<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  $mode = $_REQUEST['mode'];

  if ($mode=='edit')
    {
      $tourney_id = $_REQUEST['tourney_id'];
      $tour       = new tourney(array('tourney_id'=>$tourney_id)) ;
    }

  if (!$p->isSuperAdmin() && ($mode!='edit' || !$p->isTourneyAdmin($tour->getValue('tourney_id'))))
    {
      util::throwException('not authorized') ;
    }

  if ($mode == "edit")
    {
      echo "<b><p>Modify a Tournament:</b></p>";

      $t_name    = $tour->getValue('name');
      $rules     = $tour->getValue('rules');
      $gt_id     = $tour->getValue('game_type_id');
      $tour_type = $tour->getValue('tourney_type');
      $status    = $tour->getValue('status');
      $team_size = $tour->getValue('team_size');
      $timelimit = $tour->getValue('timelimit');
    }

  else
    {
      echo "<p><b>Create a Tournament:</b></p>";

      $t_name    = '' ;
      $rules     = '' ;
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
  echo "<td>Name:</td>";
  echo "<td><input type='text' name='tourney_name' maxlength='50' value='" . $t_name . "' size='50'></td>";
  echo "</tr>";

  echo "<tr><td>Game Type:</td>";
  echo "<td><select name='game_type_id'>";
 
  foreach (game_type::getAllGameTypes(array('name', SORT_ASC)) as $gt)
    {
      $sel = "";
      if ($gt->getValue('game_type_id') == $gt_id)
	{
	  $sel = "selected";
	}

      echo "<option value='" . $gt->getValue('game_type_id') . "'" . $sel . ">" . $gt->getValue('name') ;
    }

  echo "</select></td>";
  echo "</tr>";

  echo "<tr><td>Tournament Type:</td>";
  echo "<td><select name='tourney_type'>";

  foreach(tourney::getTourneyTypes(array('name', SORT_ASC)) as $key=>$value)
    {
      $sel = "";
      if ($value == $tour_type)
	{
	  $sel = "selected";
	}

      echo "<option value='" . $key . "'" . $sel . ">" . $value ;
    }

  echo "</select></td>";
  echo "</tr>";

  echo "<tr><td>Status:</td>";
  echo "<td><select name='status'>";

  foreach(tourney::getStatusTypes(array('name', SORT_ASC)) as $key=>$value)
    {
      $sel = "";
      if ($value == $status)
	{
	  $sel = "selected";
	}

      echo "<option value='" . $key . "'" . $sel . ">" . $value ;
    }

  echo "</select></td>";
  echo "</tr>";

  echo "<tr>";
  echo "<td>Team size:</td>";
  echo "<td><input type='text' name='team_size' maxlength='50' value='" . $team_size . "' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Timelimit:</td>";
  echo "<td><input type='text' name='timelimit' maxlength='50' value='" . $timelimit . "' size='50'></td>";
  echo "</tr>";

  echo "<tr>";
  echo "<td>Rules:</td>";

  echo "<td><textarea name='rules' cols='80' rows='8'>$rules</textarea></td>";
  echo "</tr>";

  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";
  echo "</form>" ;

  if ($p->isSuperAdmin())
  {
    include 'listTourneys.php';
  }
}
catch (Exception $e) {}
?>
