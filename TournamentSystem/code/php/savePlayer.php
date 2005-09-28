<?php

require 'includes.php';
require_once 'login.php' ;

try
{
  try
  {
      $p = new player(array('player_id'=>$_SESSION['user_id']));
  }
  catch(Exception $e) {}

  $mode = $_REQUEST['mode'];

  if (!util::isLoggedInAsTeam() && !$p->isSuperAdmin() && ($_SESSION[user_id] != $_REQUEST['player_id'] || $mode!='edit'))
    {
      util::throwException('not authorized') ;
    }

  if ($mode=="edit" && !util::isLoggedInAsTeam())
    {
      $player_id = $_POST['player_id'];
      $p = new player(array('player_id'=>$player_id));
      $p->update('name',$_POST['name']);
      $p->update('location_id',$_POST['location_id']);

      if ($_POST['password'] != "")
	{
	  // only update the pw if a new one is supplied
	  $p->update('password',$_POST['password']);
	}

      if ($p->isSuperAdmin())
	{
	  $hascolumn = util::choose(($_POST['hascolumn'] == "1"), 1, 0) ;
	  $p->update('hasColumn', $hascolumn);

	  $p->update('superAdmin',$_POST['superadmin']);
	}

 
      $msg = "<br>Player updated!<br>";
    }

  elseif ($mode=="delete" && !util::isLoggedInAsTeam())
    {
      $player_id = $_REQUEST['player_id'];
      $p = new player(array('player_id'=>$player_id));
      try
	{
	  $p->delete();
	  $msg = "<br>Player deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }

  else
    {
      if ($_POST['hascolumn'] == "1")
	{
	  $hascolumn = "1";
	}
      else
	{
	  $hascolumn = "0";
	}

      // All players dont need a password
      $pw = $_POST['password'];

      try
	{
	  $p = new player(array('name'=>$_POST['name'],
				'superAdmin'=>$_POST['superadmin'],
				'location_id'=>$_POST['location_id'],
				'password'=>$pw,
				'hasColumn'=>$hascolumn));
	
	  $msg = "<br>New player created!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error creating player!<br>" ;
	}
    }

  echo $msg;
  echo "<p><a href='?a=managePlayer'>Create Player</a><p>";
  
  if (!util::isLoggedInAsTeam()) {
    include 'listPlayers.php';
  }
}
catch (Exception $e) {}
?>
