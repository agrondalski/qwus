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
      $team_id = $_POST['team_id'];
      $tm = new team(array('team_id'=>$team_id));
      $tm->update('name',$_POST['name']);
      $tm->update('name_abbr',$_POST['name_abbr']);
      $tm->update('email',$_POST['email']);
      $tm->update('irc_channel',$_POST['irc_channel']);
      $tm->update('location_id',$_POST['location_id']);

      if ($_POST['password'] != "")
	{
	  // only update the pw if a new one is supplied
	  $tm->update('password',$_POST['password']);
	}

      if ($_POST['approved'] == "1")
	{
	  $appr = "1";
	}
      else
	{
	  $appr = "0";
	}
      $tm->update('approved',$appr);
 
      $msg = "<br>Team updated!<br>";
    }

  elseif ($mode=="delete")
    {
      $team_id = $_REQUEST['team_id'];
      $tm = new team(array('team_id'=>$team_id));

      try
	{
	  $tm->delete();
	  $msg = "<br>Team deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }
  
  else
    {
      if ($_POST['approved'] == "1")
	{
	  $appr = "1";
	}
      else
	{
	  $appr = "0";
	}

      // Make sure this team has a password
      $pw = $_POST['password'];
      if ($pw == "")
	{
	  $pw = "dumB3as5!";
	}

      $tm = new team(array('name'=>$_POST['name'],
			   'name_abbr'=>$_POST['name_abbr'],
			   'email'=>$_POST['email'],
			   'irc_channel'=>$_POST['irc_channel'],
			   'location_id'=>$_POST['location_id'],
			   'password'=>$pw,
			   'approved'=>$appr));
	
      $msg = "<br>New team created!<br>";
    }
  
  echo $msg;
  include 'listTeams.php';
}
catch (Exception $e) {}
?>
