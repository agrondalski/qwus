<?php
class stats
{
  private $player_id ;
  private $game_id ;
  private $stat_name ;
  private $team_id ;
  private $value ;

  function __construct($a)
    {
      if (array_key_exists('stat_id', $a))
	{
	  $this->stat_id = $this->validateColumn($a['stat_id'], 'stat_id') ;
	  
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

      $sql_str = sprintf("insert into stats(player_id, game_id, stat_name, team_id, value)" .
                         "values(%d, %d, '%s', %d, %d)",
			 $this->player_id, $this->game_id, $this->stat_name, $this->team_id, $this->value) ;
      $result = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
    }

  private function getStatsInfo()
    {
      $sql_str = sprintf("select player_id, game_id, team_id, stat_name, value from stats where stat_id=%d", $this->stat_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      if (mysqli_num_rows($result)!=1)
	{
	  mysqli_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysqli_fetch_row($result) ;

      $this->player_id  = $row[0] ; 
      $this->game_id    = $row[1] ; 
      $this->team_id    = $row[2] ; 
      $this->stat_name  = $row[3] ; 
      $this->value      = $row[4] ; 

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
      if ($col == 'stat_id')
	{
	  if (!$cons)
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
	}

      elseif ($col == 'player_id')
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

      elseif ($col == 'team_id')
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

  public static function getSortedPlayerStats($a)
    {
      $arr = self::getPlayerStats() ;
      return util::row_sort($arr, $a) ;
    }

  public static function getPlayerStats($a)
    {
      $join_query         = null ;

      $map_query_g        = null ;
      $player_query_p     = null ;
      $player_query_s     = null ;
      $game_query_g       = null ;
      $game_query_s       = null ;
      $team_query_tm      = null ;
      $team_query_s       = null ;
      $division_query_ti  = null ;
      $division_query_d   = null ;
      $tourney_query_pi   = null ;
      $tourney_query_d    = null ;

      $career             = true ;

      if (is_array($a))
	{
	  if (!util::isNull($a['all_players']) && is_bool($a['all_players']))
	    {
	      $join_query = ' right outer ' ;
	    }

	  if (!util::isNull($a['map_id']))
	    {
	      $mid = map::validateColumn($a['map_id'], 'map_id') ;

	      $map_query_g = ' and g.map_id=' . $mid ;
	    }

	  if (!util::isNull($a['player_id']))
	    {
	      $pid = player::validateColumn($a['player_id'], 'player_id') ;

	      $player_query_p = ' and p.player_id=' . $pid ;
	      $player_query_s = ' and s.player_id=' . $pid ;
	    }

	  if (!util::isNull($a['game_id']))
	    {
	      $gid = game::validateColumn($a['game_id'], 'game_id') ;

	      $game_query_g = ' and g.game_id=' . $gid . ' and s.value is not null';
	      $game_query_s = ' and s.value is not null';
	    }

	  if (!util::isNull($a['team_id']))
	    {
	      $tm = team::validateColumn($a['team_id'], 'team_id') ;

	      $team_query_tm  = ' and tm.team_id=' . $tm ;
	      $team_query_s   = ' and s.team_id=' . $tm ;
	    }

	  if (!util::isNull($a['division_id']))
	    {
	      $div = division::validateColumn($a['division_id'], 'division_id') ;
	      $career = false ;

	      $division_query_ti = ' and ti.division_id=' . $div ;
	      $division_query_d  = ' and d.division_id=' . $div ;
	    }

	  elseif (!util::isNull($a['tourney_id']))
	    {
	      $tid = tourney::validateColumn($a['tourney_id'], 'tourney_id') ;
	      $career = false ;

	      $tourney_query_pi = ' and pi.tourney_id=' . $tid ;
	      $tourney_query_d  = ' and d.tourney_id=' . $tid ;
	    }
	}

      if (!$career)
	{
	  $sql_str = sprintf("select p.player_id, p.name, tm.team_id, tm.name, d.division_id, d.name, s.value, s.match_id,
                                     s.winning_team_id, s.team1_id, s.team1_score, s.team2_score, s.team_players, p.location_id, s.game_id
                              from (select s.player_id, s.value, m.match_id, m.winning_team_id, m.team1_id, g.game_id,
                                           g.team1_score, g.team2_score, g.game_id, s.team_id,
                                           (select count(*) from stats s2 where s2.game_id=s.game_id and s2.stat_name='%s' and s2.team_id=s.team_id) team_players
                                    from stats s, game g, match_table m, match_schedule ms, division d
                                    where s.stat_name='%s' %s %s and s.game_id=g.game_id %s %s and g.match_id=m.match_id
                                      and m.approved=true and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id %s %s) s %s join player p using (player_id),
                                   player_info pi, tourney_info ti, team tm, division d
                              where p.player_id=pi.player_id and pi.team_id=ti.team_id and pi.tourney_id=ti.tourney_id and ti.team_id=tm.team_id and ti.division_id=d.division_id %s %s %s %s %s
                              order by p.player_id, s.match_id",
			     util::SCORE, util::SCORE, $player_query_s, $team_query_s, $game_query_g, $map_query_g, $tourney_query_d, $division_query_d, $join_query,
			     $team_query_tm, $tourney_query_pi, $division_query_ti, $player_query_p, $game_query_s) ;
	}
      else
	{
	  $sql_str = sprintf("select p.player_id, p.name, s.team_id, null, null, null,
                                     s.value, s.match_id, s.winning_team_id, s.team1_id, s.team1_score, s.team2_score,
                                     (select count(*) from stats s2 where s2.game_id=s.game_id and s2.stat_name='%s' and s2.team_id=s.team_id), p.location_id, s.game_id
                              from (select s.player_id, s.value, m.match_id, m.winning_team_id, m.team1_id,, g.game_id,
                                           g.team1_score, g.team2_score, g.game_id, s.team_id
                                    from stats s, game g, match_table m
                                    where s.stat_name='%s' %s and s.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true) s right outer join player p using (player_id)
                              where 1=1 %s
                              order by p.player_id, s.match_id",
			     util::SCORE, util::SCORE, $player_query_s, $map_query_g, $player_query_p) ;
	}
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));

      $arr = array() ;
      $old_player = -1 ;

      while ($row = mysqli_fetch_row($result))
	{
	  if ($row[0] != $old_player) 
	    {
	      if ($old_player!=-1)
	      {
		$arr[$pid][util::TOTAL_SCORE]    = $total_score;
		$arr[$pid][util::SCORE_PER_GAME] = util::choose($total_games!=0, round($total_score/$total_games, 1), 0);
		$arr[$pid][util::SCORE_DIFF]     = util::choose($total_games!=0, round(($total_score)/($total_games)-($game_avg/$total_games), 1), 0) ;
		$arr[$pid][util::MATCHES_PLAYED] = $total_matches ;
		$arr[$pid][util::MATCHES_WON]    = $matches_won ;
		$arr[$pid][util::MATCHES_LOST]   = $matches_lost ;
		$arr[$pid][util::GAMES_PLAYED]   = $total_games;
		$arr[$pid][util::GAMES_WON]      = $games_won ;
		$arr[$pid][util::GAMES_LOST]     = $games_lost ;
	      }

	      $pid = $row[0] ;
	      $old_player = $pid ;

	      $total_score   = 0 ;
	      $total_games   = 0 ;
	      $total_matches = 0 ;
	      $matches_won   = 0 ;
	      $matches_lost  = 0 ;
	      $games_won     = 0 ;
	      $games_lost    = 0 ;
	      $team_score    = 0 ;
	      $old_match_id  = 0 ;
	      $game_avg = 0 ;

	      $team_id = $row[2] ;

	      $arr[$pid] = array() ;
	      $arr[$pid]['player_id'] = $pid ;
	      $arr[$pid]['name']      = util::htmlentities($row[1], ENT_QUOTES) ;
	      $arr[$pid]['location_id']   = $row[13] ;

	      $arr[$pid]['team_id']       = $row[2] ;
	      $arr[$pid]['team_name']     = util::htmlentities($row[3], ENT_QUOTES) ;
	      $arr[$pid]['division_id']   = $row[4] ;
	      $arr[$pid]['division_name'] = util::htmlentities($row[5], ENT_QUOTES) ;
	    }

	  if (util::isNull($row[6]) || ($row[0]==97 && $row[14]==48))
	    {
	      continue ;
	    }

	  $total_score += $row[6] ;

	  $total_games += 1 ;

	  if ($team_id==$row[9])
	    {
	      $team_score = $row[10] ;
	    }
	  else
	    {
	      $team_score = $row[11] ;
	    }

	  if ($row[14]!=48)
	    {
	      $game_avg += util::choose($row[12]!=0, $team_score/$row[12], 0) ;
	    }
	  else
	    {
	      $game_avg += util::choose($row[12]>1, ($team_score-5)/($row[12]-1), 0) ;
	    }

	  if ($row[2]==$row[9])
	    {
	      if ($row[10]>$row[11])
		{
		  $games_won++ ;
		}
	      elseif ($row[10]<$row[11])
		{
		  $games_lost++ ;
		}
	    }
	  else
	    {
	      if ($row[10]<$row[11])
		{
		  $games_won++ ;
		}
	      if ($row[10]>$row[11])
		{
		  $games_lost++ ;
		}
	    }
	  
	  if ($row[7] != $old_match_id)
	    {
	      $total_matches += 1 ;
	      
	      $old_match_id = $row[7] ;
	      
	      if ($row[8]==$team_id)
		{
		  $matches_won += 1 ;
		}
	      else
		{
		  $matches_lost += 1 ;
		}
	    }
	}

      if (!util::isNull($arr[$pid]))
	{
	  $arr[$pid][util::TOTAL_SCORE]    = $total_score;
	  $arr[$pid][util::SCORE_PER_GAME] = util::choose($total_games!=0, round($total_score/$total_games, 1), 0);
	  $arr[$pid][util::SCORE_DIFF]     = util::choose($total_games!=0, round(($total_score)/($total_games)-($game_avg/$total_games), 1), 0) ;
	  $arr[$pid][util::MATCHES_PLAYED] = $total_matches ;
	  $arr[$pid][util::MATCHES_WON]    = $matches_won ;
	  $arr[$pid][util::MATCHES_LOST]   = $matches_lost ;
	  $arr[$pid][util::GAMES_PLAYED]   = $total_games;
	  $arr[$pid][util::GAMES_WON]      = $games_won ;
	  $arr[$pid][util::GAMES_LOST]     = $games_lost ;
	}

      mysqli_free_result($result) ;

      if (!$career)
	{
	    $sql_str = sprintf("select s.player_id, s.stat_name, s.value
                                from stats s, game g, match_table m, match_schedule ms, division d, player_info pi, tourney_info ti, team tm
                                where s.stat_name!='%sX' %s %s and s.game_id=g.game_id %s %s and g.match_id=m.match_id and m.approved=true and m.schedule_id=ms.schedule_id 
                                  and ms.division_id=d.division_id %s %s and d.tourney_id=pi.tourney_id and s.player_id=pi.player_id and s.team_id=tm.team_id
                                  and pi.tourney_id=ti.tourney_id and pi.team_id=ti.team_id and ti.team_id=tm.team_id %s %s %s %s",
			       util::SCORE, $player_query_s, $team_query_s, $game_query_g, $map_query_g, $tourney_query_d, $division_query_d,
			       $team_query_tm, $tourney_query_pi, $division_query_ti, $game_query_s) ;

	}
      else
	{
	    $sql_str = sprintf("select s.player_id, s.stat_name, s.value
                                from stats s, game g, match_table m
                                where s.stat_name!='%sX' %s and s.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true",
			       util::SCORE, $player_query_s, $map_query_g) ;
	}
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));

      while ($row = mysqli_fetch_row($result))
	{
	  $pid = $row[0] ;

	  if (!isset($arr[$pid][$row[1]]))
	    {
	      $arr[$pid][$row[1]] = $row[2] ;
	    }
	  else
	    {
	      // Maxes/Mins
	      if ($row[1]!=util::FRAG_STREAK)
		{
		  $arr[$pid][$row[1]] += $row[2] ;
		}
	      else
		{
		  if ($row[2]>$arr[$pid][$row[1]])
		    {
		      $arr[$pid][$row[1]] = $row[2] ;
		    }
		}
	    }
	}

      // Derived Stats
      foreach ($arr as $k=>$p)
	{
	  if (util::isNull($p[util::TOTAL_FRAGS]))
	    {
	      $p[util::TOTAL_FRAGS] = 0 ;
	    }

	  if (util::isNull($p[util::TOTAL_DEATHS]))
	    {
	      $p[util::TOTAL_DEATHS] = 0 ;
	    }

	  if ($p[util::TOTAL_FRAGS]!=0 || $p[util::TOTAL_DEATHS]!=0)
	    {
	      $arr[$k][util::EFFICIENCY] = round(($p[util::TOTAL_FRAGS]/($p[util::TOTAL_FRAGS]+$p[util::TOTAL_DEATHS]))*100, 2) ;
	    }
	  else
	    {
	      $arr[$k][util::EFFICIENCY] = 0 ;
	    }

	  if ($p[util::TOTAL_FRAGS]!=0 && $arr[$k][util::GAMES_PLAYED]!=0)
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
	  $sql_str = sprintf("update stats set %s=%d where stat_id_id=%d", $col, $this->$col, $this->stat_id) ;
	}
      else
	{
	  $sql_str = sprintf("update stats set %s='%s' where stat_id=%d", $col, $this->$col, $this->stat_id) ;
	}

      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from stats where stat_id", $this->stat_id, $this->game_id, $this->stat_name) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
    }
}
?>
