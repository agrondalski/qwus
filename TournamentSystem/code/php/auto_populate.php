<?php
require 'includes.php' ;
?>

<?php

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

  for ($i=0; $i<$c_gt; $i++)
    {
      $v1 = PREFIX . generate_string(10) ;
      $n = new game_type(array('name'=> $v1)) ;

      $gs[] = $n->getValue("game_type_id") ;
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
      $v3 = $gs[generate_integer(count($gs))] ;
      $n = new map(array('map_name'=> $v1, 'map_abbr'=>$v2, 'game_type_id'=>$v3)) ;

      $maps[] = $n->getValue("map_id") ;
    }

  for ($i=0; $i<$c_tour; $i++)
    {
      $v1 = $gs[generate_integer(count($gs))] ;
      $v2 = PREFIX . generate_string(10) ;
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
      $v1 = PREFIX . generate_string(10) ;
      $v2 = generate_boolean() ;
      $v3 = $loc[generate_integer(count($loc))] ;
      $v4 = PREFIX . generate_string(10) ; 
      $n = new player(array('name'=>$v1, 'superAdmin'=>$v2, 'location_id'=>$v3, 'password'=>$v4)) ;

      $play[] = $n->getValue("player_id") ;
    }

  for ($i=0; $i<$c_div; $i++)
    {
      $v1 = $tour[generate_integer(count($tour))] ; 
      $v2 = PREFIX . generate_string(10) ; 
      $v3 = generate_integer(10) ;
      $v4 = generate_integer(10) ;
      $v5 = generate_integer(10) ;
      $v6 = generate_integer(10) ;
      $n = new division(array('tourney_id'=>$v1, 'name'=>$v2, 'max_teams'=>$v3, 'num_games'=>$v4, 'playoff_spots'=>$v5, 'elim_losses'=>$v6)) ;

      $div[] = $n->getValue("division_id") ;
    }

  for ($i=0; $i<$c_team; $i++)
    {
      $v1 = PREFIX . generate_string(10) ; 
      $v2 = PREFIX . generate_string(10) ; 
      $v3 = PREFIX . generate_string(10) ; 
      $v4 = $loc[generate_integer(count($loc))] ;
      $v5 = generate_integer(10) ;
      $v6 = generate_boolean() ;
      $n = new team(array('name'=>$v1, 'email'=>$v2, 'irc_channel'=>$v3, 'location_id'=>$v4, 'password'=>$v5, 'approved'=>$v6)) ;

      $team[] = $n->getValue("team_id") ;
    }

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
      $v8 = PREFIX . generate_string(25) ;
      $n = new match(array('division_id'=>$v1, 'team1_id'=>$v2, 'team2_id'=>$v3, 'winning_team_id'=>$v4, 'approved'=>$v5,
			   'match_date'=>$v6, 'deadline'=>$v7, 'week_name'=>$v8)) ;

      $mat[] = $n->getValue("match_id") ;
    }

  for ($i=0; $i<$c_game; $i++)
    {
      $v1 = $mat[generate_integer(count($mat))] ;
      $v2 = $maps[generate_integer(count($maps))] ;
      $v3 = generate_integer(250) ;
      $v4 = generate_integer(250) ;
      $v5 = PREFIX . generate_string(25) ;
      $v6 = PREFIX . generate_string(25) ;
      $n = new game(array('match_id'=>$v1, 'map_id'=>$v2, 'team1_score'=>$v3, 'team2_score'=>$v4, 'screenshot_url'=>$v5, 'demo_url'=>$v6)) ;

      $game[] = $n->getValue("game_id") ;
    }

  for ($i=0; $i<$c_comm; $i++)
    {
      $v1 = generate_string(250) ;
      $v2 = generate_integer(1000) . "." . generate_integer(1000) . "." . generate_integer(1000) . "." . generate_integer(1000) ;
      $v3 = $mat[generate_integer(count($mat))] ;
      $v4 = PREFIX . generate_string(100) ;
      $v5 = date('Y-m-d', time()+(60*60*24*($sd+generate_integer(100)+1))) ;
      $v6 = date('H:i:s', time()+(60*(generate_integer(1440)))) ;
      $n = new comment(array('name'=>$v1, 'player_ip'=>$v2, 'match_id'=>$v3, 'comment_text'=>$v4, 'comment_date'=>$v5, 'comment_time'=>$v6)) ;

      //$comm[] = $n->getValue("comment_id") ;
    }

  for ($i=0; $i<$c_stats; $i++)
    {
      $v1 = $play[generate_integer(count($play))] ;
      $v2 = $game[generate_integer(count($game))] ;
      $v3 = generate_integer(125) ;
      $v4 = generate_integer(20) ;

      if (! stats::hasStatsEntry($v1, $v2))
	{
	  $n = new stats(array('player_id'=>$v1, 'game_id'=>$v2, 'score'=>$v3, 'time'=>$v4, 'elim_losses'=>$v5)) ;
	}

      //$stats[] = $n->getValue("stat_id") ;
    }

  for ($i=0; $i<$c_news; $i++)
    {
      $v1 = $play[generate_integer(count($play))] ;

      if (generate_boolean())
	{
	  $v2 = $tour[generate_integer(count($tour))] ;
	}
      else
	{
	  $v2 = null ;
	}

      $v3 = PREFIX . generate_string(25) ;

      $c = $c + generate_integer(25) ;
      $v4 = date('Y-m-d', time()+(60*60*24*($sd+$c))) ;

      $v5 = PREFIX . generate_string(25) ;
      $n = new news(array('writer_id'=>$v1, 'tourney_id'=>$v2, 'subject'=>$v3, 'news_date'=>$v4, 'text'=>$v5)) ;

      //$news[] = $n->getValue("news_id") ;
    }

  for ($i=0; $i<count($tour); $i++)
    {
      $t = new tourney(array('tourney_id'=>$tour[$i])) ;

      for ($j=0; $j<count($div); $j++)
	{
	  $d = new division(array('division_id'=>$div[$j])) ;

	  $c = generate_integer(2) + 3 ;
	  for ($k=0; $k<$c; $k++)
	    {
	      $te = new team(array('team_id'=>$team[generate_integer(count($team))])) ;

	      if (! $d->hasTeam($te->getValue("team_id")))
		{
		  $d->addTeam($te->getValue("team_id")) ;
		}

	      $pc = generate_integer(10) + 1 ;
	      for ($l=0; $l<$pc; $l++)
		{
		  $p = new player(array('player_id'=>$play[generate_integer(count($team))])) ;
		  $v1 = generate_boolean() ;
		  if (! $te->hasPlayer($t->getValue("tourney_id"), $p->getValue("player_id")))
		    {
		      $te->addPlayer($t->getValue("tourney_id"), $p->getValue("player_id"), $v1) ;
		    }
		}
	    }
	}

      $c = generate_integer(3) + 2 ;
      for ($j=0; $j<$c; $j++)
	{
	  $m = new map(array('map_id'=>$maps[generate_integer(count($maps))])) ;

	  if (! $t->usesMap($m->getValue("map_id")))
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
	   'game'=>45,
	   'comments'=>10,
	   'stats'=>50,
	   'news'=>25) ;

auto_populate($a) ;

print "DONE";
?>