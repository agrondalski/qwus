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
      if ($t->hasAdmin($_REQUEST['player_id']))
	{
	  try
	    {
	      $t->removeAdmin($_REQUEST['player_id']);
	      $msg = "<br>Admin removed from tourney!<br>";
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
      if ($t->hasAdmin($_POST['player_id']) == false)
	{
	  try
	    {
	      $t->addAdmin($_POST['player_id']);
	      $msg = "<br>Admin added!<br>";
	    }
	  catch (Exception $e)
	    {
	      $msg = "<br>Error adding!<br>";
	    }
	}
      else
	{
	  $msg = "<br>Admin already added!<br>";
	} 
    }
  
  echo $msg;
  include 'assignAdminsToTourney.php';
}
catch (Exception $e) {}
?>
