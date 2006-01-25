<?php
class tourney
{
  private $tourney_id ;
  private $game_type_id ;
  private $name ;
  private $rules ;
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

      $sql_str = sprintf("insert into tourney(game_type_id, name, rules, tourney_type, status, team_size, timelimit)" .
                         "values(%d, '%s', '%s', '%s', '%s', %d, %d)",
			 $this->game_type_id, $this->name, $this->rules, $this->tourney_type, $this->status, $this->team_size, $this->timelimit) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->tourney_id = mysql_insert_id() ;
    }

  private function getTourneyInfo()
    {
      $sql_str = sprintf("select game_type_id, name, rules, tourney_type, status, team_size, timelimit from tourney where tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->game_type_id  = $row[0] ;
      $this->name          = $row[1] ;
      $this->rules         = $row[2] ;
      $this->tourney_type  = $row[3] ;
      $this->status        = $row[4] ;
      $this->team_size     = $row[5] ; 
      $this->timelimit     = $row[6] ;

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

      elseif ($col == 'rules')
	{
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

  public function addDivision($a)
    {
      $a['tourney_id'] = $this->tourney_id ;
      $d = new division($a) ;
    }

  public function addNews($a)
    {
      $a['id'] = $this->tourney_id ;
      $a['news_type'] = news::TYPE_TOURNEY ;
      $n = new news($a) ;
    }

  public function addPoll($a)
    {
      $a['id'] = $this->tourney_id ;
      $a['poll_type'] = comment::TYPE_TOURNEY ;
      $p = new poll($a) ;
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

  public function getDivisions($a)
    {
      $sql_str = sprintf("select * from division d where d.tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new division(array('division_id'=>$row['division_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new division(array('division_id'=>$row['division_id'])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTeams($a)
    {
      $sql_str = sprintf("select * from tourney_info ti where ti.tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new team(array('team_id'=>$row['team_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new team(array('team_id'=>$row['team_id'])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getUnassignedTeams($a)
    {
      $sql_str = sprintf("select * from tourney_info ti where ti.tourney_id=%d and division_id is null", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new team(array('team_id'=>$row['team_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new team(array('team_id'=>$row['team_id'])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getUnassignedPlayers($a)
    {
      $sql_str = sprintf("select * from player p where p.player_id not in(select player_id from player_info pi where pi.tourney_id=%d)", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new player(array('player_id'=>$row['player_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new player(array('player_id'=>$row['player_id'])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getUnassignedAdmins($a)
    {
      $sql_str = sprintf("select * from player p where p.player_id not in(select player_id from tourney_admins ta where ta.tourney_id=%d)", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new player(array('player_id'=>$row['player_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new player(array('player_id'=>$row['player_id'])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getUnassignedMaps($a)
    {
      $sql_str = sprintf("select * from maps m
                           where m.game_type_id=%d and map_id not in (select tm.map_id from tourney_maps tm where tm.tourney_id=%d)", $this->game_type_id, $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new map(array('map_id'=>$row['map_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new map(array('map_id'=>$row['map_id'])) ;
	    }
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

  public function getMaps($a)
    {
      $sql_str = sprintf("select m.* from tourney_maps tm, maps m where tm.tourney_id=%d and tm.map_id=m.map_id and m.map_name!='%s'", $this->tourney_id, util::FORFEIT_MAP) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new map(array('map_id'=>$row['map_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new map(array('map_id'=>$row['map_id'])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getMapsReport($a)
    {
      $sql_str = sprintf("select m.* from tourney_maps tm, maps m where tm.tourney_id=%d and tm.map_id=m.map_id", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new map(array('map_id'=>$row['map_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new map(array('map_id'=>$row['map_id'])) ;
	    }
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

  public function getTourneyRoot()
    {
      return util::ROOT_DIR . util::SLASH . $this->name ;
    }

  public function getTourneyRootHtml()
    {
      return util::HTML_ROOT_DIR . util::SLASH . $this->name ;
    }

  public function getNews($a, $l)
    {
      $sql_str = sprintf("select n.* from news n where n.news_type='Tournament' and n.id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

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

      $sql_str = sprintf("delete from player_info where tourney_id=%d and team_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
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
    }

  public function removeTeamFromDiv($team_id, $div)
    {
      $team_id  = team::validateColumn($team_id, 'team_id') ;
      $div = division::validateColumn($div, 'division_id') ;

      $sql_str = sprintf("update tourney_info set division_id=null where tourney_id=%d and team_id=%d and division_id=%d", $this->tourney_id, $team_id, $div) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
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
    }

  public function removeMap($mid)
    {
      $mid = map::validateColumn($mid, 'map_id') ;

      $sql_str = sprintf("delete from tourney_maps where tourney_id=%d and map_id=%d", $this->tourney_id, $mid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
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
    }

  public function removeAdmin($id)
    {
      $id = player::validateColumn($id, 'player_id') ;

      $sql_str = sprintf("delete from tourney_admins where tourney_id=%d and player_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
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

  public function getSortedTeamStats($a, $map_id=null)
    {
      if (!util::isNull($map_id) && is_numeric($map_id) && $map_id!=-1)
	{
	  $q['map_id'] = $map_id ;
	}

      $q['tourney_id'] = $this->tourney_id ;
      $arr = stats_team::getTeamStats($q) ;
      return util::row_sort($arr, $a) ;
    }

  public function getSortedPlayerStats($a, $map_id=null)
    {
      if (!util::isNull($map_id) && is_numeric($map_id) && $map_id!=-1)
	{
	  $q['map_id'] = $map_id ;
	}

      $q['tourney_id'] = $this->tourney_id ;
      $arr = stats::getPlayerStats($q) ;
      return util::row_sort($arr, $a) ;
    }

  public function getValue($col, $quote_style=ENT_QUOTES)
    {
      $this->validateColumnName($col) ;

      if ($col!="rules")
	{
	  return util::htmlentities($this->$col, $quote_style) ;
	}
      else
	{
	  return $this->$col ;
	}
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
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from tourney where tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
