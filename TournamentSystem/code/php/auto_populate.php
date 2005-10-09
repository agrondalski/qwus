<?php
require 'includes.php' ;
?>

<?php

// Run populateLocations.sql and populateGameTypes.sql first

define("PREFIX", "AUTO-") ;

function make_seed()
{
  list($usec, $sec) = explode(' ', microtime());
  return (float) $sec + ((float) $usec * 100000);
}

function generate_string($len)
{
  for ($i-0; $i<$len; $i++)
    {
      srand(make_seed());
      $s .= chr(rand(32, 120)) ;
    }
  
  return $s ;
}

function generate_integer($val)
{
  srand(make_seed());
  return rand(0, $val-1) ;
}

function generate_boolean()
{
  if (generate_integer(2)==1)
    {
      return true ;
    }
  else
    {
      return false ;
    }
}

function auto_populate($a)
{
  $c_tour   = $a['tourney'] ;
  $c_play   = $a['players'] ;
  $c_div    = $a['division'] ;
  $c_team   = $a['team'] ;
  $c_news   = $a['news'] ;

  $gta = game_type::getAllGameTypes() ;
  foreach ($gta as $g)
    {
      $gt[] = $g->getValue("game_type_id") ;
    }

  $loca = location::getAllLocations() ;
  foreach ($loca as $l)
    {
      $loc[] = $l->getValue("location_id") ;
    }

  /*
  $mapsa = map::getAllMaps() ;
  foreach ($mapsa as $m)
    {
      $maps[] = $m->getValue("map_id") ;
    }
  */

  for ($i=0; $i<$c_tour; $i++)
    {
      //$v1 = $gt[generate_integer(count($gt))] ;
      $v1 = 1 ;
      $v2 = 'naql-' . generate_string(5) ;
      $v3 = tourney::TYPE_LEAGUE ;

      $sd = generate_integer(100) ;

      $v4 = generate_integer(20) ;
      $v5 = generate_integer(20) ;

      $n = new tourney(array('game_type_id'=>$v1, 'name'=>$v2, 'tourney_type'=>$v3, 'team_size'=>$v4, 'timelimit'=>$v5)) ;

      $tour[] = $n->getValue("tourney_id") ;
    }

  for ($i=0; $i<$c_play; $i++)
    {
      $v1 = 'player-' . generate_string(10) ;
      $v2 = generate_boolean() ;
      $v3 = $loc[generate_integer(count($loc))] ;
      //$v4 = PREFIX . generate_string(10) ; 
      $v4 = 'x' ;

      try
	{
	  $n = new player(array('name'=>$v1, 'superAdmin'=>$v2, 'location_id'=>$v3, 'password'=>$v4)) ;
	  $play[] = $n->getValue("player_id") ;
	}
      catch(Exception $e) {}
    }

  for ($i=0; $i<$c_div; $i++)
    {
      $v1 = $tour[generate_integer(count($tour))] ; 
      $v2 = 'division-' . generate_string(5) ; 
      $v3 = generate_integer(10) ;
      $v4 = generate_integer(8) + 6;
      $v5 = generate_integer(10) ;
      $v6 = generate_integer(10) ;
      $n = new division(array('tourney_id'=>$v1, 'name'=>$v2, 'num_games'=>$v4, 'playoff_spots'=>$v5, 'elim_losses'=>$v6)) ;

      $div[] = $n->getValue("division_id") ;
    }

  for ($i=0; $i<$c_team; $i++)
    {
      $v1 = 'team-' . generate_string(10) ; 
      $v2 = 'email@-' . generate_string(10) ; 
      $v3 = '#chan_' . generate_string(10) ; 
      $v4 = $loc[generate_integer(count($loc))] ;
      //$v5 = generate_string(10) ;
      $v5 = 'x' ;
      $v6 = true ;
      $v7 = generate_string(4) ;

      try
	{
	  $n = new team(array('name'=>$v1, 'email'=>$v2, 'irc_channel'=>$v3, 'location_id'=>$v4, 'password'=>$v5, 'approved'=>$v6, 'name_abbr'=>$v7)) ;
	}
      catch(Exception $e) {}
      
      $team[] = $n->getValue("team_id") ;
    }

  for ($i=0; $i<$c_news; $i++)
    {
      $v1 = $play[generate_integer(count($play))] ;

      if (generate_boolean())
	{
	  $v2 = news::TYPE_TOURNEY ;
	  $v3 = $tour[generate_integer(count($tour))] ;
	}
      else
	{
	  $v2 = news::TYPE_NEWS ;
	  $v3 = null ;
	}

      $v4 = 'subject-' . generate_string(25) ;

      $c = $c + generate_integer(25) ;
      $sd = generate_integer(100) ;
      $v5 = date('Y-m-d', time()-(60*60*24*($sd+$c))) ;

      $v6 = generate_string(25) ;
      $n = new news(array('writer_id'=>$v1, 'news_type'=> $v2, 'id'=>$v3, 'subject'=>$v4, 'news_date'=>$v5, 'text'=>$v6)) ;

      //$news[] = $n->getValue("news_id") ;
    }

  for ($i=0; $i<count($tour); $i++)
    {
      $t = new tourney(array('tourney_id'=>$tour[$i])) ;

      $mapsa = $t->getUnassignedMaps() ;
      foreach ($mapsa as $m)
	{
	  $maps[] = $m->getValue("map_id") ;
	}

      $c = generate_integer(3) + 2 ;
      for ($j=0; $j<$c; $j++)
	{
	  $m = new map(array('map_id'=>$maps[generate_integer(count($maps))])) ;

	  if (! $t->hasMap($m->getValue("map_id")))
	  {
	    $t->addMap($m->getValue("map_id")) ;
	  }
	}

      $c = generate_integer(2) + 2 ;
      for ($j=0; $j<$c; $j++)
	{
	  $p = new player(array('player_id'=>$play[generate_integer(count($play))])) ;
	  $v1 = generate_boolean() ;
	  
	  if (! $t->hasAdmin($p->getValue("player_id")))
	    {
	      $t->addAdmin($p->getValue("player_id")) ;
	    }
	}

      foreach($t->getDivisions() as $d)
	{
	  $c = generate_integer(2) + 3 ;
	  for ($j=0; $j<$c; $j++)
	    {
	      $c2=0 ;
	      do
		{
		  $te = new team(array('team_id'=>$team[generate_integer(count($team))])) ;
		} while ($t->hasTeam($te->getValue("team_id")) && $c2++<5) ;

	      if ($c2<5)
		{
		  $t->addTeam($te->getValue("team_id")) ;
		  $t->assignTeamToDiv($te->getValue('team_id'), $d->getValue('division_id')) ;

		  $pc = generate_integer(6) + 4 ;
		  for ($l=0; $l<$pc; $l++)
		    {
		      $c2 = 0 ;
		      do
			{
			  $p = new player(array('player_id'=>$play[generate_integer(count($play))])) ;
			} while ($t->hasPlayer($p->getValue("player_id")) and $c2++<10) ;
		      
		      if ($c2<10)
			{
			  $v1 = generate_boolean() ;
			  $te->addPlayer($t->getValue("tourney_id"), $p->getValue("player_id"), $v1) ;
			}
		    }
		}
	    }

	  $d->createSchedule(generate_integer(3)+5) ;
	  $matches = $d->getMatches() ;

	  foreach($matches as $m)
	    {
	      $team1_wins = 0 ;
	      $team2_wins = 0 ;
	      $winning_team = 0 ;

	      for ($j=0; $j<3; $j++)
		{
		  $v1 = $m->getValue('match_id') ;
		  $v2 = $maps[generate_integer(count($maps))] ;

		  do
		    {
		      $v3 = generate_integer(400) ;
		      $v4 = generate_integer(400) ;
		    } while ($v3 == $v4) ;

		  $v5 = 'http://' . generate_string(25) ;
		  $v6 = 'http://' . generate_string(25) ;
		  $g = new game(array('match_id'=>$v1, 'map_id'=>$v2, 'team1_score'=>$v3, 'team2_score'=>$v4)) ;

		  for ($k=0; $k<2; $k++)
		    {
		      if ($k==0)
			{
			  $team_s = new team(array('team_id'=>$m->getValue('team1_id'))) ;
			  $score = $g->getValue('team1_score') ;
			}
		      else
			{
			  $team_s = new team(array('team_id'=>$m->getValue('team2_id'))) ;
			  $score = $g->getValue('team2_score') ;
			}

		      $players = $team_s->getPlayers($t->getValue('tourney_id')) ;


		      $c2 = min(generate_integer(2)+3, count($players)) ;
		      for ($l=0; $l<$c2; $l++)
			{
			  do
			    {
			      $p = $players[generate_integer(count($players))] ;
			    } while (stats::hasStatsEntry($p->getValue('player_id'), $g->getValue('game_id'), 'SCORE')) ;

			  if ($l==($c2-1))
			    {
			      $player_score = $score ;
			    }
			  else
			    {
			      $player_score = generate_integer($score) ;
			    }
			  $score -= $player_score ;

			  $v1 = $p->getValue('player_id') ;
			  $v2 = $g->getValue('game_id') ;
			  $v3 = util::SCORE ;
			  $v4 = $player_score ;

			  $n = new stats(array('player_id'=>$v1, 'game_id'=>$v2, 'stat_name'=>$v3, 'team_id'=>$team_s->getValue('team_id'), 'value'=>$v4)) ;
			}
		    }
		
		  if ($g->getValue('team1_score') > $g->getValue('team2_score'))
		    {
		      if (++$team1_wins == 2)
			{
			  $winning_team = $m->getValue('team1_id') ;
			}
		    }
		  else
		    {
		      if (++$team2_wins == 2)
			{
			  $winning_team = $m->getValue('team2_id') ;
			}
		    }
		  
		  if ($winning_team>0)
		    {
		      $sd = generate_integer(100) ;
		      $v1 = date('Y-m-d', time()+(60*60*24*($sd))) ;

		      $sql_str = sprintf("update match_table
                                          set approved=true, winning_team_id=%d, match_date='%s'
                                          where match_id=%d", $winning_team, $v1, $m->getValue('match_id')) ;
		      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
		      break ;
		    }
		}

	      $c = generate_integer(3)+2 ;
		  
	      for ($l=0; $l<$c; $l++)
		{
		  $v1 = 'player-' . generate_string(5) ;
		  $v2 = $m->getValue('match_id') ;
		  $v3 = generate_integer(1000) . "." . generate_integer(1000) . "." . generate_integer(1000) . "." . generate_integer(1000) ;
		  $v4 = 'comment-' . generate_string(100) ;
		  $v5 = date('Y-m-d', time()+(60*60*24*($sd+generate_integer(100)+1))) ;
		  $v6 = date('H:i:s', time()+(60*(generate_integer(1440)))) ;
		  $n = new comment(array('name'=>$v1, 'comment_type'=>comment::TYPE_MATCH, 'id'=>$v2, 'player_ip'=>$v3, 'comment_text'=>$v4, 'comment_date'=>$v5, 'comment_time'=>$v6)) ;
		  //$comm[] = $n->getValue("comment_id") ;
		}
	    }
	}
    }

  $sql_str = sprintf("delete from team where team_id not in(select team_id from tourney_info)") ;
  $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
}

?>

<?php

$a = array('tourney'=>1,
	   'players'=>100,
	   'division'=>4,
	   'team'=>30,
	   'news'=>10) ;

$ts = microtime(true) ;
try
{
  auto_populate($a) ;
}
catch(Exception $e)
{
  echo $e;
}
$te = microtime(true) ;

try
{
  $p = new player(array('name'=>'x')) ;
}
catch (Exception $e)
{
  $p = new player(array('name'=>'x', 'superAdmin'=>true, 'location_id'=>1, 'password'=>'x', 'hasColumn'=>true)) ;
}

try
{
  $v1 = $p->getValue("player_id") ;
  
  for ($i=0; $i<5; $i++)
    {
      $v2 = 'subject-' . generate_string(25) ;
      
      $c = $c + generate_integer(25) ;
      $v3 = date('Y-m-d', time()-(60*60*24*($sd+$c))) ;
      
      $v4 = generate_string(25) ;
      
      $n1 = new news(array('writer_id'=>$v1, 'news_type'=>news::TYPE_COLUMN, 'id'=>null, 'subject'=>$v2, 'news_date'=>$v3, 'text'=>$v4)) ;
    }
}
catch (Exception $e)
{
  echo $e;
}

echo "<br>DONE in " . ($te-$ts) . " seconds<br>" ;
?>
