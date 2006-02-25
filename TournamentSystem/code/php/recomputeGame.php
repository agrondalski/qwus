<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  /*
  if ($_REQUEST['all']==1)
    {
      if (util::isNull($_REQUEST['tourney_id']))
	{
	  $global_recompute = true ;
	}
      else
	{
	  $tourney_recompute = true ;
	}
    }

  if (!$global_recompute)
    {
      $tid = $_REQUEST['tourney_id'];
      $t = new tourney(array('tourney_id'=>$tid));
    }
  */

  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid));

  try
  {
    $p = new player(array('player_id'=>$_SESSION['user_id']));
  }
  catch(Exception $e) {}
  
  if (!$p->isSuperAdmin() && (util::isNull($t) || !$p->isTourneyAdmin($t->getValue('tourney_id'))))
  {
      util::throwException('not authorized') ;
  }

  /*  
  if ($global_recompute)
    {
      $tours = tourney::getAllTourneys() ;
      $max_tid = 0 ;

      foreach($$tours as $tour)
	{
	  if ($tour->getValue('tourney_id')>$max_tid)
	    {
	      $max_tid = $tour->getValue('tourney_id') ;
	    }
	}

      $psss_thru = '\\max_tourney_id=' . $max_tid . '\\' ;
    }
  */


  $division_id = $_REQUEST['division_id'];
  $div = new division(array('division_id'=>$division_id));

  $match_id = $_REQUEST['match_id'];
  $m = new match(array('match_id'=>$match_id));

  $game_id =  $_REQUEST['game_id'];
  $g = new game(array('game_id'=>$game_id));

  $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
  $t2 = new team(array('team_id'=>$m->getValue('team2_id')));

  $fail = false ;
  $files = $g->getFiles() ;
  if (array_key_exists(util::MVD_DEMO, $files))
    {
      $mvd_file   = basename($files[util::MVD_DEMO]->getValue('url')) ;
      $uploadfile = util::UPLOAD_DIR . $mvd_file ;

      if (!copy($t->getTourneyRoot() . util::SLASH . $g->getFileDirectory() . util::SLASH . $mvd_file, $uploadfile))
	{
	  $fail = true ;
	}
    }
  else
    {
      $fail = true ;
    }

  if (!$fail && array_key_exists(util::SCREENSHOT, $files))
    {
      $ss_file   = basename($files[util::SCREENSHOT]->getValue('url')) ;
      $ss_uploadfile = util::UPLOAD_DIR . $ss_file ;

      if (!copy($t->getTourneyRoot() . util::SLASH . $g->getFileDirectory() . util::SLASH . $ss_file, $ss_uploadfile))
	{
	  $fail = true ;
	}
    }

  if (!$fail)
    {
      $approved = $m->getValue('approved') ;
      $g->deleteAll() ;

      $pass_thru = '\\\\' . $tid . '\\\\' . $division_id . '\\\\' . $match_id . '\\\\' . $approved . '\\\\' . $ss_uploadfile . '\\\\' . $pass_thru;

      // Post to mvdStats.pl page
      echo "<form action='./perl/mvdStats.pl' method=post>";
      echo "<table border=0 cellpadding=4 cellspacing=0>";
      echo "<tr><td><b>Recompute Game</b></td>";
      echo "<input type='hidden' name='filename' value ='$uploadfile'>";
      echo "<input type='hidden' name='team1' value='",$t1->getValue('name_abbr'),"'>";
      echo "<input type='hidden' name='team2' value='",$t2->getValue('name_abbr'),"'>";
      echo "<input type='hidden' name='pass_thru' value ='$pass_thru'>";
      echo "<td><input type='submit' value='Submit' name='B1' class='button'></td>";
      echo "<td>Please be patient, this process could take a few seconds.</td></tr>";
      echo "</table></form>";
    }
  else
    {
      echo "No mvd available.<br>" ;
      echo "<a href='?a=manageGame&amp;tourney_id=$tid&amp;match_id=$match_id'>Manage Match</a>" ;
    }
}
catch(Exception $e){}
