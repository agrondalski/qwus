<?php
class team
{
  private $team_id ;
  private $name ;
  private $name_abbr ;
  private $email ;
  private $irc_channel ;
  private $location_id ;
  private $password ;
  private $approved ;

  function __construct($a)
    {
      if (array_key_exists('team_id', $a))
	{
	  $this->team_id = $this->validateColumn($a['team_id'], 'team_id') ;

	  if ($this->getTeamInfo()==util::NOTFOUND)
	    {
	      util::throwException("No team exists with specified id");
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

      $sql_str = sprintf("insert into team(name, name_abbr, email, irc_channel, location_id, password, approved)" .
                         "values('%s', '%s', '%s', '%s', %s, '%s', %d)",
			 $this->name, $this->name_abbr, $this->email, $this->irc_channel, util::nvl($this->location_id, 'null'), $this->password, $this->approved) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . $mysql_error) ;
      $this->team_id = mysql_insert_id() ;
    }

  private function getTeamInfo()
    {
      $sql_str = sprintf("select name, name_abbr, email, irc_channel, location_id, password, approved from team where team_id=%d", $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str" . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name         = $row[0] ;
      $this->name_abbr    = $row[1] ;
      $this->email        = $row[2] ;
      $this->irc_channel  = $row[3] ;
      $this->location_id  = $row[4] ;
      $this->password     = $row[5] ; 
      $this->approved     = $row[6] ;

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

      elseif ($col == 'name_abbr')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'email')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'irc_channel')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'location_id')
	{
	  /*
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  */

	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'password')
	{
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return md5($val) ;
	}

      elseif ($col == 'approved')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'isTeamLeader')
	{
	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public static function getAllTeams($a)
    {
      $sql_str = sprintf('select t.* from team t') ;
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

  public function passwordMatches($pass)
    {
      if (md5($pass)==$this->password && !util::isNull($pass) && $this->approved)
	{
	  return true ;
	}
      else
	{
	  return false ;
	}
    }
    
  public function getPlayers($tid)
      {
	$tid = tourney::validateColumn($tid, 'tourney_id') ;

        $sql_str = sprintf("select pi.player_id from player_info pi where pi.tourney_id=%d and pi.team_id=%d", $tid, $this->team_id) ;
        $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
  
        while ($row=mysql_fetch_row($result))
  	{
  	  $arr[] = new player(array('player_id'=>$row[0])) ;
  	}
  
        mysql_free_result($result) ;
        return $arr ;
    }

  public function addPlayer($tid, $pid, $itl)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;
      $itl = $this->validateColumn($itl, 'isTeamLeader') ;

      $sql_str = sprintf("select 1 from player_info pi where pi.tourney_id=%d and pi.player_id=%d", $tid, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
      
      if ($row=mysql_fetch_row($result))
	{
	  util::throwException('this player is already on a team for the specified tourney') ;
	}

      $sql_str = sprintf("insert into player_info(tourney_id, team_id, player_id, isTeamLeader) values(%d, %d, %d, false)", $tid, $this->team_id, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
      
      if ($itl)
	{
	  $this->updateTeamLeader($tid, $pid) ;
	}
      
      mysql_free_result($result) ;
    }

  public function removePlayer($tid, $pid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("delete from player_info where tourney_id=%d and team_id=%d and player_id=%d", $tid, $this->team_id, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function hasPlayer($tid, $pid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("select 1 from player_info pi where pi.tourney_id=%d and pi.team_id=%d and pi.player_id=%d", $tid, $this->team_id, $pid) ;
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

  public function getTeamLeader($tid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;

      $sql_str = sprintf("select pi.player_id from player_info pi where pi.tourney_id=%d and team_id=%d and isTeamLeader=true", $tid, $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($pid = mysql_fetch_row($result))
	{
	  mysql_free_result($result) ;
	  return new player(array('player_id'=>$pid[0])) ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return null;
	}
    }

  public function updateTeamLeader($tid, $pid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("update player_info pi set pi.isTeamLeader=(case when pi.player_id=%d then 1 else 0 end) where pi.tourney_id=%d and pi.team_id=%d", $pid, $tid, $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
    }

  public function getTourneys()
    {
      $sql_str = sprintf("select t.tourney_id from tourney_info t where t.team_id=%d", $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new tourney(array('tourney_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getLocation()
    {
      return new location(array('location_id'=>$this->location_id)) ;
    }

  public function getDivision($tid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;

      $sql_str = sprintf("select ti.division_id
                          from tourney_info ti
                          where ti.team_id=%d and ti.tourney_id=%d", $this->team_id, $tid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if ($row = mysql_fetch_row($result))
	{
	  if (!util::isNull($row[0]))
	  {
	    mysql_free_result($result) ;
	    return new division(array('division_id'=>$row[0])) ;
	  }
	}
	  
      
      mysql_free_result($result) ;
      return null ;
    }

  public function getCareerStats()
    {
      $stats = stats::getTeamStats(array('team_id'=>$this->team_id)) ;
      return $stats['team_id'] ;
    }

  public static function getSortedCareerStats($a)
    {
      $stats = stats::getTeamStats() ;
      return util::row_sort($stats, $a) ;
    }

  public function getSortedPlayerStats($tid, $a)
    {
      $stats = stats::getPlayerStats(array('team_id'=>$this->team_id, 'tourney_id'=>$tid)) ;
      return util::row_sort($stats, $a) ;
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
	  $sql_str = sprintf("update team set %s=%d where team_id=%d", $col, $this->$col, $this->team_id) ;
	}
      else
	{
	  $sql_str = sprintf("update team set %s='%s' where team_id=%d", $col, $this->$col, $this->team_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from team where team_id=%d", $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
