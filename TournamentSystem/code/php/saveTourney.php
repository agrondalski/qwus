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

  if ($mode=="edit")
    {
      $tourney_id = $_POST['tourney_id'];
      $tour = new tourney(array('tourney_id'=>$tourney_id));

      try
	{
	  $tour->update('name', $_POST['tourney_name']);
	  $tour->update('rules', $_POST['rules']);
	  $tour->update('tourney_type', $_POST['tourney_type']);
	  $tour->update('game_type_id', $_POST['game_type_id']);
	  $tour->update('status', $_POST['status']);
	  $tour->update('team_size', $_POST['team_size']);
	  $tour->update('timelimit', $_POST['timelimit']);
      
	  $msg = "<br>Tournament updated!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error updating!<br>";
	}
    }

  elseif ($mode=="delete")
    {
      $tourney_id = $_REQUEST['tourney_id'];
      $tour = new tourney(array('tourney_id'=>$tourney_id));

      try
	{
	  $tour->delete();
	  $msg = "<br>Tournament deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }

  else
    {
      try
	{
	  $tour = new tourney(array('name'         => $_POST['tourney_name'],
				    'game_type_id' => $_POST['game_type_id'],
				    'rules'        => $_POST['rules'],
				    'tourney_type' => $_POST['tourney_type'],
				    'status'       => $_POST['status'],
				    'team_size'    => $_POST['team_size'],
				    'timelimit'    => $_POST['timelimit'])) ;
	  
	  $msg = "<br>New Tournament created!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Unable to create new Tournament!<br>";
	}
    }

  echo $msg;
  include 'listTourneys.php';
}
catch (Exception $e) {}
?>
