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
      if ($t->hasMap($_REQUEST['map_id']))
	{
	  try
	    {
	      $t->removeMap($_REQUEST['map_id']);
	      $msg = "<br>Map deleted from tourney!<br>";
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
      if ($t->hasMap($_POST['map_id']) == false)
	{
	  try
	    {
	      $t->addMap($_POST['map_id']);
	      $msg = "<br>Map added!<br>";
	    }
	  catch (Exception $e)
	    {
	      $msg = "<br>Error adding!<br>";
	    }
	}
      else
	{
	  $msg = "<br>Map already added!<br>";
	} 
    }
  
  echo $msg;
  include 'assignMapsToTourney.php';
}
catch (Exception $e) {}
?>
