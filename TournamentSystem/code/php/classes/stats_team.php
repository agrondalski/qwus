<?php
class stats_team
{
  private $team_id ;
  private $game_id ;
  private $stat_name ;
  private $value ;

  function __construct($a)
    {
      if (array_key_exists('team_id', $a) && array_key_exists('game_id', $a) && array_key_exists('stat_name', $a) && !array_key_exists('value', $a))
	{
	  $this->team_id   = $this->validateColumn($a['team_id'], 'team_id') ;
	  $this->game_id   = $this->validateColumn($a['game_id'], 'game_id') ;
	  $this->stat_name = $this->validateColumn($a['stat_name'], 'stat_name') ;
	  
	  if ($this->getStatsInfo()==util::NOTFOUND)
	    {
	      util::throwException("No stats exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      foreach($this as $key => $value)
	{
	  $this->$key = $this->validateColumn($a[$key], $key, true) ;
	}

      $sql_str = sprintf("insert into stats_team(team_id, game_id, stat_name, value)" .
                         "values(%d, %d, '%s', %d)",
			 $this->team_id, $this->game_id, $this->stat_name, $this->value) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
    }

  private function getStatsInfo()
    {
      $sql_str = sprintf("select value from stats_team where team_id=%d and game_id=%d and stat_name='%s'", $this->team_id, $this->game_id, $this->stat_name) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->value  = $row[0] ; 

      mysql_free_result($result) ;

      return util::FOUND ;
    }

  public function validateColumnName($col)
    {
      foreach($this as $key => $value)
	{
	  if ($col === $key)
	    {
	      return ;
	    }
	}

      util::throwException('invalid column name specified') ;
    }

  public static function validateColumn($val, $col, $cons=false)
    {
      if ($col == 'team_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'game_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'stat_name')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'value')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public static function hasStatsEntry($tid, $gid, $sn)
    {
      $tid = player::validateColumn($tid, 'team_id') ;
      $gid = game::validateColumn($gid, 'game_id') ;
      $sn  = self::validateColumn($sn, 'stat_name') ;

      $sql_str = sprintf("select 1 from stats_team where team_id=%d and game_id=%d and stat_name='%s'", $tid, $gid, $sn) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return false ; 
	}

      return true ;
    }

  private static function computeStreaks(&$match_maps_won, &$match_maps_lost, &$total_wins, &$total_losses, &$winning_streak, &$losing_streak,
                                  &$cur_winning_streak, &$cur_losing_streak, &$max_winning_streak, &$max_losing_streak, $x)
    {

      if ($match_maps_won==0 && $match_maps_lost==0)
	{
	  return ;
	}

      if ($match_maps_won>$match_maps_lost)
	{
	  $total_wins += 1 ;
	  
	  if ($total_losses==0)
	    {
	      $cur_winning_streak += 1;
	    }
	  
	  if ($losing_streak>0)
	    {
	      $losing_streak = 0 ;
	      $winning_streak = 0 ;
	    }

	  $winning_streak += 1;
	  
	  if ($winning_streak>$max_winning_streak)
	    {
	      $max_winning_streak = $winning_streak ;
	    }
	}
      else
	{
	  $total_losses += 1 ;
	  
	  if ($total_wins==0)
	    {
	      $cur_losing_streak += 1;
	    }
	  
	  if ($winning_streak>0)
	    {
	      $winning_streak = 0 ;
	      $losing_streak = 0 ;
	    }
	  
	  $losing_streak += 1;
	  
	  if ($losing_streak>$max_losing_streak)
	    {
	      $max_losing_streak = $losing_streak ;
	    }
	}
      
    }

  public static function getSortedTeamStats($a)
    {
      $arr = self::getTeamStats() ;
      return util::row_sort($arr, $a) ;
    }

  public static function getTeamStats($a)
    {
      $game_query_g      = null ;
      $game_query_s      = null ;
      $team_query_tm     = null ;
      $team_query_s      = null ;
      $division_query_ti = null ;
      $division_query_d  = null ;
      $tourney_query_ti  = null ;
      $tourney_query_d   = null ;

      $career            = true ;

      if (is_array($a))
	{
	  if (!util::isNull($a['game_id']))
	    {
	      $gid = game::validateColumn($a['game_id'], 'game_id') ;

	      $game_query_g  = ' and g.game_id=' . $gid ;
	      $game_query_s  = ' and s.match_id is not null' ;
	    }

	  if (!util::isNull($a['team_id']))
	    {
	      $tm = team::validateColumn($a['team_id'], 'team_id') ;
	      $team_query_tm = ' and tm.team_id=' . $tm ;
	      $team_query_s  = ' and s.team_id=' . $tm ;
	      $team_query_st = ' and st.team_id=' . $tm ;
	    }

	  if (!util::isNull($a['division_id']))
	    {
	      $div = division::validateColumn($a['division_id'], 'division_id') ;
	      $career = false ;

	      $division_query_ti = ' and ti.division_id=' . $div ;
	      $division_query_d  = ' and d.division_id=' . $div ;
	    }

	  if (!util::isNull($a['tourney_id']))
	    {
	      $tid = tourney::validateColumn($a['tourney_id'], 'tourney_id') ;
	      $career = false ;

	      $tourney_query_ti = ' and ti.tourney_id=' . $tid ;
	      $tourney_query_d  = ' and d.tourney_id=' . $tid ;
	    }
	}

      if (!$career)
	{
	  $sql_str = sprintf("select tm.team_id, tm.name, s.score, s.other, s.match_id, tm.location_id
                              from (select m.team1_id team_id, g.team1_score score, g.team2_score other, m.match_id, m.match_date
                                    from game g, match_table m, match_schedule ms, division d
                                    where g.match_id=m.match_id %s and m.approved=true and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s
                                   union all
                                    select m.team2_id team_id, g.team2_score score, g.team1_score other, m.match_id, m.match_date
                                    from game g, match_table m, match_schedule ms, division d
                                    where g.match_id=m.match_id %s and m.approved=true and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s) s right outer join team tm using (team_id),
                                    tourney_info ti
                               where tm.team_id=ti.team_id %s %s %s %s
                              order by team_id, match_date desc, match_id desc",
			     $game_query_g, $tourney_query_d, $division_query_d, $game_query_g,
			     $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_ti, $team_query_tm, $game_query_s) ;
	}
      else
	{
	  $sql_str = sprintf("select tm.team_id, tm.name, s.score, s.other, s.match_id, tm.location_id
                              from (select m.team1_id team_id, g.team1_score score, g.team2_score other, m.match_id, m.match_date
                                    from game g, match_table m
                                    where g.match_id=m.match_id and m.approved=true
                                   union all
                                    select m.team2_id team_id, g.team2_score score, g.team1_score other, m.match_id, m.match_date
                                    from game g, match_table m
                                    where g.match_id=m.match_id and m.approved=true) s right outer join team tm using(team_id)
                              where 1=1 %s
                              order by team_id, match_date desc, match_id desc",
			     $team_query_tm) ;
	}
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      $arr = array() ;
      $old_team = -1;

      $min_score       = 0 ;
      $max_score       = 0 ;
      $frags_for       = 0 ;
      $frags_against   = 0 ;
      $maps_won        = 0 ;
      $maps_lost       = 0 ;
      $match_maps_won  = 0 ;
      $match_maps_lost = 0 ;
      $total_wins      = 0 ;
      $total_losses    = 0 ;
      $winning_streak  = 0 ;
      $losing_streak   = 0 ;
      $max_winning_streak = 0 ;
      $max_losing_streak  = 0 ;
      $cur_winning_streak = 0 ;
      $cur_losing_streak  = 0 ;

      while ($row = mysql_fetch_row($result))
	{
	  if ($row[0] != $old_team) 
	    {
	      if ($old_team!=-1)
	      {
		self::computeStreaks($match_maps_won, $match_maps_lost, $total_wins, $total_losses, $winning_streak, $losing_streak,
				     $cur_winning_streak, $cur_losing_streak, $max_winning_streak, $max_losing_streak) ;

		$arr_idx = 'match_' . $match_maps_won . '-' . $match_maps_lost ;
		if (!util::isNull($arr[$tmid][$arr_idx]))
		  {
		    $arr[$tmid][$arr_idx] += 1 ;
		  }
		else
		  {
		    $arr[$tmid][$arr_idx] = 1 ;
		  }

		if ($cur_winning_streak>0)
		  {
		    $arr[$tmid]['winning_streak'] = $cur_winning_streak ;
		  }
		elseif($cur_losing_streak>0)
		  {
		    $arr[$tmid]['losing_streak'] = $cur_losing_streak ;
		  }

		$arr[$tmid]['num_games'] = $num_games ;		
		$arr[$tmid]['max_score'] = $max_score ;
		$arr[$tmid]['min_score'] = $min_score ;
		$arr[$tmid]['avg_score'] = util::choose($num_games!=0, $frags_for / $num_games, 0) ;
		$arr[$tmid]['frags_for'] = $frags_for ;
		$arr[$tmid]['frags_against'] = $frags_against ;
		$arr[$tmid]['maps_won']  = $maps_won ;
		$arr[$tmid]['maps_lost'] = $maps_lost ;
		$arr[$tmid]['points']    = ($arr[$tmid]['match_2-0']*3) + ($arr[$tmid]['match_2-1']*3) + ($arr[$tmid]['match_1-2']) ;
		$arr[$tmid]['wins']      = $total_wins ;
		$arr[$tmid]['losses']    = $total_losses ;
		$arr[$tmid]['max_winning_streak'] = $max_winning_streak ;
		$arr[$tmid]['max_losing_streak']  = $max_losing_streak ;
	      }

	      $tmid = $row[0] ;
	      $old_team = $tmid ;
	      $old_match_id = $row[4] ;

	      $min_score       = $row[2] ;
	      $max_score       = 0 ;
	      $frags_for       = 0 ;
	      $frags_against   = 0 ;
	      $num_games       = 0 ;
	      $maps_won        = 0 ;
	      $maps_lost       = 0 ;
	      $match_maps_won  = 0 ;
	      $match_maps_lost = 0 ;
	      $total_wins      = 0 ;
	      $total_losses    = 0 ;
	      $winning_streak  = 0 ;
	      $losing_streak   = 0 ;
	      $max_winning_streak = 0 ;
	      $max_losing_streak  = 0 ;
	      $cur_winning_streak = 0 ;
	      $cur_losing_streak  = 0 ;

	      $arr[$tmid] = array() ;
	      $arr[$tmid]['team_id'] = $row[0] ;
	      $arr[$tmid]['name'] = util::htmlentities($row[1], ENT_QUOTES) ;
	      $arr[$tmid]['location_id'] = $row[5] ;

	      if (util::isNull($row[4]))
		{
		  continue ;
		}
	    }

	  if ($row[4] != $old_match_id)
	    {
	      $old_match_id = $row[4] ;

	      self::computeStreaks($match_maps_won, $match_maps_lost, $total_wins, $total_losses, $winning_streak, $losing_streak,
				   $cur_winning_streak, $cur_losing_streak, $max_winning_streak, $max_losing_streak) ;

	      $arr_idx = 'match_' . $match_maps_won . '-' . $match_maps_lost ;
	      if (!util::isNull($arr[$tmid][$arr_idx]))
		{
		  $arr[$tmid][$arr_idx] += 1 ;
		}
	      else
		{
		  $arr[$tmid][$arr_idx] = 1 ;
		}

	      $match_maps_won = 0 ;
	      $match_maps_lost = 0 ;
	    }

	  $frags_for += $row[2] ;
	  $frags_against += $row[3] ;
	  $num_games += 1 ;

	  if ($row[2] > $max_score)
	    {
	      $max_score = $row[2] ;
	    }

	  if ($row[2] < $min_score)
	    {
	      $min_score = $row[2] ;
	    }

	  if ($row[2]>$row[3])
	    {
	      $maps_won += 1 ;
	      $match_maps_won += 1 ;
	    }
	  else
	    {
	      $maps_lost += 1 ;
	      $match_maps_lost += 1 ;
	    }
	}

      if (util::isNull($arr[$tmid]))
	{
	  return ;
	}

      self::computeStreaks($match_maps_won, $match_maps_lost, $total_wins, $total_losses, $winning_streak, $losing_streak,
			   $cur_winning_streak, $cur_losing_streak, $max_winning_streak, $max_losing_streak) ;
      
      $arr_idx = 'match_' . $match_maps_won . '-' . $match_maps_lost ;
      if (!util::isNull($arr[$tmid][$arr_idx]))
	{
	  $arr[$tmid][$arr_idx] += 1 ;
	}
      else
	{
	  $arr[$tmid][$arr_idx] = 1 ;
	}
      
      if ($cur_winning_streak>0)
	{
	  $arr[$tmid]['winning_streak'] = $cur_winning_streak ;
	}
      elseif($cur_losing_streak>0)
	{
	  $arr[$tmid]['losing_streak'] = $cur_losing_streak ;
	}
      
      $arr[$tmid]['num_games'] = $num_games ;		
      $arr[$tmid]['max_score'] = $max_score ;
      $arr[$tmid]['min_score'] = $min_score ;
      $arr[$tmid]['avg_score'] = util::choose($num_games!=0, $frags_for / $num_games, 0) ;
      $arr[$tmid]['frags_for'] = $frags_for ;
      $arr[$tmid]['frags_against'] = $frags_against ;
      $arr[$tmid]['maps_won']  = $maps_won ;
      $arr[$tmid]['maps_lost'] = $maps_lost ;
      $arr[$tmid]['points']    = ($arr[$tmid]['match_2-0']*3) + ($arr[$tmid]['match_2-1']*3) + ($arr[$tmid]['match_1-2']) ;
      $arr[$tmid]['wins']      = $total_wins ;
      $arr[$tmid]['losses']    = $total_losses ;
      $arr[$tmid]['max_winning_streak'] = $max_winning_streak ;
      $arr[$tmid]['max_losing_streak']  = $max_losing_streak ;
      
      mysql_free_result($result) ;

      if (!$career)
	{
	  $sql_str = sprintf("select s.team_id, s.stat_name, s.value
                              from stats s, game g, match_table m, match_schedule ms, division d, tourney_info ti
                              where s.stat_name!='%s' %s and s.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true
                                and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s and s.team_id=ti.team_id %s %s %s",
			     util::SCORE, $team_query_s, $game_query_g, $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_ti, $team_query_tm) ;
	}
      else
	{
	  $sql_str = sprintf("select s.team_id, s.stat_name, s.value
                              from stats s, game g, match_table m
                              where s.stat_name!='%s' %s and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true",
			     util::SCORE, $team_query_s) ;
	}
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      while ($row = mysql_fetch_row($result))
	{
	  $tid = $row[0] ;

	  if (!isset($arr[$tid][$row[1]]))
	    {
	      $arr[$tid][$row[1]] = $row[2] ;
	    }
	  else
	    {
	      $arr[$tid][$row[1]] += $row[2] ;
	    }
	}

      mysql_free_result($result) ;

      if (!$career)
	{
	  $sql_str = sprintf("select st.team_id, st.stat_name, st.value
                              from stats_team st, game g, match_table m, match_schedule ms, division d, tourney_info ti
                              where st.stat_name!='%s' and st.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true
                                and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s and st.team_id=ti.team_id %s %s %s",
			     util::SCORE, $game_query_g, $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_ti, $team_query_st) ;
	}
      else
	{
	  $sql_str = sprintf("select st.team_id, st.stat_name, st.value
                              from stats_team st, game g, match_table m
                              where st.stat_name!='%s' and st.game_id=g.game_id and g.match_id=m.match_id and m.approved=true %s",
			     util::SCORE, $team_query_st) ;
	}
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      while ($row = mysql_fetch_row($result))
	{
	  $tid = $row[0] ;

	  if (!isset($arr[$tid][$row[1]]))
	    {
	      $arr[$tid][$row[1]] = $row[2] ;
	    }
	  else
	    {
	      $arr[$tid][$row[1]] += $row[2] ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public static function getSortedTeamMapStats($a)
    {
      $arr = self::getTeamMapStats() ;
      return util::row_sort($arr, $a) ;
    }

  public static function getTeamMapStats($a)
    {
      $map_query_g       = null ;
      $game_query_g      = null ;
      $game_query_s      = null ;
      $team_query_tm     = null ;
      $team_query_s      = null ;
      $team_query_st     = null ;
      $division_query_ti = null ;
      $division_query_d  = null ;
      $tourney_query_ti  = null ;
      $tourney_query_d   = null ;

      $career            = true ;

      if (is_array($a))
	{
	  if (!util::isNull($a['map_id']))
	    {
	      $mid = map::validateColumn($a['map_id'], 'map_id') ;

	      $map_query_g = ' and g.map_id=' . $mid ;
	    }

	  if (!util::isNull($a['game_id']))
	    {
	      $gid = game::validateColumn($a['game_id'], 'game_id') ;

	      $game_query_g  = ' and g.game_id=' . $gid ;
	      $game_query_s  = ' and s.match_id is not null' ;
	    }

	  if (!util::isNull($a['team_id']))
	    {
	      $tm = team::validateColumn($a['team_id'], 'team_id') ;
	      $team_query_tm = ' and tm.team_id=' . $tm ;
	      $team_query_s  = ' and s.team_id=' . $tm ;
	      $team_query_st = ' and st.team_id=' . $tm ;
	    }

	  if (!util::isNull($a['division_id']))
	    {
	      $div = division::validateColumn($a['division_id'], 'division_id') ;
	      $career = false ;

	      $division_query_ti = ' and ti.division_id=' . $div ;
	      $division_query_d  = ' and d.division_id=' . $div ;
	    }

	  if (!util::isNull($a['tourney_id']))
	    {
	      $tid = tourney::validateColumn($a['tourney_id'], 'tourney_id') ;
	      $career = false ;

	      $tourney_query_ti = ' and ti.tourney_id=' . $tid ;
	      $tourney_query_d = ' and d.tourney_id=' . $tid ;
	    }
	}

      if (!$career)
	{
	  $sql_str = sprintf("select tm.team_id, tm.name, s.score, s.other, s.match_id, tm.location_id, s.map_id, s.map_name
                              from (select m.team1_id team_id, g.team1_score score, g.team2_score other, m.match_id, m.match_date, mp.map_id, mp.map_name
                                    from game g, maps mp, match_table m, match_schedule ms, division d
                                    where g.match_id=m.match_id %s %s and g.map_id=mp.map_id and m.approved=true and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s
                                   union all
                                    select m.team2_id team_id, g.team2_score score, g.team1_score other, m.match_id, m.match_date, mp.map_id, mp.map_name
                                    from game g, maps mp, match_table m, match_schedule ms, division d
                                    where g.match_id=m.match_id %s %s and g.map_id=mp.map_id and m.approved=true and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s) s, team tm,
                                    tourney_info ti
                               where s.team_id=tm.team_id and tm.team_id=ti.team_id %s %s %s %s
                              order by team_id, map_id, match_date desc, match_id desc",
			     $game_query_g, $map_query_g, $tourney_query_d, $division_query_d, $game_query_g, $map_query_g,
			     $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_ti, $team_query_tm, $game_query_s) ;
	}
      else
	{
	  $sql_str = sprintf("select tm.team_id, tm.name, s.score, s.other, s.match_id, tm.location_id, s.map_id, s.map_name
                              from (select m.team1_id team_id, g.team1_score score, g.team2_score other, m.match_id, m.match_date, mp.map_id, mp.map_name
                                    from game g, match_table m, maps mp
                                    where g.match_id=m.match_id %s and g.map_id=mp.map_id and m.approved=true
                                   union all
                                    select m.team2_id team_id, g.team2_score score, g.team1_score other, m.match_id, m.match_date, mp.map_id, mp.map_name
                                    from game g, match_table m, maps mp
                                    where g.match_id=m.match_id %s and g.map_id=mp.map_id and m.approved=true) s, tm
                              where s.team_id=tm.team_id %s
                              order by team_id, map_id, match_date desc, match_id desc",
			     $map_query_g, $map_query_g, $team_query_tm) ;
	}
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      $arr = array() ;
      $old_team = -1;
      $old_map  = -1;

      while ($row = mysql_fetch_row($result))
	{
	  if ($row[0] != $old_team || $row[6] != $old_map) 
	    {
	      if ($old_team!=-1)
	      {
		self::computeStreaks($match_maps_won, $match_maps_lost, $total_wins, $total_losses, $winning_streak, $losing_streak,
				     $cur_winning_streak, $cur_losing_streak, $max_winning_streak, $max_losing_streak) ;

		if ($cur_winning_streak>0)
		  {
		    $arr[$tmid . '-' . $mid]['winning_streak'] = $cur_winning_streak ;
		  }
		elseif($cur_losing_streak>0)
		  {
		    $arr[$tmid . '-' . $mid]['losing_streak'] = $cur_losing_streak ;
		  }

		$arr[$tmid . '-' . $mid]['num_games'] = $num_games ;		
		$arr[$tmid . '-' . $mid]['max_score'] = $max_score ;
		$arr[$tmid . '-' . $mid]['min_score'] = $min_score ;
		$arr[$tmid . '-' . $mid]['avg_score'] = util::choose($num_games!=0, $frags_for / $num_games, 0) ;
		$arr[$tmid . '-' . $mid]['frags_for'] = $frags_for ;
		$arr[$tmid . '-' . $mid]['frags_against'] = $frags_against ;
		$arr[$tmid . '-' . $mid]['maps_won']  = $maps_won ;
		$arr[$tmid . '-' . $mid]['maps_lost'] = $maps_lost ;
		$arr[$tmid . '-' . $mid]['max_winning_streak'] = $max_winning_streak ;
		$arr[$tmid . '-' . $mid]['max_losing_streak']  = $max_losing_streak ;
	      }

	      $tmid = $row[0] ;
	      $mid  = $row[6] ;
	      $old_team = $tmid ;
	      $old_map  = $mid ;

	      $min_score       = $row[2] ;
	      $max_score       = 0 ;
	      $frags_for       = 0 ;
	      $frags_against   = 0 ;
	      $num_games       = 0 ;
	      $maps_won        = 0 ;
	      $maps_lost       = 0 ;
	      $match_maps_won  = 0 ;
	      $match_maps_lost = 0 ;
	      $total_wins      = 0 ;
	      $total_losses    = 0 ;
	      $winning_streak  = 0 ;
	      $losing_streak   = 0 ;
	      $max_winning_streak = 0 ;
	      $max_losing_streak  = 0 ;
	      $cur_winning_streak = 0 ;
	      $cur_losing_streak  = 0 ;

	      $arr[$tmid . '-' . $mid] = array() ;
	      $arr[$tmid . '-' . $mid]['team_id'] = $row[0] ;
	      $arr[$tmid . '-' . $mid]['name'] = util::htmlentities($row[1], ENT_QUOTES) ;
	      $arr[$tmid . '-' . $mid]['location_id'] = $row[5] ;

	      $arr[$tmid . '-' . $mid]['map_id'] = $mid ;
	      $arr[$tmid . '-' . $mid]['map_name'] = util::htmlentities($row[15], ENT_QUOTES) ;

	      if (util::isNull($row[4]))
		{
		  continue ;
		}
	    }

	  $frags_for += $row[2] ;
	  $frags_against += $row[3] ;
	  $num_games += 1 ;

	  if ($row[2] > $max_score)
	    {
	      $max_score = $row[2] ;
	    }

	  if ($row[2] < $min_score)
	    {
	      $min_score = $row[2] ;
	    }

	  if ($row[2]>$row[3])
	    {
	      $maps_won += 1 ;
	      $match_maps_won += 1 ;
	    }
	  else
	    {
	      $maps_lost += 1 ;
	      $match_maps_lost += 1 ;
	    }
	}

      if (!util::isNull($arr[$tmid . '-' . $mid]))
	{
	  self::computeStreaks($match_maps_won, $match_maps_lost, $total_wins, $total_losses, $winning_streak, $losing_streak,
			       $cur_winning_streak, $cur_losing_streak, $max_winning_streak, $max_losing_streak) ;
      
	  if ($cur_winning_streak>0)
	    {
	      $arr[$tmid . '-' . $mid]['winning_streak'] = $cur_winning_streak ;
	    }
	  elseif($cur_losing_streak>0)
	    {
	      $arr[$tmid . '-' . $mid]['losing_streak'] = $cur_losing_streak ;
	    }
      
	  $arr[$tmid . '-' . $mid]['num_games'] = $num_games ;		
	  $arr[$tmid . '-' . $mid]['max_score'] = $max_score ;
	  $arr[$tmid . '-' . $mid]['min_score'] = $min_score ;
	  $arr[$tmid . '-' . $mid]['avg_score'] = util::choose($num_games!=0, $frags_for / $num_games, 0) ;
	  $arr[$tmid . '-' . $mid]['frags_for'] = $frags_for ;
	  $arr[$tmid . '-' . $mid]['frags_against'] = $frags_against ;
	  $arr[$tmid . '-' . $mid]['maps_won']  = $maps_won ;
	  $arr[$tmid . '-' . $mid]['maps_lost'] = $maps_lost ;
	  $arr[$tmid . '-' . $mid]['max_winning_streak'] = $max_winning_streak ;
	  $arr[$tmid . '-' . $mid]['max_losing_streak']  = $max_losing_streak ;
	}
      
      mysql_free_result($result) ;

      if (!$career)
	{
	  $sql_str = sprintf("select s.team_id, s.stat_name, s.value, g.map_id
                              from stats s, game g, match_table m, match_schedule ms, division d, tourney_info ti
                              where s.stat_name!='%s' and s.game_id=g.game_id %s %s and g.match_id=m.match_id and m.approved=true
                                and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %d %d s.team_id=ti.team_id %s %s %s",
			     util::SCORE, $game_query_g, $map_query_g, $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_ti, $team_query_tm) ;
	}
      else
	{
	  $sql_str = sprintf("select s.team_id, s.stat_name, s.value, g.map_id
                              from stats s, game g, match_table m
                              where s.stat_name!='%s' and s.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true %s",
			     util::SCORE, $map_query_g, $team_query_s) ;
	}
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      while ($row = mysql_fetch_row($result))
	{
	  $tid = $row[0] ;
	  $mid = $row[3] ;

	  if (!isset($arr[$tid . '-' . $mid][$row[1]]))
	    {
	      $arr[$tid . '-' . $mid][$row[1]] = $row[2] ;
	    }
	  else
	    {
	      $arr[$tid . '-' . $mid][$row[1]] += $row[2] ;
	    }
	}

      mysql_free_result($result) ;

      if (!$career)
	{
	  $sql_str = sprintf("select st.team_id, st.stat_name, st.value, g.map_id
                              from stats_team st, game g, match_table m, tourney_info ti
                              where st.stat_name!='%s' and st.game_id=g.game_id %s %s and g.match_id=m.match_id and m.approved=true
                                and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s and st.team_id=ti.team_id %s %s %s",
			     util::SCORE, $game_query_g, $map_query_g, $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_to, $team_query_st) ;
	}
      else
	{
	  $sql_str = sprintf("select st.team_id, st.stat_name, st.value, g.map_id
                              from stats_team st, game g, match_table m
                              where st.stat_name!='%s' %s and st.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true",
			     util::SCORE, $team_query_st, $map_query_g) ;
	}
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      while ($row = mysql_fetch_row($result))
	{
	  $tid = $row[0] ;
	  $mid = $row[3] ;

	  if (!isset($arr[$tid . '-' . $mid][$row[1]]))
	    {
	      $arr[$tid . '-' . $mid][$row[1]] = $row[2] ;
	    }
	  else
	    {
	      $arr[$tid . '-' . $mid][$row[1]] += $row[2] ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getValue($col, $quote_style=ENT_QUOTES)
    {
      $this->validateColumnName($col) ;
      return util::htmlentities($this->$col, $quote_style) ;
    }

  public function update($col, $val)
    {
      $this->$col = $this->validateColumn($val, $col) ;

      if (is_numeric($this->$col))
	{
	  $sql_str = sprintf("update stats_team set %s=%d where team_id=%d and game_id=%d and stat_name='%s'", $col, $this->$col, $this->team_id, $this->game_id, $this->stat_name) ;
	}
      else
	{
	  $sql_str = sprintf("update stats_team set %s='%s' where team_id=%d and game_id=%d and stat_name='%s'", $col, $this->$col, $this->team_id, $this->game_id, $this->stat_name) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from stats_team where team_id=%d and game_id=%d and stat_name='%s'", $this->team_id, $this->game_id, $this->stat_name) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
    }
}
?>
