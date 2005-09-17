<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $tid = $_POST['tourney_id'];

  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && $p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  $division_id = util::nvl($_POST['division_id'], $_REQUEST['division_id']) ;
  $div = new division(array('division_id'=>$division_id));

  $team_id = util::nvl($_POST['team_id'], $_REQUEST['team_id']) ;
  $tm  = new team(array('team_id'=>$team_id));

  $mode = $_REQUEST['mode'];

  if ($mode=="delete")
    {
      if ($div->hasTeam($team_id) == true)
	{
	  try
	    {
	      $div->removeTeam($team_id);
	      $msg = "<br>Team deleted from div!<br>";
	    }
	  catch (Exception $e)
	    {
	      $msg = "<br>Error deleting!<br>";
	    }
	}
      else
	{
	  $msg = "<br>Error!<br>";
	} 
      
    }

  // add new
  else
    {
      if ($div->hasTeam($team_id) == false)
	{
	  try
	    {
	      $div->addTeam($team_id);
	      $msg = "<br>Team added!<br>";
	    }
	  catch (Exception $e)
	    {
	      $msg = "<br>Error adding!<br>";
	    }
	}
      else
	{
	  $msg = "<br>Error!<br>";
	} 
    }

  echo $msg;
  include 'assignTeamsToDiv.php';
}
catch (Exception $e) {}
?>
