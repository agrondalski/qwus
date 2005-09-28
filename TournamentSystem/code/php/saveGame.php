<?php

require 'includes.php';
require_once 'login.php' ;

try
{
  $tid = $_REQUEST['tourney_id'];
  $match_id = $_REQUEST['match_id'];
  $t = new tourney(array('tourney_id'=>$tid));
  $m = new match(array('match_id'=>$match_id));
  $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
  $t2 = new team(array('team_id'=>$m->getValue('team2_id')));
  try
  {
    $p = new player(array('player_id'=>$_SESSION['user_id'])) ;
  }
  catch(Exception $e) {}
  try
  {
    $tm = new team(array('team_id'=>$_SESSION['team_id'])) ;
  }
  catch(Exception $e) {}
  if (util::isNull($tm) && !$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }
  // Create new 
  $mode = $_REQUEST['mode'];

  if ($mode=="edit")
    {
      $game_id = $_POST['game_id'];
      $g = new game(array('game_id'=>$game_id));
      $g->update('map_id',$_POST['map_id']);
      $g->update('team1_score',$_POST['team1_score']);
      $g->update('team2_score',$_POST['team2_score']);
      $msg = "<br>Game updated!<br>";
    }

  elseif ($mode=="delete")
    {
      $game_id = $_REQUEST['game_id'];
	  $g = new game(array('game_id'=>$game_id));
      try
	  {
	    $g->delete();
	    $msg = "<br>Game deleted!<br>";
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
	    $m->addGame(array('map_id'=>$_POST['map_id'],
				'team1_score'=>$_POST['team1_score'],
				'team2_score'=>$_POST['team2_score']));

	    $msg = "<br>New game added!<br>";
	  }
      catch (Exception $e)
	  {
	    $msg = "<br>Error creating!<br>";
	  }
    }
  echo $msg ;
  include 'listGames.php';
}
catch (Exception $e) {
  include 'listGames.php';
}

?>
