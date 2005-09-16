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
  $c_gt     = $a['game_type'] ;
  $c_loc    = $a['location'] ;
  $c_maps   = $a['maps'] ;
  $c_tour   = $a['tourney'] ;
  $c_play   = $a['players'] ;
  $c_div    = $a['division'] ;
  $c_team   = $a['team'] ;
  $c_match  = $a['match'] ;
  $c_game   = $a['game'] ;
  $c_comm   = $a['comments'] ;
  $c_stats  = $a['stats'] ;
  $c_news   = $a['news'] ;

  /*
  for ($i=0; $i<$c_gt; $i++)
    {
      $v1 = PREFIX . generate_string(10) ;
      $n = new game_type(array('name'=> $v1)) ;

      $gt[] = $n->getValue("game_type_id") ;
    }

  for ($i=0; $i<$c_loc; $i++)
    {
      $v1 = PREFIX . generate_string(10) ;
      $v2 = PREFIX . generate_string(10) ;
      $v3 = PREFIX . generate_string(25) ;
      $n = new location(array('country_name'=> $v1, 'state_name'=>$v2, 'logo_url'=>$v3)) ;

      $loc[] = $n->getValue("location_id") ;
    }

  for ($i=0; $i<$c_maps; $i++)
    {
      $v1 = PREFIX . generate_string(10) ;
      $v2 = PREFIX . generate_string(3) ;
      $v3 = $gt[generate_integer(count($gt))] ;
      $n = new map(array('map_name'=> $v1, 'map_abbr'=>$v2, 'game_type_id'=>$v3)) ;

      $maps[] = $n->getValue("map_id") ;
    }
  */

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

  $mapsa = map::getAllMaps() ;
  foreach ($mapsa as $m)
    {
      $maps[] = $m->getValue("map_id") ;
    }

  for ($i=0; $i<$c_tour; $i++)
    {
      $v1 = $gt[generate_integer(count($gt))] ;
      $v2 = 'naql-' . generate_string(5) ;
      $v3 = 'TOURNAMENT' ;

      $sd = generate_integer(100) ;

      $v4 = date('Y-m-d', time()+(60*60*24*$sd)) ;
      $v5 = date('Y-m-d', time()+(60*60*24*($sd+generate_integer(100)+1))) ;
      $v6 = generate_integer(20) ;
      $v7 = generate_integer(20) ;

      $n = new tourney(array('game_type_id'=>$v1, 'name'=>$v2, 'tourney_type'=>$v3,
			     'signup_start'=>$v4, 'signup_end'=>$v5, 'team_size'=>$v6, 'timelimit'=>$v7)) ;

      $tour[] = $n->getValue("tourney_id") ;
    }

  for ($i=0; $i<$c_play; $i++)
    {
      $v1 = 'player-' . generate_string(8) ;
      $v2 = generate_boolean() ;
      $v3 = $loc[generate_integer(count($loc))] ;
      $v4 = PREFIX . generate_string(10) ; 

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
      $v4 = generate_integer(10) + 2;
      $v5 = generate_integer(10) ;
      $v6 = generate_integer(10) ;
      $n = new division(array('tourney_id'=>$v1, 'name'=>$v2, 'num_games'=>$v4, 'playoff_spots'=>$v5, 'elim_losses'=>$v6)) ;

      $div[] = $n->getValue("division_id") ;
    }

  for ($i=0; $i<$c_team; $i++)
    {
      $v1 = 'team-' . generate_string(8) ; 
      $v2 = 'email@-' . generate_string(10) ; 
      $v3 = '#chan_' . generate_string(10) ; 
      $v4 = $loc[generate_integer(count($loc))] ;
      $v5 = generate_string(10) ;
      $v6 = generate_boolean() ;
      $v7 = generate_string(4) ;

      $n = new team(array('name'=>$v1, 'email'=>$v2, 'irc_channel'=>$v3, 'location_id'=>$v4, 'password'=>$v5, 'approved'=>$v6, 'name_abbr'=>$v7)) ;

      $team[] = $n->getValue("team_id") ;
    }

  /*
  for ($i=0; $i<$c_match; $i++)
    {
      $v1 = $div[generate_integer(count($div))] ;
      $v2 = $team[generate_integer(count($team))] ;

      $v3 = $team[generate_integer(count($team))] ;
      while ($v3==$v2 and $c_team!=1)
	{
	  $v3 = $team[generate_integer(count($team))] ;
	}

      if (generate_boolean())
	{
	  $v4 = $v2 ;
	}
      else
	{
	  $v4 = $v3;
	}

      $v5 = generate_boolean() ;
      $v6 = date('Y-m-d', time()+(60*60*24*(generate_integer(100)))) ;
      $v7 = date('Y-m-d', time()+(60*60*24*(generate_integer(100)))) ;
      $v8 = 'week-' . generate_string(3) ;
      $n = new match(array('division_id'=>$v1, 'team1_id'=>$v2, 'team2_id'=>$v3, 'winning_team_id'=>$v4, 'approved'=>$v5,
			   'match_date'=>$v6, 'deadline'=>$v7, 'week_name'=>$v8)) ;

      $mat[] = $n->getValue("match_id") ;
    }
  */

  for ($i=0; $i<$c_news; $i++)
    {
      $v1 = $play[generate_integer(count($play))] ;

      if (generate_boolean())
	{
	  $v2 = 'TOURNEY' ;
	  $v3 = $tour[generate_integer(count($tour))] ;
	}
      else
	{
	  $v2 = 'NEWS' ;
	  $v3 = null ;
	}

      $v4 = 'subject-' . generate_string(25) ;

      $c = $c + generate_integer(25) ;
      $v5 = date('Y-m-d', time()-(60*60*24*($sd+$c))) ;

      $v6 = generate_string(25) ;
      $n = new news(array('writer_id'=>$v1, 'news_type'=> $v2, 'id'=>$v3, 'subject'=>$v4, 'news_date'=>$v5, 'text'=>$v6)) ;

      //$news[] = $n->getValue("news_id") ;
    }


  for ($i=0; $i<count($tour); $i++)
    {
      $t = new tourney(array('tourney_id'=>$tour[$i])) ;

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
	  $p = new player(array('player_id'=>$maps[generate_integer(count($player))])) ;
	  $v1 = generate_boolean() ;
	  
	  if (! $t->hasAdmin($p->getValue("player_id")))
	    {
	      $t->addAdmin($p->getValue("player_id"), $v1) ;
	    }
	}
    
      $tdiv = $t->getDivisions() ;
      for ($j=0; $j<count($tdiv); $j++)
	{
	  $d = $tdiv[$j] ;

	  $c = generate_integer(2) + 3 ;
	  for ($k=0; $k<$c; $k++)
	    {
	      $te = new team(array('team_id'=>$team[generate_integer(count($team))])) ;

	      if (! $t->hasTeam($te->getValue("team_id")))
		{
		  try
		    {
		      $d->addTeam($te->getValue("team_id")) ;
		      $dteam[] = $te->getValue("team_id") ;
		    }
		  catch(Exception $e) {}
		}
	      
	      $pc = generate_integer(10) + 1 ;
	      for ($l=0; $l<$pc; $l++)
		{
		  $p = new player(array('player_id'=>$play[generate_integer(count($team))])) ;
		  $v1 = generate_boolean() ;
		  if (! $t->hasPlayer($p->getValue("player_id")))
		    {
		      try
			{
			  $te->addPlayer($t->getValue("tourney_id"), $p->getValue("player_id"), $v1) ;
			  $dplay[] = $p->getValue("player_id") ;
			}
		      catch(Exception $e) {print $e;}
		    }
		}
	    }

	  $d->createSchedule(generate_integer(3)+5) ;
	  $mata = $d->getMatches() ;

	  for ($k=0; $k<count($mata); $k++)
	    {
	      $c = generate_integer(2)+1 ;

	      for ($l=0; $l<$c; $l++)
		{
		  $v1 = $mata[$k]->getValue('match_id') ;
		  $v2 = $maps[generate_integer(count($maps))] ;
		  $v3 = generate_integer(250) ;
		  $v4 = generate_integer(250) ;
		  $v5 = 'http://' . generate_string(25) ;
		  $v6 = 'http://' . generate_string(25) ;
		  
		  try{
		    $n = new game(array('match_id'=>$v1, 'map_id'=>$v2, 'team1_score'=>$v3, 'team2_score'=>$v4, 'screenshot_url'=>$v5, 'demo_url'=>$v6)) ;
		    $game[] = $n->getValue("game_id") ;
		  }
		  catch(Exception $e) {}
		}
	    }

	  for ($k=0; $k<count($game); $k++)
	    {
	      $c = generate_integer(4)+4 ;

	      for ($l=0; $l<$c; $l++)
		{
		  $v1 = $dplay[generate_integer(count($dplay))] ;
		  $v2 = $game[generate_integer(count($game))] ;
		  //$v3 = generate_string(20) ;
		  $v3 = 'SCORE' ;
		  $v4 = generate_integer(20) ;
	  
		  try
		    {
		      $n = new stats(array('player_id'=>$v1, 'game_id'=>$v2, 'stat_name'=>$v3, 'value'=>$v4)) ;
		    }
		  catch(Exception $e) {}
		}

	      //$stats[] = $n->getValue("stat_id") ;
	    }

	  $sql_str = sprintf("update match_table set approved=true, winning_team_id=(case when 0.5<rand() then team1_id else team2_id end)
                              where schedule_id in(select schedule_id from match_schedule where division_id=%d)", $d->getValue('division_id')) ;
	  $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;

	  for ($k=0; $k<count($mata); $k++)
	    {
	      $c = generate_integer(3)+2 ;

	      for ($l=0; $l<$c; $l++)
		{
		  $v1 = 'player-' . generate_string(5) ;
		  $v2 = $mata[$k]->getValue('match_id') ;
		  $v3 = generate_integer(1000) . "." . generate_integer(1000) . "." . generate_integer(1000) . "." . generate_integer(1000) ;
		  $v4 = 'comment-' . generate_string(100) ;
		  $v5 = date('Y-m-d', time()+(60*60*24*($sd+generate_integer(100)+1))) ;
		  $v6 = date('H:i:s', time()+(60*(generate_integer(1440)))) ;
		  $n = new comment(array('name'=>$v1, 'comment_type'=>'MATCH', 'id'=>$v2, 'player_ip'=>$v3, 'comment_text'=>$v4, 'comment_date'=>$v5, 'comment_time'=>$v6)) ;
		  
		  //$comm[] = $n->getValue("comment_id") ;
		}
	    }

	}
    }
}

?>

<?php

$a = array('game_type'=>1,
	   'location'=>5,
	   'maps'=>10,
	   'tourney'=>3,
	   'players'=>100,
	   'division'=>6,
	   'team'=>10,
	   'match'=>20,
	   'game'=>5,
	   'comments'=>5,
	   'stats'=>10,
	   'news'=>25) ;

$ts = microtime(true) ;
try
{
  auto_populate($a) ;
}
catch(Exception $e)
{
  print $e;
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
      $v3 = date('Y-m-d', time()+(60*60*24*($sd+$c))) ;
      
      $v4 = generate_string(25) ;
      
      $n1 = new news(array('writer_id'=>$v1, 'news_type'=>'COLUMN', 'id'=>null, 'subject'=>$v2, 'news_date'=>$v3, 'text'=>$v4)) ;
    }
}
catch (Exception $e)
{
  print $e;
}

print "DONE in " . ($te-$ts) . " seconds" ;
?>
