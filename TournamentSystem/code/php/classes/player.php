<?php
class player
{
  private $player_id ;
  private $name ;
  private $superAdmin ;
  private $location_id ;
  private $password ;
  private $hasColumn ;

  function __construct($a)
    {
      if (count($a)==1)
	{
	  if (array_key_exists('player_id', $a))
	    {
	      $this->player_id = $this->validateColumn($a['player_id'], 'player_id') ;

	      if ($this->getPlayerInfo()==util::NOTFOUND)
		{
		  util::throwException("No player exists with specified id");
		}
	      else
		{
		  return ;
		}
	    }

	  if (array_key_exists('name', $a))
	    {
	      $this->name = $this->validateColumn($a['name'], 'name') ;

	      if ($this->getPlayerInfoByName()==util::NOTFOUND)
		{
		  util::throwException("No player exists with specified name");
		}
	      else
		{
		  return ;
		}
	    }
	}

      foreach($this as $key => $value)
	{
	  $this->$key = $this->validateColumn($a[$key], $key, true) ;
	}

      $sql_str = sprintf("insert into player(name, superAdmin, location_id, password, hasColumn)" .
                         "values('%s', %d, %d, '%s', %d)",
			 $this->name, $this->superAdmin, $this->location_id, $this->password, $this->hasColumn) ;

      $result = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->player_id = mysql_insert_id() ;
    }

