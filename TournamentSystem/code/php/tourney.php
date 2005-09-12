<?php
require_once 'dbConnect.php' ;
?>

<?php
class tourney
{
  private $tourney_id ;
  private $game_type_id ;
  private $name ;
  private $tourney_type ;
  private $signup_start ;
  private $signup_end ;
  private $team_size ;
  private $timelimit ;

  function __construct($a)
    {
      $id = $a['tourney_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->tourney_id = $id ;

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

      $sql_str = sprintf("insert into tourney(game_type_id, name, tourney_type, signup_start, signup_end, team_size, timelimit)" .
                         "values(%d, '%s', '%s', '%s', '%s', %d, %d)",
			 $this->game_type_id, $this->name, $this->tourney_type, $this->signup_start, $this->signup_end, $this->team_size, $this->timelimit) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->tourney_id = mysql_insert_id() ;

      mysql_free_result($result) ;
    }

  private function getTourneyInfo()
    {
      $sql_str = sprintf("select game_type_id, name, tourney_type, signup_start, signup_end, team_size, timelimit from tourney where tourney_id=%d", $this->tourney_id) ;
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
      $this->signup_start  = $row[3] ;
      $this->signup_end    = $row[4] ;
      $this->team_size     = $row[5] ; 
      $this->timelimit     = $row[6] ;

      mysql_free_result($result) ;

      return util::FOUND ;
    }

  public function validateColumnName($col)
    {
      $found ;
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
      if ($col == 'tourney_id')
	{
	  if (!$cons)
	    {
	      if (util::isNull($val))
		{
		  util::throwException($col . ' cannot be null') ;
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
	      util::throwException($col . ' cannot be null') ;
	    }

	  if ($val!='LADDER' && $val!='LEAGUE' && $val!='TOURNAMENT')
	    {
	      util::throwException('invalid value specified for ' . $col) ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'signup_start')
	{
	  if (!util::isNull($val) && !util::isValidDate($val))
	    {
	      util::throwException('invalid date specified for ' . $col) ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), util::DEFAULT_DATE) ;
	}

      elseif ($col == 'signup_end')
	{
	  if (!util::isNull($val) && !util::isValidDate($val))
	    {
	      util::throwException('invalid date specified for ' . $col) ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), util::DEFAULT_DATE) ;
	}

      elseif ($col == 'team_size')
	{
	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'timelimit')
	{
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

  public function getTourneyAdmins()
    {
      $sql_str = sprintf("select ta.player_id from tourney_admins ta where ta.tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = $row[0] ;
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
      $sql_str = sprintf("select di.team_id from division d, division_info di where d.tourney_id=%d and d.division_id=di.division_id", $this->tourney_id) ;
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
      $sql_str = sprintf("select pi.player_id from player_info pi where pi.tourney_id=%d", $this->tourney_id) ;
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

  public function getNews($a)
    {
      $sql_str = sprintf("select n.news_id from news n where n.tourney_id=%d %s", $this->tourney_id, util::getOrderBy($a)) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new news(array('news_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getNewsCount()
    {
      $sql_str = sprintf("select count(*) from news n where tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $row = mysql_fetch_row($result) ;
      $val = $row[0] ;

      mysql_free_result($result) ;
      return $val ;
    }

  public function getGameType()
    {
      return new game_type(array('game_type_id'=>$this->game_type_id)) ;
    }

  public function addMap($id)
    {
      $id = map::validateColumn($id, 'map_id') ;

      $sql_str = sprintf("insert into tourney_maps(tourney_id, map_id) values(%d, %d)", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function removeMap($id)
    {
      $id = map::validateColumn($id, 'map_id') ;

      $sql_str = sprintf("delete from tourney_maps where tourney_id=%d and map_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function hasMap($id)
    {
      $id = map::validateColumn($id, 'map_id') ;

      $sql_str = sprintf("select 1 from tourney_maps where tourney_id=%d and map_id=%d", $this->tourney_id, $id) ;
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

  public function addAdmin($id, $pn)
    {
      $id = player::validateColumn($id, 'player_id') ;
      $pn = player::validateColumn($pn, 'canPostNews') ;

      $sql_str = sprintf("insert into tourney_admins(tourney_id, player_id, canPostNews) values(%d, %d, %d)", $this->tourney_id, $id, $pn) ;
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
