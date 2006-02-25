<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = util::nvl($_POST['tourney_id'], $_REQUEST['tourney_id']) ;

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  $mode = $_REQUEST['mode'];

  if ($mode=="delete")
    {
      if ($t->hasTeam($_REQUEST['team_id']))
	{
	  try
	    {
	      $t->removeTeam($_REQUEST['team_id']);
	      $msg = "<br>Team removed from tourney!<br>";
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
      if ($t->hasTeam($_POST['team_id']) == false)
	{
	  try
	    {
	      $t->addTeam($_POST['team_id']);
	      $msg = "<br>Team added!<br>";
	    }
	  catch (Exception $e)
	    {
	      $msg = "<br>Error adding!<br>";
	    }
	}
      else
	{
	  $msg = "<br>Team already added!<br>";
	} 
    }
  
  echo $msg;
  include 'assignTeamsToTourney.php';
}
catch (Exception $e) {print $e;}
?>
