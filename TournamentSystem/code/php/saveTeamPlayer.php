<?php

require 'includes.php';
require_once 'login.php' ;

try
{

  $tid       = util::nvl($_POST['tourney_id'], $_REQUEST['tourney_id']) ;
  $team_id   = util::nvl($_POST['team_id'], $_REQUEST['team_id']) ;
  $player_id = util::nvl($_POST['player_id'], $_REQUEST['player_id']) ;

  $t = new tourney(array('tourney_id'=>$tid)) ;
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && $p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  $tm = new team(array('team_id'=>$team_id));
  $p  = new player(array('player_id'=>$player_id));

  $mode = $_REQUEST['mode'];

  if ($_POST['isteamleader'] == "1")
    {
      $itl = $_POST['isteamleader'];
    }
  else
    {
      $itl = "0";
    }

  if ($mode=="delete")
    {
      if ($tm->hasPlayer($tid,$player_id) == true)
	{
	  try
	    {
	      $tm->removePlayer($tid,$player_id);
	      $msg = "<br>Player deleted from team!<br>";
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
      if ($tm->hasPlayer($tid, $player_id) == false)
	{
	  try
	    {
	      $tm->addPlayer($tid, $player_id, $itl);
	      $msg = "<br>Player added!<br>";
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
  include 'assignPlayersToTeam.php';
}
catch (Exception $e) {}
?>
