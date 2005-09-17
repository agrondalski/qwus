<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && $p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  $division_id = $_POST['division_id'];

  // Create the new division 
  $mode = $_REQUEST['mode'];

  if ($mode=="edit")
    {
      try
	{
	  $schedule_id = $_POST['schedule_id'];
	  $ms = new match_schedule(array('schedule_id'=>$schedule_id));

	  $ms->update('name',$_POST['name']);
	  $ms->update('deadline',$_POST['deadline']);
	  $msg = "<br>Updated!<br>";
	}
      catch(Exception $e)
	{
	  $msg = "<br>Error!<br>";
	}
    }

  elseif ($mode=="delete")
    {
      $schedule_id = $_REQUEST['schedule_id'];
      $ms = new match_schedule(array('schedule_id'=>$schedule_id));

      try
	{
	  $ms->delete();
	  $msg = "<br>Deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }
  else
    {
      $ms = new match_schedule(array('division_id'=>$_POST['division_id'],
				     'name'=>$_POST['name'],
				     'deadline'=>$_POST['deadline']));

      $msg = "<br>Created!<br>";
    }

  echo $msg ;
  include 'listMatchSchedule.php';
}
catch (Exception $e) {}
?>
