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

  if ($_POST['tourney_type']==1)
    {
      $ttype = 'LEAGUE' ;
    }
  elseif ($_POST['tourney_type']==2)
    {
      $ttype = 'TOURNAMENT' ;
    }
  elseif ($_POST['tourney_type']==3)
    {
      $ttype = 'LADDER' ;
    }
  print $ttype ;
  if ($mode=="edit")
    {
      $tourney_id = $_POST['tourney_id'];
      $tour = new tourney(array('tourney_id'=>$tourney_id));

      try
	{
	  $tour->update('name', $_POST['tourney_name']);
	  $tour->update('tourney_type', $ttype) ;
	  $tour->update('game_type_id', $_POST['game_type_id']);
	  $tour->update('signup_start', $_POST['signup_start']);
	  $tour->update('signup_end', $_POST['signup_end']);
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
				    'tourney_type' => $ttype,
				    'signup_start' => $_POST['signup_start'],
				    'signup_end'   => $_POST['signup_end'],
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
catch (Exception $e) {print $e;}
?>
