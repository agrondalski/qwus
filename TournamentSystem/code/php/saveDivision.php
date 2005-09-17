<?php

require 'includes.php';
require_once 'login.php' ;

try
{
  $tid = $_POST['tourney_id'];

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && $p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  // Create the new division 
  $mode = $_REQUEST['mode'];

  if ($mode=="edit")
    {
      $did = $_POST['did'];
      $div = new division(array('division_id'=>$did));
      
      $div->update('name',$_POST['name']);

      $div->update('num_games',$_POST['num_games']);
      $div->update('playoff_spots',$_POST['playoff_spots']);
      $div->update('elim_losses',$_POST['elim_losses']);
      $msg = "<br>Division updated!<br>";
    }

  elseif ($mode=="delete")
    {
      $did = $_REQUEST['did'];
      $div = new division(array('division_id'=>$did));
      try
	{
	  $div->delete();
	  $msg = "<br>Division deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }

  else
    {
      $div = new division(array('tourney_id'=>$tid,
				'name'=>$_POST['name'],
				'num_games'=>$_POST['num_games'],
				'playoff_spots'=>$_POST['playoff_spots'],
				'elim_losses'=>$_POST['elim_losses']));
      $msg = "<br>New division created!<br>";
    }

  include 'listDivisions.php';
  echo $msg ;
}
catch (Exception $e) {}
?>
