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
	  
	  if ($this->getPlayerInfo()==util::NOTFOUND)
	    {
	      util::throwException("No player exists with specified id");
	    }
	  else
	    {
	      return ;
	    }

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
      $sql_str = sprintf("select team_id, value from stats where player_id=%d and game_id=%d", $this->player_id, $this->game_id, $this->stat_name) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->value    = $row[0] ; 
      $this->team_id  = $row[0] ; 

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
      $team_query      = null ;
      $division_query  = null ;
      $tourney_query   = null ;
      $career          = true ;
      
      if (is_array($a))
	{
	  if (!util::isNull($a['team_id']))
	    {
	      $tm = team::validateColumn($a['team_id'], 'team_id') ;
	      $team_query = ' and tm.team_id=' . $tm ;
	    }

	  if (!util::isNull($a['division_id']))
	    {
	      $div = division::validateColumn($a['division_id'], 'division_id') ;
	      $career = false ;

	      $division_query = ' and ti.division_id=' . $div ;
	    }

	  if (!util::isNull($a['tourney_id']))
	    {
	      $tid = tourney::validateColumn($a['tourney_id'], 'tourney_id') ;
	      $career = false ;

	      $tourney_query = ' and ti.tourney_id=' . $tid ;
	    }
	}

      if (!$career)
	{
	  $sql_str = sprintf("select tm.team_id, tm.name, s.score, s.other, s.match_id, tm.location_id
                              from (select m.team1_id team_id, g.team1_score score, g.team2_score other, m.match_id, m.match_date
                                    from match_table m, game g
                                    where m.approved=true and m.match_id=g.match_id
                                   union all
                                    select m.team2_id team_id, g.team2_score score, g.team1_score other, m.match_id, m.match_date
                                    from match_table m, game g
                                    where m.approved=true and m.match_id=g.match_id) s right outer join team tm using (team_id),
                                    tourney_info ti
                               where tm.team_id=ti.team_id %s %s %s
                              order by team_id, match_date desc, match_id desc",
			     $tourney_query, $division_query, $team_query) ;
	}
      else
	{
	  $sql_str = sprintf("select tm.team_id, tm.name, s.score, s.other, s.match_id, tm.location_id
                              from (select m.team1_id team_id, g.team1_score score, g.team2_score other, m.match_id, m.match_date
                                    from match_table m, game g
                                    where ms.schedule_id=m.schedule_id and m.approved=true and m.match_id=g.match_id
                                   union all
                                    select m.team2_id team_id, g.team2_score score, g.team1_score other, m.match_id, m.match_date
                                    from match_table m, game g
                                    where approved=true and m.match_id=g.match_id) s right outer join team tm using(team_id)
                              where 1=1 %s
                              order by team_id, match_date desc, match_id desc",
			     $team_query) ;
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

		$arr[$tmid]['max_score'] = $max_score ;
		$arr[$tmid]['min_score'] = $min_score ;
		$arr[$tmid]['avg_score'] = $frags_for / $num_games ;
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
	      $arr[$tmid]['name'] = htmlentities($row[1], ENT_QUOTES) ;
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
      
      $arr[$tmid]['max_score'] = $max_score ;
      $arr[$tmid]['min_score'] = $min_score ;
      $arr[$tmid]['avg_score'] = $frags_for / $num_games ;
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

      if (!$career && 1==2)
	{
	  $sql_str = sprintf("select s.team_id, s.stat_name, s.value
                              from stats s, game g, match_table m, tourney_info ti
                              where s.stat_name!='%s' and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true and s.team_id=ti.team_id %s %s %s",
			     util::SCORE, $tourney_query, $division_query, $team_query) ;
	}
      elseif (1==2)
	{
	  $sql_str = sprintf("select s.team_id, s.stat_name, s.value
                              from stats s, game g, match_table m
                              where s.stat_name!='%s' and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true",
			     util::SCORE, $team_query) ;
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
	  $sql_str = sprintf("select p.player_id, p.name, null, null, null, null,
                                     s.value, s.match_id, s.winning_team_id, s.team1_id, s.team1_score, s.team2_score,
                                     (select count(*) from stats s2 where s2.game_id=s.game_id and s2.stat_name=s.stat_name and s2.team_id=s.team_id), p.location_id
                              from (select s.player_id, s.value, m.match_id, m.winning_team_id, m.team1_id,
                                           g.team1_score, g.team2_score, g.game_id, s.team_id, s.stat_name
                                    from stats s, game g, match_table m
                                    where s.stat_name='%s' and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true) s, player p
                              where 1=1 %s and s.player_id=p.player_id
                              order by p.player_id, s.match_id",
			     util::SCORE, $player_query) ;
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
		$arr[$pid]['frags_per_game'] = round($total_frags/$total_games, 1);
		$arr[$pid]['matches_won']    = $matches_won ;
		$arr[$pid]['matches_lost']   = $matches_lost ;
		$arr[$pid]['frag_diff']      = round(($total_frags)/($total_games)-($game_avg/$total_games), 1) ;
	      }

	      $pid = $row[0] ;
	      $old_player = $pid ;

	      $total_games   = 0 ;
	      $total_frags   = 0 ;
	      $total_matches = 0 ;
	      $matches_won   = 0 ;
	      $matches_lost  = 0 ;
	      $team_score    = 0 ;
	      $old_match_id  = 0 ;
	      $game_avg = 0 ;

	      $team_id = $row[2] ;

	      $arr[$pid] = array() ;
	      $arr[$pid]['player_id'] = $pid ;
	      $arr[$pid]['name']      = htmlentities($row[1], ENT_QUOTES) ;
	      $arr[$pid]['location_id'] = null ;

	      $arr[$pid]['team_id']       = $row[2] ;
	      $arr[$pid]['team_name']     = htmlentities($row[3], ENT_QUOTES) ;
	      $arr[$pid]['division_id']   = $row[4] ;
	      $arr[$pid]['division_name'] = htmlentities($row[1], ENT_QUOTES) ;
	      $arr[$pid]['location_id']   = $row[13] ;
	    }

	  if (util::isNull($row[6]))
	    {
	      continue ;
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

	  $game_avg += $team_score/$row[12] ;

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
	  $arr[$pid]['frags_per_game'] = round($total_frags/$total_games, 1);
	  $arr[$pid]['matches_won']    = $matches_won ;
	  $arr[$pid]['matches_lost']   = $matches_lost ;
	  $arr[$pid]['frag_diff']      = round(($total_frags)/($total_games)-($game_avg/$total_games), 1) ;
	}

      mysql_free_result($result) ;


      if (!$career)
	{	
	  $sql_str = sprintf("select s.player_id, s.stat_name, s.value
                              from stats s, game g, match_table m, player_info pi, tourney_info ti, team tm
                              where s.stat_name!='%s' %s and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true and s.player_id=pi.player_id
                                and pi.tourney_id=ti.tourney_id and pi.team_id=ti.tourney_id and ti.team_id=tm.team_id %s %s",
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

      if ($quote_style!=ENT_COMPAT && $quote_style!=ENT_QUOTES && $quote_style!=ENT_NOQUOTES)
	{
	  util::throwException('invalid quote_style value') ;
	}

      return htmlentities($this->$col, $quote_style) ;
    }

  public function update($col, $val)
    {
      $this->$col = $this->validateColumn($val, $col) ;

      if (is_numeric($this->$col))
	{
	  $sql_str = sprintf("update stats set %s=%d where player_id=%d and game_id=%d", $col, $this->$col, $this->player_id, $this->game_id) ;
	}
      else
	{
	  $sql_str = sprintf("update stats set %s='%s' where player_id=%d and game_id=%d", $col, $this->$col, $this->player_id, $this->game_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from stats where player_id=%d and game_id=%d", $this->player_id, $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
