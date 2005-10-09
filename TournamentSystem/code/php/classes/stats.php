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
      if (array_key_exists('player_id', $a) && array_key_exists('game_id', $a) && array_key_exists('stat_name', $a) && !array_key_exists('value', $a))
	{
	  $this->player_id = $this->validateColumn($a['player_id'], 'player_id') ;
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

      $sql_str = sprintf("insert into stats(player_id, game_id, stat_name, team_id, value)" .
                         "values(%d, %d, '%s', %d, %d)",
			 $this->player_id, $this->game_id, $this->stat_name, $this->team_id, $this->value) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
    }

  private function getStatsInfo()
    {
      $sql_str = sprintf("select team_id, value from stats where player_id=%d and game_id=%d and stat_name='%s'", $this->player_id, $this->game_id, $this->stat_name) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->team_id  = $row[0] ; 
      $this->value    = $row[1] ; 

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
      if ($col == 'player_id')
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

  public static function getSortedPlayerStats($a)
    {
      $arr = self::getPlayerStats() ;
      return util::row_sort($arr, $a) ;
    }

  public static function getPlayerStats($a)
    {
      $player_query    = null ;
      $team_query      = null ;
      $division_query  = null ;
      $tourney_query   = null ;
      $career          = true ;

      if (is_array($a))
	{
	  if (!util::isNull($a['player_id']))
	    {
	      $pid = player::validateColumn($a['player_id'], 'player_id') ;

	      $player_query = ' and p.player_id=' . $pid ;
	    }

	  if (!util::isNull($a['team_id']))
	    {
	      $tm = team::validateColumn($a['team_id'], 'team_id') ;
	      $career = false ;

	      $team_query = ' and tm.team_id=' . $tm ;
	    }

	  if (!util::isNull($a['division_id']))
	    {
	      $div = division::validateColumn($a['division_id'], 'division_id') ;
	      $career = false ;

	      $division_query = ' and ti.division_id=' . $div ;
	    }

	  elseif (!util::isNull($a['tourney_id']))
	    {
	      $tid = tourney::validateColumn($a['tourney_id'], 'tourney_id') ;
	      $career = false ;

	      $tourney_query = ' and pi.tourney_id=' . $tid ;
	    }
	}


      if (!$career)
	{
	  $sql_str = sprintf("select p.player_id, p.name, tm.team_id, tm.name, d.division_id, d.name,
                                     s.value, s.match_id, s.winning_team_id, s.team1_id, s.team1_score, s.team2_score,
                                     (select count(*) from stats s2 where s2.game_id=s.game_id and s2.stat_name='%s' and s2.team_id=s.team_id), p.location_id
                              from (select s.player_id, s.value, m.match_id, m.winning_team_id, m.team1_id,
                                           g.team1_score, g.team2_score, g.game_id, s.team_id
                                    from stats s, game g, match_table m
                                    where s.stat_name='%s' and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true) s right outer join player p using (player_id),
                                   player_info pi, tourney_info ti, team tm, division d
                              where p.player_id=pi.player_id and pi.tourney_id=ti.tourney_id and pi.team_id=ti.team_id and ti.team_id=tm.team_id and ti.division_id=d.division_id %s %s %s %s
                              order by p.player_id, s.match_id",
			     util::SCORE, util::SCORE, $team_query, $division_query, $tourney_query, $player_query) ;
	}
      else
	{
	  $sql_str = sprintf("select p.player_id, p.name, s.team_id, null, null, null,
                                     s.value, s.match_id, s.winning_team_id, s.team1_id, s.team1_score, s.team2_score,
                                     (select count(*) from stats s2 where s2.game_id=s.game_id and s2.stat_name='%s' and s2.team_id=s.team_id), p.location_id
                              from (select s.player_id, s.value, m.match_id, m.winning_team_id, m.team1_id,
                                           g.team1_score, g.team2_score, g.game_id, s.team_id
                                    from stats s, game g, match_table m
                                    where s.stat_name='%s' s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true) s, player p
                              where 1=1 %s and s.player_id=p.player_id
                              order by p.player_id, s.match_id",
			     util::SCORE, util::SCORE, $player_query) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      $arr = array() ;
      $old_player = -1 ;

      while ($row = mysql_fetch_row($result))
	{
	  if ($row[0] != $old_player) 
	    {
	      if ($old_player!=-1)
	      {
		$arr[$pid]['matches_played'] = $total_matches ;
		$arr[$pid]['games_played']   = $total_games;
		$arr[$pid]['total_frags']    = $total_frags;
		$arr[$pid]['frags_per_game'] = util::choose($total_games!=0, round($total_frags/$total_games, 1), 0);
		$arr[$pid]['matches_won']    = $matches_won ;
		$arr[$pid]['matches_lost']   = $matches_lost ;
		$arr[$pid]['frag_diff']      = util::choose($total_games!=0, round(($total_frags)/($total_games)-($game_avg/$total_games), 1), 0) ;
	      }

	      $pid = $row[0] ;
	      $old_player = $pid ;

	      $total_frags   = 0 ;
	      $total_games   = 0 ;
	      $total_matches = 0 ;
	      $matches_won   = 0 ;
	      $matches_lost  = 0 ;
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

	  if (util::isNull($row[6]))
	    {
	      continue ;
	    }

	  $total_frags += $row[6] ;

	  $total_games += 1 ;

	  if ($team_id==$row[9])
	    {
	      $team_score = $row[10] ;
	    }
	  else
	    {
	      $team_score = $row[11] ;
	    }

	  $game_avg += util::choose($row[12]!=0, $team_score/$row[12], 0) ;
	  
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
	  $arr[$pid]['matches_played'] = $total_matches ;
	  $arr[$pid]['games_played']   = $total_games;
	  $arr[$pid]['total_frags']    = $total_frags;
	  $arr[$pid]['frags_per_game'] = util::choose($total_games!=0, round($total_frags/$total_games, 1), 0);
	  $arr[$pid]['matches_won']    = $matches_won ;
	  $arr[$pid]['matches_lost']   = $matches_lost ;
	  $arr[$pid]['frag_diff']      = util::choose($total_games!=0, round(($total_frags)/($total_games)-($game_avg/$total_games), 1), 0) ;
	}

      mysql_free_result($result) ;


      if (!$career)
	{
	    $sql_str = sprintf("select s.player_id, s.stat_name, s.value
                                from stats s, game g, match_table m, player_info pi, tourney_info ti, team tm
                                where s.stat_name!='%s' and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true and s.player_id=pi.player_id
                                  and pi.tourney_id=ti.tourney_id and pi.team_id=ti.tourney_id and ti.team_id=tm.team_id %s %s %s %s",
			       util::SCORE, $team_query, $division_query, $tourney_query, $player_query) ;
	}
      else
	{
	    $sql_str = sprintf("select s.player_id, s.stat_name, s.value
                                from stats s, game g, match_table m
                                where s.stat_name!='%s' and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true %s",
			       util::SCORE, $player_query) ;
	}
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      while ($row = mysql_fetch_row($result))
	{
	  $pid = $row[0] ;

	  if (!isset($arr[$pid][$row[1]]))
	    {
	      $arr[$pid][$row[1]] = $row[2] ;
	    }
	  else
	    {
	      $arr[$pid][$row[1]] += $row[2] ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public static function getSortedPlayerMapStats($a)
    {
      $arr = self::getPlayerMapStats() ;
      return util::row_sort($arr, $a) ;
    }

  public static function getPlayerMapStats($a)
    {
      $map_query       = null ;
      $player_query    = null ;
      $team_query      = null ;
      $division_query  = null ;
      $tourney_query   = null ;
      $career          = true ;

      if (is_array($a))
	{
	  if (!util::isNull($a['map_id']))
	    {
	      $mid = map::validateColumn($a['map_id'], 'map_id') ;

	      $map_query = ' and g.map_id=' . $mid ;
	    }

	  if (!util::isNull($a['player_id']))
	    {
	      $pid = player::validateColumn($a['player_id'], 'player_id') ;

	      $player_query = ' and p.player_id=' . $pid ;
	    }

	  if (!util::isNull($a['team_id']))
	    {
	      $tm = team::validateColumn($a['team_id'], 'team_id') ;
	      $career = false ;

	      $team_query = ' and tm.team_id=' . $tm ;
	    }

	  if (!util::isNull($a['division_id']))
	    {
	      $div = division::validateColumn($a['division_id'], 'division_id') ;
	      $career = false ;

	      $division_query = ' and ti.division_id=' . $div ;
	    }

	  elseif (!util::isNull($a['tourney_id']))
	    {
	      $tid = tourney::validateColumn($a['tourney_id'], 'tourney_id') ;
	      $career = false ;

	      $tourney_query = ' and pi.tourney_id=' . $tid ;
	    }
	}

      if (!$career)
	{
	  $sql_str = sprintf("select p.player_id, p.name, tm.team_id, tm.name, d.division_id, d.name,
                                     s.value, s.match_id, s.winning_team_id, s.team1_id, s.team1_score, s.team2_score,
                                     (select count(*) from stats s2 where s2.game_id=s.game_id and s2.stat_name='%s' and s2.team_id=s.team_id), p.location_id,
                                     s.map_id, s.map_name
                              from (select s.player_id, s.value, m.match_id, m.winning_team_id, m.team1_id,
                                           g.team1_score, g.team2_score, g.game_id, s.team_id, mp.map_id, mp.map_name
                                    from stats s, game g, match_table m, maps mp
                                    where s.stat_name='%s' and s.game_id=g.game_id %s and g.map_id=mp.map_id and g.match_id=m.match_id and m.approved=true) s, player p,
                                   player_info pi, tourney_info ti, team tm, division d
                              where s.player_id=p.player_id and p.player_id=pi.player_id and pi.tourney_id=ti.tourney_id and
                                    pi.team_id=ti.team_id and ti.team_id=tm.team_id and ti.division_id=d.division_id %s %s %s %s
                              order by p.player_id, s.map_id",
			     util::SCORE, util::SCORE, $map_query, $team_query, $division_query, $tourney_query, $player_query) ;
	}
      else
	{
	  $sql_str = sprintf("select p.player_id, p.name, s.team_id, null, null, null,
                                     s.value, s.match_id, s.winning_team_id, s.team1_id, s.team1_score, s.team2_score,
                                     (select count(*) from stats s2 where s2.game_id=s.game_id and s2.stat_name='%s' and s2.team_id=s.team_id), p.location_id,
                                     s.map_id, s.map_name
                              from (select s.player_id, s.value, m.match_id, m.winning_team_id, m.team1_id,
                                           g.team1_score, g.team2_score, g.game_id, s.team_id, mp.map_id, mp.map_name
                                    from stats s, game g, match_table m, maps mp
                                    where s.stat_name='%s' and s.game_id=g.game_id and %s g.map_id=mp.map_id and g.match_id=m.match_id and m.approved=true) s, player p
                              where 1=1 %s and s.player_id=p.player_id
                              order by p.player_id, s.map_id",
			     util::SCORE, util::SCORE, $map_query, $player_query) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      $arr = array() ;
      $old_player = -1 ;
      $old_map    = -1 ;

      while ($row = mysql_fetch_row($result))
	{
	  if ($row[0] != $old_player || $row[14] != $old_map) 
	    {
	      if ($old_player!=-1)
	      {
		$arr[$pid . '-' . $mid]['games_played']   = $total_games;
		$arr[$pid . '-' . $mid]['total_frags']    = $total_frags;
		$arr[$pid . '-' . $mid]['frags_per_game'] = util::choose($total_games!=0, round($total_frags/$total_games, 1), 0);
		$arr[$pid . '-' . $mid]['games_won']      = $games_won ;
		$arr[$pid . '-' . $mid]['games_lost']     = $games_lost ;
		$arr[$pid . '-' . $mid]['frag_diff']      = util::choose($total_games!=0, round(($total_frags)/($total_games)-($game_avg/$total_games), 1), 0) ;
	      }

	      $pid = $row[0] ;
	      $mid = $row[14] ;
	      $old_player = $pid ;
	      $old_map    = $mid ;

	      $total_games   = 0 ;
	      $total_frags   = 0 ;
	      $games_won     = 0 ;
	      $games_lost    = 0 ;
	      $team_score    = 0 ;
	      $old_match_id  = 0 ;
	      $game_avg      = 0 ;

	      $team_id = $row[2] ;

	      $arr[$pid . '-' . $mid] = array() ;
	      $arr[$pid . '-' . $mid]['player_id']   = $pid ;
	      $arr[$pid . '-' . $mid]['name']        = util::htmlentities($row[1], ENT_QUOTES) ;
	      $arr[$pid . '-' . $mid]['location_id'] = $row[13] ;

	      $arr[$pid . '-' . $mid]['map_id']    = $mid ;
	      $arr[$pid . '-' . $mid]['map_name']  = util::htmlentities($row[15], ENT_QUOTES) ;
	      $arr[$pid . '-' . $mid]['team_id']       = $row[2] ;
	      $arr[$pid . '-' . $mid]['team_name']     = util::htmlentities($row[3], ENT_QUOTES) ;
	      $arr[$pid . '-' . $mid]['division_id']   = $row[4] ;
	      $arr[$pid . '-' . $mid]['division_name'] = util::htmlentities($row[5], ENT_QUOTES) ;
	    }

	  $total_games += 1 ;
	  $total_frags += $row[6] ;

	  if ($team_id==$row[9])
	    {
	      $team_score = $row[10] ;
	    }
	  else
	    {
	      $team_score = $row[11] ;
	    }

	  $game_avg += util::choose($row[12]!=0, $team_score/$row[12], 0) ;

	  if ($row[2]==$row[8])
	    {
	      $games_won += 1 ;
	    }
	  else
	    {
	      $games_lost += 1 ;
	    }
	}

      if (!util::isNull($arr[$pid . '-' . $mid]))
	{
	  $arr[$pid . '-' . $mid]['games_played']   = $total_games;
	  $arr[$pid . '-' . $mid]['total_frags']    = $total_frags;
	  $arr[$pid . '-' . $mid]['frags_per_game'] = util::choose($total_games!=0, round($total_frags/$total_games, 1), 0);
	  $arr[$pid . '-' . $mid]['games_won']    = $games_won ;
	  $arr[$pid . '-' . $mid]['games_lost']   = $games_lost ;
	  $arr[$pid . '-' . $mid]['frag_diff']    = util::choose($total_games!=0, round(($total_frags)/($total_games)-($game_avg/$total_games), 1). 0) ;
	}

      mysql_free_result($result) ;


      if (!$career)
	{
	    $sql_str = sprintf("select s.player_id, s.stat_name, s.value, g.map_id
                                from stats s, game g, match_table m, player_info pi, tourney_info ti, team tm
                                where s.stat_name!='%s' %s and s.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true and s.player_id=pi.player_id
                                  and pi.tourney_id=ti.tourney_id and pi.team_id=ti.tourney_id and ti.team_id=tm.team_id %s %s %s",
			       util::SCORE, $player_query, $map_query, $division_query, $tourney_query, $team_query) ;
	}
      else
	{
	    $sql_str = sprintf("select s.player_id, s.stat_name, s.value, g.map_id
                                from stats s, game g, match_table m
                                where s.stat_name!='%s' and s.game_id=g.game_id %s and g.match_id=m.match_id and m.approved=true",
			       util::SCORE, $player_query, $map_query) ;
	}
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      while ($row = mysql_fetch_row($result))
	{
	  $pid = $row[0] ;
	  $mid = $row[3] ;

	  if (!isset($arr[$pid . '-' . $mid][$row[1]]))
	    {
	      $arr[$pid . '-' . $mid][$row[1]] = $row[2] ;
	    }
	  else
	    {
	      $arr[$pid . '-' . $mid][$row[1]] += $row[2] ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public static function hasStatsEntry($pid, $gid, $sn)
    {
      $pid = player::validateColumn($pid, 'player_id') ;
      $gid = game::validateColumn($gid, 'game_id') ;
      $sn  = self::validateColumn($sn, 'stat_name') ;

      $sql_str = sprintf("select 1 from stats where player_id=%d and game_id=%d and stat_name='%s'", $pid, $gid, $sn) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return false ; 
	}

      return true ;
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
	  $sql_str = sprintf("update stats set %s=%d where player_id=%d and game_id=%d and stat_name='%s'", $col, $this->$col, $this->player_id, $this->game_id, $this->stat_name) ;
	}
      else
	{
	  $sql_str = sprintf("update stats set %s='%s' where player_id=%d and game_id=%d and stat_name='%s'", $col, $this->$col, $this->player_id, $this->game_id, $this->stat_name) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from stats where player_id=%d and game_id=%d and stat_name='%s'", $this->player_id, $this->game_id, $this->stat_name) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
    }
}
?>
