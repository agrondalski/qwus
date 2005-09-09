<?php
require_once 'dbConnect.php' ;
?>

<?php
class tourney
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

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

	  if ($this->getTourneyInfo()==self::NOTFOUND)
	    {
	      util::throwException("No tourney exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      util::canNotBeNull($a, 'game_type_id') ;
      util::canNotBeNull($a, 'name') ;
      util::canNotBeNull($a, 'tourney_type') ;

      $this->game_type_id  = util::mysql_real_escape_string($a['game_type_id']) ;
      $this->name          = util::mysql_real_escape_string($a['name']) ;
      $this->tourney_type  = util::mysql_real_escape_string($a['tourney_type']) ;
      $this->signup_start  = util::nvl(util::mysql_real_escape_string($a['signup_start']), util::DEFAULT_DATE) ;
      $this->signup_end    = util::nvl(util::mysql_real_escape_string($a['signup_end']), util::DEFAULT_DATE) ;
      $this->team_size     = util::nvl(util::mysql_real_escape_string($a['team_size']), 0) ;
      $this->timelimit     = util::nvl(util::mysql_real_escape_string($a['timelimit']), 0) ;

      $sql_str = sprintf("insert into tourney(game_type_id, name, tourney_type, signup_start, signup_end, team_size, timelimit)" .
                         "values(%d, '%s', '%s', '%s', '%s', %d, %d)",
			 $this->game_type_id, $this->name, $this->tourney_type, $this->signup_start, $this->signup_end, $this->team_size, $this->timelimit) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->tourney_id = mysql_insert_id() ;

      mysql_free_result($result) ;
    }

  private function getTourneyInfo()
    {
      $sql_str = sprintf("select game_type_id, name, tourney_type, signup_start, signup_end, team_size, timelimit from tourney where tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->game_type_id  = $row[0] ;
      $this->name          = $row[1] ;
      $this->tourney_type  = $row[2] ;
      $this->signup_start  = $row[3] ;
      $this->signup_end    = $row[4] ;
      $this->team_size     = $row[5] ; 
      $this->timelimit     = $row[6] ;

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public static function getAllTourneys()
    {
      $sql_str = sprintf('select t.tourney_id from tourney t') ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $sql_str = sprintf("insert into tourney_maps(tourney_id, map_id) values(%d, %d)", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function removeMap($id)
    {
      if (!isset($id))
	{
	  return ;
	}

      $sql_str = sprintf("delete from tourney_maps where tourney_id=%d and map_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function hasMap($id)
    {
      $sql_str = sprintf("select 1 from tourney_maps where tourney_id=%d and map_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)==1)
	{
	  mysql_free_result($row) ;
	  return true ;
	}
      else
	{
	  mysql_free_result($row) ;
	  return false ;
	}
    }

  public function addAdmin($id, $pn)
    {
      $sql_str = sprintf("insert into tourney_admins(tourney_id, player_id, canPostNews) values(%d, %d, %d)", $this->tourney_id, $id, $pn) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function removeAdmin($id, $pn)
    {
      $sql_str = sprintf("delete from tourney_admins where tourney_id=%d and player_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function hasAdmin($id)
    {
      $sql_str = sprintf("select 1 from tourney_admins where tourney_id=%d and player_id=%d", $this->tourney_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)==1)
	{
	  mysql_free_result($row) ;
	  return true ;
	}
      else
	{
	  mysql_free_result($row) ;
	  return false ;
	}
    }

  public function getValue($col)
    {
      if (!isset($col) || !isset($this->$col))
	{
	  return null ;
	}      

      return htmlentities($this->$col) ;
    }

  public function update($col, $val)
    {
      if (! isset($col) || !isset($val))
	{
	  return ;
	}

      $this->$col = util::mysql_real_escape_string($val) ;

      if (is_numeric($val))
	{
	  $sql_str = sprintf("update tourney set %s=%d where tourney_id=%d", $col, $this->$col, $this->tourney_id) ;
	}
      else
	{
	  $sql_str = sprintf("update tourney set %s='%s' where tourney_id=%d", $col, $this->$col, $this->tourney_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from tourney where tourney_id=%d", $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
