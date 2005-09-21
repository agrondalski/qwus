<?php
class tourney
{
  private $tourney_id ;
  private $game_type_id ;
  private $name ;
  private $tourney_type ;
  private $status ;
  private $team_size ;
  private $timelimit ;

  const TYPE_LEAGUE= 0 ;
  const TYPE_TOURNAMENT = 1 ;
  const TYPE_LADDER = 2 ;

  const STATUS_SIGNUPS = 3 ;
  const STATUS_REGULAR_SEASON = 4 ;
  const STATUS_PLAYOFFS= 5 ;
  const STATUS_COMPLETE = 6 ;

  function __construct($a)
    {
      if (array_key_exists('tourney_id', $a))
	{
	  $this->tourney_id = $this->validateColumn($a['tourney_id'], 'tourney_id') ;

	  if ($this->getTourneyInfo()==util::NOTFOUND)
	    {
	      util::throwException("No tourney exists with specified id");
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

      $sql_str = sprintf("insert into tourney(game_type_id, name, tourney_type, status, team_size, timelimit)" .
                         "values(%d, '%s', '%s', '%s', %d, %d)",
			 $this->game_type_id, $this->name, $this->tourney_type, $this->status, $this->team_size, $this->timelimit) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->tourney_id = mysql_insert_id() ;

      mysql_free_result($result) ;
    }

  private function getTourneyInfo()
    {
      $sql_str = sprintf("select game_type_id, name, tourney_type, status, team_size, timelimit from tourney where tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->game_type_id  = $row[0] ;
      $this->name          = $row[1] ;
      $this->tourney_type  = $row[2] ;
      $this->status        = $row[3] ;
      $this->team_size     = $row[4] ; 
      $this->timelimit     = $row[5] ;

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
      $tourney_type_enum = array(self::TYPE_LEAGUE=>'League', self::TYPE_TOURNAMENT=>'Tournament', self::TYPE_LADDER=>'Ladder') ;
      $status_enum = array(self::STATUS_SIGNUPS=>'Signups', self::STATUS_REGULAR_SEASON=>'Regular Season', self::STATUS_PLAYOFFS=>'Playoffs', self::STATUS_COMPLETE=>'Complete') ;

      if ($col == 'tourney_id')
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

      elseif ($col == 'game_type_id')
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

      elseif ($col == 'name')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'tourney_type')
	{
	  if (util::isNull($val))
	    {
	      $val = self::TYPE_LEAGUE ;
	    }
	  
	  if (!is_numeric($val))
	    {
	      util::throwException('invalid value specified for ' . $col) ;
	    }

	  return $tourney_type_enum[$val] ;
	}

      elseif ($col == 'status')
	{
	  if (util::isNull($val))
	    {
	      $val = self::STATUS_SIGNUPS ;
	    }
	  
	  if (!is_numeric($val))
	    {
	      util::throwException('invalid value specified for ' . $col) ;
	    }

	  return $status_enum[$val] ;
	}

      elseif ($col == 'team_size')
	{
	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'timelimit')
	{
	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public static function getAllTourneys()
    {
      $sql_str = sprintf('select t.tourney_id from tourney t') ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new tourney(array('tourney_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public static function getTourneysByStatus($status)
    {
      $status = self::validateColumn($status, 'status') ;
      
      $sql_str = sprintf("select t.tourney_id from tourney t where status='%s'", $status) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new tourney(array('tourney_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTourneyAdmins()
    {
      $sql_str = sprintf("select ta.player_id from tourney_admins ta where ta.tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getDivisions()
    {
      $sql_str = sprintf("select d.division_id from division d where d.tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new division(array('division_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTeams()
    {
      $sql_str = sprintf("select ti.team_id from tourney_info ti where ti.tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new team(array('team_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getUnassignedTeams()
    {
      $sql_str = sprintf("select ti.team_id from tourney_info ti where ti.tourney_id=%d and division_id is null", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new team(array('team_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getPlayers()
    {
      $sql_str = sprintf("select pi.player_id from
                          player_info pi, tourney_info ti
                          where pi.tourney_id=%d and pi.team_id=ti.team_id and ti.tourney_id=pi.tourney_id and ti.division_id is not null",
			 $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getMaps()
    {
      $sql_str = sprintf("select tm.map_id from tourney_maps tm where tm.tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new map(array('map_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getGameTypeMaps()
    {
      $sql_str = sprintf("select m.map_id from maps m where m.game_type_id=%d", $this->game_type_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new map(array('map_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTourneyTypes()
    {
      $arr = array(self::TYPE_LEAGUE=>'League', self::TYPE_TOURNAMENT=>'Tournament', self::TYPE_LADDER=>'Ladder') ;
      return $arr ;
    }

  public function getStatusTypes()
    {
      $arr = array(self::STATUS_SIGNUPS=>'Signups', self::STATUS_REGULAR_SEASON=>'Regular Season', self::STATUS_PLAYOFFS=>'Playoffs', self::STATUS_COMPLETE=>'Complete') ;
      return $arr ;
    }

  public function getNews($a, $l)
    {
      $sql_str = sprintf("select n.* from news n where n.news_type='Tournament' and n.id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNUll($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new news(array('news_id'=>$row['news_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new news(array('news_id'=>$row['news_id'])) ;
	    }
	}

      if (is_array($l) && is_integer($l['limit']))
	{
	  $arr = array_slice($arr, 0, $l['limit']) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getNewsCount()
    {
      $sql_str = sprintf("select count(*) from news n where n.news_type='Tournament' and n.id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($row = mysql_fetch_row($result))
	{
	  mysql_free_result($result) ;
	  return $row[0] ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return 0 ;
	}
    }

  public function getGameType()
    {
      return new game_type(array('game_type_id'=>$this->game_type_id)) ;
    }

  public function addTeam($team_id)
    {
      $team_id  = team::validateColumn($team_id, 'team_id') ;

      if ($this->status!='Signups')
	{
	  util::throwException('Teams can only be added during signup phase') ;
	}

      $sql_str = sprintf("insert into tourney_info(tourney_id, team_id, division_id) values(%d, %d, null)", $this->tourney_id, $team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function removeTeam($id)
    {
      $id = team::validateColumn($id, 'team_id') ;

      if ($this->status!='Signups')
	{
	  util::throwException('Teams can only be added during signup phase') ;
	}

      $sql_str = sprintf("delete from tourney_info where tourney_id=%d and team_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function hasTeam($team_id)
    {
      $team_id = team::validateColumn($team_id, 'team_id') ;

      $sql_str = sprintf("select 1 from tourney_info where tourney_id=%d and team_id=%d", $this->tourney_id, $team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)==1)
	{
	  mysql_free_result($result) ;
	  return true ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return false ;
	}
    }

  public function assignTeamToDiv($team_id, $div)
    {
      $team_id  = team::validateColumn($team_id, 'team_id') ;
      $div = division::validateColumn($div, 'division_id') ;

      $sql_str = sprintf("update tourney_info set division_id=%d where tourney_id=%d and team_id=%d", $div, $this->tourney_id, $team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function removeTeamFromDiv($team_id, $div)
    {
      $team_id  = team::validateColumn($team_id, 'team_id') ;
      $div = division::validateColumn($div, 'division_id') ;

      $sql_str = sprintf("update tourney_info set division_id=null where tourney_id=%d and team_id=%d and division_id=%d", $this->tourney_id, $team_id, $div) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function addMap($mid)
    {
      $mid = map::validateColumn($mid, 'map_id') ;

      $m = new map(array('map_id'=>$mid)) ;

      if ($m->getValue('game_type_id') != $this->game_type_id)
	{
	  util::throwException('map type differs from tourney type') ;
	}

      $sql_str = sprintf("insert into tourney_maps(tourney_id, map_id) values(%d, %d)", $this->tourney_id, $mid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function removeMap($mid)
    {
      $mid = map::validateColumn($mid, 'map_id') ;

      $sql_str = sprintf("delete from tourney_maps where tourney_id=%d and map_id=%d", $this->tourney_id, $mid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function hasMap($mid)
    {
      $mid = map::validateColumn($mid, 'map_id') ;

      $sql_str = sprintf("select 1 from tourney_maps where tourney_id=%d and map_id=%d", $this->tourney_id, $mid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)==1)
	{
	  mysql_free_result($result) ;
	  return true ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return false ;
	}
    }

  public function addAdmin($id)
    {
      $id = player::validateColumn($id, 'player_id') ;

      $sql_str = sprintf("insert into tourney_admins(tourney_id, player_id) values(%d, %d)", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function removeAdmin($id)
    {
      $id = player::validateColumn($id, 'player_id') ;

      $sql_str = sprintf("delete from tourney_admins where tourney_id=%d and player_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function hasAdmin($id)
    {
      $id = player::validateColumn($id, 'player_id') ;

      $sql_str = sprintf("select 1 from tourney_admins where tourney_id=%d and player_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)==1)
	{
	  mysql_free_result($result) ;
	  return true ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return false ;
	}
    }

  public function hasPlayer($pid)
    {
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("select 1 from player_info where tourney_id=%d and player_id=%d", $this->tourney_id, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)==1)
	{
	  mysql_free_result($result) ;
	  return true ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return false ;
	}
    }

  public function getSortedTeamInfo($a)
    {
      $tid = $this->tourney_id ;

      $arr = array() ;
      foreach($this->getTeams() as $t)
	{
	  $arr[] = $t->getTourneyInfo($tid) ;
	}

      return util::row_sort($arr, $a) ;
    }

  public function getSortedPlayerStats($a)
    {
      $arr = $this->getPlayerStats() ;
      return util::row_sort($arr, $a) ;
    }

  public function getPlayerStats($a)
    {
      $player_query = null ;
      $division_query = null ;
      $team_query = null ;

      if (is_array($a))
	{
	  if (!util::isNull($a['player_id']))
	    {
	      $pid = player::validateColumn($a['player_id'], 'player_id') ;
	      $player_query = ' and s.player_id=' . $pid ;
	    }

	  if (!util::isNull($a['team_id']))
	    {
	      $tm = team::validateColumn($a['team_id'], 'team_id') ;
	      $team_query = ' and tm.team_id=' . $tm ;
	    }

	  if (!util::isNull($a['division_id']))
	    {
	      $div = division::validateColumn($a['division_id'], 'division_id') ;
	      $division_query = ' and d.division_id=' . $div ;
	    }
	}


      $sql_str = sprintf("select s.player_id, p.name, tm.team_id, tm.name, d.division_id, d.name,
                                  s.value, m.match_id, m.winning_team_id, m.team1_id, g.team1_score, g.team2_score,
                                (select count(*) from stats s2 where s2.game_id=g.game_id and s2.stat_name='%s' and s2.team_id=s.team_id)
                          from stats s, game g, match_table m, match_schedule ms, team tm, player p, division d
                          where s.stat_name='%s' and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true
                            and m.schedule_id=ms.schedule_id and ms.division_id and ms.division_id = d.division_id and d.tourney_id=%d
                            and s.player_id = p.player_id and s.team_id = tm.team_id %s %s %s
                          order by s.player_id, m.match_id", util::SCORE, util::SCORE, $this->tourney_id, $player_query, $division_query, $team_query) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      $old_player = -1 ;
      $arr = array() ;

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

	      $name = $row[1] ;
	      $team_id = $row[2] ;
	      $team_name = $row[3] ;
	      $division_id = $row[4] ;
	      $division_name = $row[5] ;

	      $arr[$pid] = array() ;
	      $arr[$pid]['player_id'] = $pid ;
	      $arr[$pid]['name']      = $row[1] ;
	      $arr[$pid]['location_id'] = 1 ;

	      $arr[$pid]['team_id']       = $row[2] ;
	      $arr[$pid]['team_name']     = $row[3] ;
	      $arr[$pid]['division_id']   = $row[4] ;
	      $arr[$pid]['division_name'] = $row[5] ;
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

      $arr[$pid]['matches_played'] = $total_matches ;
      $arr[$pid]['games_played']   = $total_games;
      $arr[$pid]['total_frags']    = $total_frags;
      $arr[$pid]['frags_per_game'] = round($total_frags/$total_games, 1);
      $arr[$pid]['matches_won']    = $matches_won ;
      $arr[$pid]['matches_lost']   = $matches_lost ;
      $arr[$pid]['frag_diff']      = round(($total_frags)/($total_games)-($game_avg/$total_games), 1) ;

      mysql_free_result($result) ;

      $sql_str = sprintf("select s.player_id, s.stat_name, s.value
                          from stats s, game g, match_table m, match_schedule ms, division d, team tm
                          where s.stat_name != '%s' and s.game_id=g.game_id and g.match_id=m.match_id and m.approved=true
                            and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id and d.tourney_id=%d
                          %s %s %s",
			 util::SCORE, $this->tourney_id, $player_query, $division_query, $team_query) ;
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

  public function getValue($col)
    {
      $this->validateColumnName($col) ;
      return htmlentities($this->$col) ;
    }

  public function update($col, $val)
    {
      $this->$col = $this->validateColumn($val, $col) ;

      if (is_numeric($this->$col))
	{
	  $sql_str = sprintf("update tourney set %s=%d where tourney_id=%d", $col, $this->$col, $this->tourney_id) ;
	}
      else
	{
	  $sql_str = sprintf("update tourney set %s='%s' where tourney_id=%d", $col, $this->$col, $this->tourney_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from tourney where tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