  private function getPlayerInfo()
    {
      $sql_str = sprintf("select name, superAdmin, location_id, password, hasColumn from player where player_id=%d", $this->player_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name         = $row[0] ;
      $this->superAdmin   = $row[1] ;
      $this->location_id  = $row[2] ;
      $this->password     = $row[3] ; 
      $this->hasColumn    = $row[4] ; 

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

      elseif ($col == 'name')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'superAdmin')
	{
	  return util::nvl(util::mysql_real_escape_string($val), false) ;
	}

      elseif ($col == 'location_id')
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

      elseif ($col == 'password')
	{
	  return md5($val) ;
	}

      elseif ($col == 'hasColumn')
	{
	  return util::nvl(util::mysql_real_escape_string($val), false) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  private function getPlayerInfoByName()
    {
      $sql_str = sprintf("select player_id, superAdmin, location_id, password, hasColumn from player where name='%s'", $this->name) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->player_id    = $row[0] ;
      $this->superAdmin   = $row[1] ;
      $this->location_id  = $row[2] ;
      $this->password     = $row[3] ; 
      $this->hasColumn    = $row[4] ; 

      mysql_free_result($result) ;

      return util::FOUND ;
    }

  public static function getAllPlayers()
    {
      $sql_str = sprintf('select p.player_id from player p') ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function addColumn($a)
    {
      $a['writer_id'] = $this->player_id ;
      $a['news_type'] = news::TYPE_COLUMN ;
      $n = new news($a) ;
    }

  public static function getPlayersWithColumns()
    {
      $sql_str = sprintf("select p.player_id, MAX(n.news_date) as mx from player p, news n where p.hasColumn=1 and p.player_id=n.writer_id and n.news_type='Column' group by p.player_id order by mx desc");
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }


  public function passwordMatches($pass)
    {
      if (md5($pass)==$this->password && !util::isNull($pass))
	{
	  return true ;
	}
      else
	{
	  return false ;
	}
    }

  public function isSuperAdmin()
    {
      return $this->superAdmin ;
    }

  public function isTourneyAdmin($tid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;

      $sql_str = sprintf("select count(*) from tourney_admins ta where ta.tourney_id=%d and ta.player_id=%d", $tid, $this->player_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
      
      if ($row = mysql_fetch_row($result))
	{
	  return ($row[0]>0) ;
	}
      else
	{
	  util::throwException('this cannot ever occur') ;
	}

    }

  public function hasColumn()
    {
      if ($this->hasColumn)
	{
	  return true ;
	}
      else
	{
	  return false ;
	}
    }

  public static function columnCount()
    {
      $sql_str = sprintf("select count(*) from player p where hasColumn=true") ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($row = mysql_fetch_row($result))
	{
	  mysql_free_result($result) ;
	  return $row[0] ;
	}
      else
	{
	  util::throwException('this cannot ever occur') ;
	}
    }

  public function getGamesPlayed($tid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;

      $sql_str = sprintf("select g.game_id, s.value, s.team_id, mp.map_abbr, t.name, m.team1_id, g.team1_score, g.team2_score
                          from stats s, game g, match_table m, match_schedule ms, division d, maps mp, team t
                          where s.player_id=%d and s.stat_name='%s' and s.game_id=g.game_id and g.match_id=m.match_id
                            and m.approved=1 and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id and d.tourney_id=%d and g.map_id=mp.map_id
                            and (m.team1_id=t.team_id or m.team2_id=t.team_id) and t.team_id!=s.team_id
                          order by m.match_date desc, m.match_id desc, g.game_id desc",
			 $this->player_id, util::SCORE, $tid) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  if ($this->player_id!=97 || $row[0]!=48)
	    {
	      $arr[] = new game(array('game_id'=>$row[0])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getLocation()
    {
      return new location(array('location_id'=>$this->location_id)) ;
    }

  public function getNewsColumns($a, $l)
    {
      $sql_str = sprintf("select n.* from news n where n.writer_id=%d and n.news_type='Column'", $this->player_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

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

  public function getLastNewsColumnDate()
    {
      $sql_str = sprintf("select news_date from news n where n.writer_id=%d and n.news_type='Column' order by news_date desc, news_id desc", $this->player_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($row = mysql_fetch_row($result))
	{
	  return $row[0] ;
	}
      else
	{
	  return null ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTeam($tid)
    {
      $tid = division::validateColumn($tid, 'tourney_id') ;

      $sql_str = sprintf("select pi.team_id
                          from player_info pi
                          where pi.player_id=%d and pi.tourney_id=%d", $this->player_id, $tid) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if ($row = mysql_fetch_row($result))
	{
	  return new team(array('team_id'=>$row[0])) ;
	}
      else
	{
	  return null ;
	}
    }

  public function getDivision($tid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;

      $sql_str = sprintf("select ti.division_id
                          from player_info pi, tourney_info ti
                          where pi.player_id=%d and pi.tourney_id=%d and pi.team_id=ti.team_id and ti.tourney_id=pi.tourney_id", $this->player_id, $tid) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if ($row = mysql_fetch_row($result))
	{
	  return new division(array('division_id'=>$row[0])) ;
	}
      else
	{
	  return null ;
	}
    }

  public function getCareerStats()
    {
      $stats = stats::getPlayerStats(array('player_id'=>$this->player_id)) ;
      return $stats[$this->player_id] ;
    }

  public static function getSortedCareerStats($a)
    {
      $stats = stats::getPlayerStats() ;
      return util::row_sort($stats, $a) ;
    }

  public function getTourneyStats($tid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;

      $stats = stats::getPlayerStats(array('player_id'=>$this->player_id, 'tourney_id'=>$tid, 'all_players'=>true)) ;
      return $stats[$this->player_id] ;
    }

  public function getGameStats($gid)
    {
      $gid = game::validateColumn($gid, 'game_id') ;

      $g = new game(array('game_id'=>$gid)) ;
      $t = $g->getTourney() ;

      $stats = stats::getPlayerStats(array('player_id'=>$this->player_id, 'game_id'=>$gid, 'tourney_id'=>$t->getValue('tourney_id'))) ;
      return $stats[$this->player_id] ;
    }

  public function getPieChartIdx($game_id)
    {
      $game_id = game::validateColumn($game_id, 'game_id') ;

      $g = new game(array('game_id'=>$game_id)) ;
      $map = $g->getMap() ;

      return util::PIECHART . '_' . $this->player_id . '_' . $map->getValue('map_abbr') . '_' . $game_id ;
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
	  $sql_str = sprintf("update player set %s=%d where player_id=%d", $col, $this->$col, $this->player_id) ;
	}
      else
	{
	  $sql_str = sprintf("update player set %s='%s' where player_id=%d", $col, $this->$col, $this->player_id) ;
	}

      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from player where player_id=%d", $this->player_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
