<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = util::nvl($_REQUEST['tourney_id'], $_REQUEST['tourney_id']) ;

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  //tourney_id,division_id,team1_id,team2_id,deadline,week_name
  $division_id = util::nvl($_POST['division_id'], $_REQUEST['division_id']) ;
  $div = new division(array('division_id'=>$division_id));

  $mode = $_REQUEST['mode'];

  if ($mode=="edit")
    {
      try
	{
	  $match_id = $_POST['match_id'];
	  $m = new match(array('match_id'=>$match_id));

	  $m->update('approved',$_POST['approved']);
	  $m->update('match_date',$_POST['match_date']);

	  $msg = "<br>Match updated!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error modifying!<br>";
	}
    }

  elseif ($mode=="delete")
    {
      $match_id = $_REQUEST['match_id'];
      $m = new match(array('match_id'=>$match_id));

      try
	{
	  $m->delete();
	  $msg = "<br>Match deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }

  // add new
  else
    {
      if (($_POST['team1_id'] == $_POST['team2_id']) or ($_POST['team1_id']=="") or ($_POST['team2_id']==""))
	{
	  $msg = "<br>Error adding match!<br>";	
	}
      else
	{
	  try
	    {
	      $ms = new match_schedule(array('schedule_id'=>$_POST['schedule_id'])) ;

	      $ms->addMatch(array('division_id'=>$division_id,
				  'team1_id'=>$_POST['team1_id'],
				  'team2_id'=>$_POST['team2_id'])) ;

	      $msg = "<br>Match added!<br>";
	    }
	  catch (Exception $e)
	    {
	      $msg = "<br>Error creating match!<br>";
	    }
	}
    }

  echo $msg;
  include 'manageSchedule.php';
}
catch (Exception $e) {}
?>
