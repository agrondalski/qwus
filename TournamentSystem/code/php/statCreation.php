<?php

$vars = explode('\\\\', $_REQUEST['pass_thru']) ;
if (count($vars)<9)
{
  util::throwException('wrong number of parameters passed in') ;
}

$tid          = $vars[0] ;
$division_id  = $vars[1] ;
$match_id     = $vars[2] ;
$approved     = $vars[3] ;
$match_date   = $vars[4] ;
$ss_url       = $vars[5] ;
$max_tid      = $vars[6] ;
$max_gid      = $vars[7] ;
$m_recompute  = $vars[8] ;
$_REQUEST['tourney_id'] = $tid ;

require 'includes.php';
require_once 'login.php';

try
{
  $m = new match(array('match_id'=>$match_id)) ;
  $error = false ;

  if ($approved == "1")
    {
      $m->update('approved',"1");
    }
  
  try
    {
      $m->addGameWithStats(array('filename'=>$_REQUEST['filename'],
				 'screenshot_url'=>$ss_url,
				 'map'=>$_REQUEST['map'],
				 'teamStats'=>$_REQUEST['teamStats'],
				 'team1'=>$_REQUEST['team1'],
				 'team2'=>$_REQUEST['team2'],
				 'team_score_graph_small'=>$_REQUEST['team_score_graph_small'],
				 'team_score_graph_large'=>$_REQUEST['team_score_graph_large'],
				 'player_score_graph'=>$_REQUEST['player_score_graph'],
				 'playerFields'=>$_REQUEST['playerFields'],
				 'PlayerStats'=>$_REQUEST['PlayerStats'],
				 'team1players'=>$_REQUEST['team1players'],
				 'team2players'=>$_REQUEST['team2players']));

      if (!util::isNull($match_date))
	{
	  $m->update('match_date', $match_date) ;
	}

      echo "<b>Success!</b><br><br>";

      if (!util::isNull($_REQUEST['team_score_graph_small']))
	{
	  $tmp_dir = dirname($_REQUEST['team_score_graph_small']) ;
	  util::delete_files($tmp_dir) ;
	}

      if ($m_recompute!=1 && m_recompute!=2)
	{
	  echo "Game was added, click this link to add another game.";
	}
    }
  catch (Exception $e)
    {
      echo 'Unable to add game to match #' . $match_id . ' using demo ' . $_REQUEST['filename'] . '.<BR>' ;
      $error = true ;
    }

  if ($m_recompute!=1 && m_recompute!=2)
    {
      echo "<br><br><a href='?a=reportMatch&amp;tourney_id=$tid&amp;division_id=$division_id&amp;match_id=$match_id&amp;approved=$approved&amp;approved_step=1'>Report Match Page</a>";
      return ;
    }

  $done   = true ;
  $next_t = new tourney(array('tourney_id'=>$tid)) ;
  $games  = $next_t->getGames(array('game_id', SORT_ASC)) ;

  $next_game  = $games[0] ;

  if ((util::isNull($next_game) || $next_game->getValue('game_id') > $max_gid))
    {
      $tours = tourney::getAllTourneys(array('tourney_id', SORT_ASC)) ;
      $cnt = 0 ;

      while($done && $cnt<(count($tours)))
	{
	  $next_t = $tours[$cnt++] ;

	  if ($next_t->getValue('tourney_id')<=$max_tid && $next_t->getValue('tourney_id') > $tid)
	    {
	      $games = $next_t->getGames(array('game_id', SORT_ASC)) ;
	      if (count($games)!=0)
		{
		  $done = false ;
		  $next_game = $games[0] ;
		  $max_gid = $games[count($games)-1]->getValue('game_id') ;
		}
	    }
	}
    }
  else
    {
      $done = false ;
    }

  if (!$done)
    {
      $next_match = $next_game->getMatch() ;
      $next_d     = $next_match->getDivision() ;

      // Post to mvdStats.pl page
      echo "<form action='?a=recomputeGame' method=post name=stats>";
      echo "<table border=0 cellpadding=4 cellspacing=0>";
      echo "<input type='hidden' name='tourney_id' value='" . $next_t->getValue('tourney_id') . "'>";
      echo "<input type='hidden' name='division_id' value='" . $next_d->getValue('division_id') . "'>";
      echo "<input type='hidden' name='match_id' value='" . $next_match->getValue('match_id') . "'>";
      echo "<input type='hidden' name='game_id' value='" . $next_game->getValue('game_id') . "'>";
      echo "<input type='hidden' name='max_tid' value='" . $max_tid . "'>";
      echo "<input type='hidden' name='max_gid' value='" . $max_gid . "'>";
      echo "<input type='hidden' name='m_recompute' value='" . $m_recompute . "'>";
      echo "<td><input type='submit' value='Continue' name='B1' class='button'></td>";
      echo "</table></form>";

      if (!$error)
	{
	  echo "<script>\n";
	  echo "document.stats.submit();\n";
	  echo "</script>\n";
	}
    }
  elseif ($m_recompute==1 || $m_recompute=2)
    {
      echo "<BR><b>All games successfully recomputed.!</b><br><br>";
    }
}
catch (Exception $e) {}

?>
