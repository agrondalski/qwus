<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $m_recompute = 0 ;
  $max_tid = $_REQUEST['max_gid'] ;
  $max_gid = $_REQUEST['max_gid'] ;
  $done = false ;

  if ($_REQUEST['all']==1)
    {
      $tourney_recompute = true ;
      $m_recompute = 1 ;

      if (util::isNull($_REQUEST['tourney_id']))
	{
	  $global_recompute = true ;
	}
      else
	{
	  $global_recompute = false ;
	}
    }
  else
    {
      $tourney_recompute = false ;
      $global_recompute = false ;
    }

  if (!$global_recompute)
    {
      $tid = $_REQUEST['tourney_id'];
      $t = new tourney(array('tourney_id'=>$tid));
    }

  try
  {
    $p = new player(array('player_id'=>$_SESSION['user_id']));
  }
  catch(Exception $e) {}
  
  if (!$p->isSuperAdmin() && (util::isNull($t) || !$p->isTourneyAdmin($t->getValue('tourney_id'))))
  {
      util::throwException('not authorized') ;
  }

  if ($global_recompute)
    {
      $tours = tourney::getAllTourneys(array('tourney_id', SORT_ASC)) ;

      if (count($tours)==0)
	{
	  print 'There are no games to recompute.' ;
	  return ;
	}
      else
	{
	  $t = $tours[0] ;
	  $tid = $t->getValue('tourney_id') ;

	  $max_tid = $tours[count($tours)-1]->getValue('tourney_id') ;
	  $pass_thru = $max_tid . '\\\\' ;
	}
    }
  elseif (util::isNull($max_tid))
    {
      $pass_thru = $t->getValue('tourney_id') . '\\\\' ;
    }
  else
    {
      $pass_thru = $max_tid . '\\\\' ;
    }

  if ($tourney_recompute)
    {
      $games = $t->getGames(array('game_id', SORT_ASC)) ;

      if (count($games)==0)
	{
	  if (!$global_recompute)
	    {
	      $done = true ;
	    }
	  else
	    {
	      $cnt=1;
	      while(count($games)==0 && $cnt<count($tours))
		{
		  $t = $tours[$cnt++] ;
		  $games = $t->getGames(array('game_id', SORT_ASC)) ;
		}
	      
	      if ($cnt==count($tours))
		{
		  $done = true ;
		}
	    }
	}

      if (!$done)
	{
	  $max_gid = $games[count($games)-1]->getValue('game_id') ;

	  $g = $games[0] ;
	  $game_id = $g->getValue('game_id') ;

	  $m = $g->getMatch() ;
	  $match_id = $m->getValue('match_id') ;

	  $div = $m->getDivision() ;
	  $division_id = $div->getValue('division_id') ;

	  $teams = $m->getTeams() ;
	  $t1 = $teams[0] ;
	  $t2 = $teams[1] ;

	  $pass_thru = $pass_thru . $max_gid . '\\\\' ;
	}
    }
  else
    {
      $division_id = $_REQUEST['division_id'];
      $div = new division(array('division_id'=>$division_id));

      $match_id = $_REQUEST['match_id'];
      $m = new match(array('match_id'=>$match_id));

      $game_id = $_REQUEST['game_id'] ;
      $g = new game(array('game_id'=>$game_id));

      $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
      $t2 = new team(array('team_id'=>$m->getValue('team2_id')));

      if (util::isNull($max_gid))
	{
	  $pass_thru = $pass_thru . $g->getValue('game_id') . '\\\\' ;
	}
      else
	{
	  $pass_thru = $pass_thru . $max_gid . '\\\\' ;
	  $m_recompute = 1 ;
	}
    }

  if ($done)
    {
      print 'There are no games to recompute.' ;
      return ;
    }

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

      $pass_thru = $tid . '\\\\' . $division_id . '\\\\' . $match_id . '\\\\' . $approved . '\\\\' . $ss_uploadfile . '\\\\' . $pass_thru . $m_recompute ;

      // Post to mvdStats.pl page
      echo "<form action='./perl/mvdStats.pl' method=post name=stats>";
      echo "<table border=0 cellpadding=4 cellspacing=0>";
      echo "<tr><td><b>Recompute Game</b></td>";
      echo "<input type='hidden' name='filename' value ='$uploadfile'>";
      echo "<input type='hidden' name='team1' value='",$t1->getValue('name_abbr'),"'>";
      echo "<input type='hidden' name='team2' value='",$t2->getValue('name_abbr'),"'>";
      echo "<input type='hidden' name='pass_thru' value ='$pass_thru'>";
      echo "<td><input type='submit' value='Submit' name='B1' class='button'></td>";
      echo "<td>Please be patient, this process could take a few seconds.</td></tr>";
      echo "</table></form>";

      echo "<script>\n";
      echo "document.stats.submit();\n";
      echo "</script>\n";
    }
  else
    {
      echo "Unable to copy file.<br>" ;
      echo "<a href='?a=manageGame&amp;tourney_id=$tid&amp;match_id=$match_id'>Manage Match</a>" ;
    }
}
catch(Exception $e){}
