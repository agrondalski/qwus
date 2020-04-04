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

      $result = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
    }

  private function getStatsInfo()
    {
      $sql_str = sprintf("select value from stats_team where team_id=%d and game_id=%d and stat_name='%s'", $this->team_id, $this->game_id, $this->stat_name) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      if (mysqli_num_rows($result)!=1)
	{
	  mysqli_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysqli_fetch_row($result) ;

      $this->value  = $row[0] ; 

      mysqli_free_result($result) ;

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
	  
	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
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
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      if (mysqli_num_rows($result)!=1)
	{
	  mysqli_free_result($result) ;
	  return false ; 
	}

      return true ;
    }

  private static function computeStreaks(&$match_maps_won, &$match_maps_lost, &$total_wins, &$total_losses, &$winning_streak, &$losing_streak,
                                  &$cur_winning_streak, &$cur_losing_streak, &$max_winning_streak, &$max_losing_streak)
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
      $map_query_g       = null ;
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
	      $tourney_query_d  = ' and d.tourney_id=' . $tid ;
	    }
	}

      if (!$career)
	{
	  $sql_str = sprintf("select tm.team_id, tm.name, s.score, s.other, s.match_id, tm.location_id, s.map_name
                              from (select mt.team1_id team_id, g.team1_score score, g.team2_score other, mt.match_id, mt.match_date, m.map_name
                                    from game g, maps m, match_table mt, match_schedule ms, division d
                                    where g.map_id=m.map_id and g.match_id=mt.match_id %s %s and mt.approved=true and
                                          mt.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s
                                   union all
                                    select mt.team2_id team_id, g.team2_score score, g.team1_score other, mt.match_id, mt.match_date, m.map_name
                                    from game g, maps m, match_table mt, match_schedule ms, division d
                                    where g.map_id=m.map_id and g.match_id=mt.match_id %s %s and mt.approved=true and
                                          mt.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s) s right outer join team tm using (team_id),
                                    tourney_info ti
                               where tm.team_id=ti.team_id %s %s %s %s
                              order by team_id, match_date desc, match_id desc",
			     $map_query_g, $game_query_g, $tourney_query_d, $division_query_d, $map_query_g, $game_query_g,
			     $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_ti, $team_query_tm, $game_query_s) ;
	}
      else
	{
	  $sql_str = sprintf("select tm.team_id, tm.name, s.score, s.other, s.match_id, tm.location_id, s.map_name
                              from (select mt.team1_id team_id, g.team1_score score, g.team2_score other, mt.match_id, mt.match_date, m.map_name
                                    from game g, maps m, match_table mt
                                    where %s g.map_id=m.map_id and g.match_id=mt.match_id and mt.approved=true
                                   union all
                                    select mt.team2_id team_id, g.team2_score score, g.team1_score other, mt.match_id, mt.match_date, m.map_name
                                    from game g, maps m, match_table mt
                                    where %s g.map_id=m.map_id and g.match_id=mt.match_id and mt.approved=true) s right outer join team tm using(team_id)
                              where 1=1 %s
                              order by team_id, match_date desc, match_id desc",
			     $map_query_g, $map_query_g, $team_query_tm) ;
	}
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));

      $arr = array() ;
      $old_team = -1;

      $min_score       = 0 ;
      $max_score       = 0 ;
      $frags_for       = 0 ;
      $frags_against   = 0 ;
      $games_won       = 0 ;
      $games_lost      = 0 ;
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

      while ($row = mysqli_fetch_row($result))
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
		    $arr[$tmid][util::WINNING_STREAK] = $cur_winning_streak ;
		  }
		elseif($cur_losing_streak>0)
		  {
		    $arr[$tmid][util::LOSING_STREAK] = $cur_losing_streak ;
		  }

		$arr[$tmid][util::TOTAL_SCORE]     = $frags_for ;
		$arr[$tmid][util::TOTAL_SCORE_OPP] = $frags_against ;
		$arr[$tmid][util::SCORE_DIFF]      = $frags_for - $frags_against ;
		$arr[$tmid][util::SCORE_PER_GAME]  = util::choose($num_games!=0, round($frags_for / $num_games, 2), 0) ;
		$arr[$tmid][util::POINTS]          = ($arr[$tmid]['match_2-0']*3) + ($arr[$tmid]['match_2-1']*3) + ($arr[$tmid]['match_1-2']) ;
		$arr[$tmid][util::MAX_SCORE]       = $max_score ;
		$arr[$tmid][util::MIN_SCORE]       = $min_score ;
		$arr[$tmid][util::MATCHES_WON]     = $total_wins ;
		$arr[$tmid][util::MATCHES_LOST]    = $total_losses ;
		$arr[$tmid][util::GAMES_PLAYED]    = $num_games ;
		$arr[$tmid][util::GAMES_WON]       = $games_won ;
		$arr[$tmid][util::GAMES_LOST]      = $games_lost ;
		$arr[$tmid][util::MAX_WINNING_STREAK] = $max_winning_streak ;
		$arr[$tmid][util::MAX_LOSING_STREAK]  = $max_losing_streak ;
	      }

	      $tmid = $row[0] ;
	      $old_team = $tmid ;
	      $old_match_id = $row[4] ;

	      $min_score       = $row[2] ;
	      $max_score       = 0 ;
	      $frags_for       = 0 ;
	      $frags_against   = 0 ;
	      $num_games       = 0 ;
	      $games_won       = 0 ;
	      $games_lost      = 0 ;
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

	  if ($row[6]!=util::FORFEIT_MAP)
	    {
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
	    }

	  if ($row[2]>$row[3])
	    {
	      $games_won += 1 ;
	      $match_maps_won += 1 ;
	    }
	  elseif ($row[2]<$row[3])
	    {
	      $games_lost += 1 ;
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
	  $arr[$tmid][util::WINNING_STREAK] = $cur_winning_streak ;
	}
      elseif($cur_losing_streak>0)
	{
	  $arr[$tmid][util::LOSING_STREAK] = $cur_losing_streak ;
	}

      $arr[$tmid][util::TOTAL_SCORE]     = $frags_for ;
      $arr[$tmid][util::TOTAL_SCORE_OPP] = $frags_against ;
      $arr[$tmid][util::SCORE_DIFF]      = $frags_for - $frags_against ;
      $arr[$tmid][util::SCORE_PER_GAME]  = util::choose($num_games!=0, round($frags_for / $num_games, 2), 0) ;
      $arr[$tmid][util::POINTS]          = ($arr[$tmid]['match_2-0']*3) + ($arr[$tmid]['match_2-1']*3) + ($arr[$tmid]['match_1-2']) ;
      $arr[$tmid][util::MAX_SCORE]       = $max_score ;
      $arr[$tmid][util::MIN_SCORE]       = $min_score ;
      $arr[$tmid][util::MATCHES_WON]     = $total_wins ;
      $arr[$tmid][util::MATCHES_LOST]    = $total_losses ;
      $arr[$tmid][util::GAMES_PLAYED]    = $num_games ;
      $arr[$tmid][util::GAMES_WON]       = $games_won ;
      $arr[$tmid][util::GAMES_LOST]      = $games_lost ;
      $arr[$tmid][util::MAX_WINNING_STREAK] = $max_winning_streak ;
      $arr[$tmid][util::MAX_LOSING_STREAK]  = $max_losing_streak ;

      mysqli_free_result($result) ;

      if (!$career)
	{
	  $sql_str = sprintf("select s.team_id, s.stat_name, s.value
                              from stats s, game g, match_table m, match_schedule ms, division d, tourney_info ti
                              where s.stat_name!='%sX' %s and s.game_id=g.game_id %s %s and g.match_id=m.match_id and m.approved=true
                                and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id and d.tourney_id=ti.tourney_id %s %s and s.team_id=ti.team_id %s %s %s",
			     util::SCORE, $team_query_s, $game_query_g, $map_query_g, $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_ti, $team_query_tm) ;
	}
      else
	{
	  $sql_str = sprintf("select s.team_id, s.stat_name, s.value
                              from stats s, game g, match_table m
                              where s.stat_name!='%sX' %s and s.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true",
			     util::SCORE, $team_query_s, $map_query_g) ;
	}
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));

      while ($row = mysqli_fetch_row($result))
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

      mysqli_free_result($result) ;

      if (!$career)
	{
	  $sql_str = sprintf("select st.team_id, st.stat_name, st.value
                              from stats_team st, game g, match_table m, match_schedule ms, division d, tourney_info ti
                              where st.stat_name!='%sX' and st.game_id=g.game_id %s %s and g.match_id=m.match_id and m.approved=true
                                and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id and d.tourney_id=ti.tourney_id %s %s and st.team_id=ti.team_id %s %s %s",
			     util::SCORE, $game_query_g, $map_query_g, $tourney_query_d, $division_query_d, $tourney_query_ti, $division_query_ti, $team_query_st) ;
	}
      else
	{
	  $sql_str = sprintf("select st.team_id, st.stat_name, st.value
                              from stats_team st, game g, match_table m
                              where st.stat_name!='%sX' and st.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true %s",
			     util::SCORE, $map_query_g, $team_query_st) ;
	}
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));

      while ($row = mysqli_fetch_row($result))
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

      // Derived Stats
      foreach ($arr as $k=>$t)
	{
	  if (util::isNull($t[util::TOTAL_FRAGS]))
	    {
	      $t[util::TOTAL_FRAGS] = 0 ;
	    }

	  if (util::isNull($t[util::TOTAL_DEATHS]))
	    {
	      $t[util::TOTAL_DEATHS] = 0 ;
	    }

	  if ($t[util::TOTAL_FRAGS]!=0 || $t[util::TOTAL_DEATHS]!=0)
	    {
	      $arr[$k][util::EFFICIENCY] = round(($t[util::TOTAL_FRAGS]/($t[util::TOTAL_FRAGS]+$t[util::TOTAL_DEATHS]))*100, 2) ;
	    }
	  else
	    {
	      $arr[$k][util::EFFICIENCY] = 0 ;
	    }

	  if ($t[util::TOTAL_FRAGS]!=0 && $arr[$k][util::GAMES_PLAYED]!=0)
	    {
	      $arr[$k][util::FRAGS_PER_GAME] = round($arr[$k][util::TOTAL_FRAGS] / $arr[$k][util::GAMES_PLAYED], 2) ;
	    }
	}


      mysqli_free_result($result) ;
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

      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from stats_team where team_id=%d and game_id=%d and stat_name='%s'", $this->team_id, $this->game_id, $this->stat_name) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
    }
}
?>
