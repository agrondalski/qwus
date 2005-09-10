<?php
require_once 'dbConnect.php' ;
?>

<?php
class team
{
  private $team_id ;
  private $name ;
  private $email ;
  private $irc_channel ;
  private $location_id ;
  private $password ;
  private $approved ;

  function __construct($a)
    {
      $id = $a['team_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->team_id = $id ;

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

      $sql_str = sprintf("insert into team(name, email, irc_channel, location_id, password, approved)" .
                         "values('%s', '%s', '%s', %d, '%s', %d)",
			 $this->name, $this->email, $this->irc_channel, $this->location_id, $this->password, $this->approved) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . $mysql_error) ;
      $this->team_id = mysql_insert_id() ;
    }

  private function getTeamInfo()
    {
      $sql_str = sprintf("select name, email, irc_channel, location_id, password, approved from team where team_id=%d", $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str" . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name         = $row[0] ;
      $this->email        = $row[1] ;
      $this->irc_channel  = $row[2] ;
      $this->location_id  = $row[3] ;
      $this->password     = $row[4] ; 
      $this->approved     = $row[5] ;

      mysql_free_result($row) ;

      return util::FOUND ;
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
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
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
	  return util::nvl(util::mysql_real_escape_string($val), false) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public static function getAllTeams()
    {
      $sql_str = sprintf('select t.team_id from team t') ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new team(array('team_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function passwordMatches($pass)
    {
      if (md5($pass)==$this->password)
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
  
	print $tid . ":" . $this->team_id;
        mysql_free_result($result) ;
        return $arr ;
    }

  public function addPlayer($tid, $pid, $itl)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;
      $itl = $this->validateColumn($itl, 'isTeamLeader') ;

      $sql_str = sprintf("insert into player_info(tourney_id, team_id, player_id, isTeamLeader) values(%d, %d, %d, %d)", $tid, $this->team_id, $pid, $itl) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function removePlayer($tid, $pid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("delete from player_info where tourney_id=%d and team_id=%d and player_id=%d", $tid, $this->team_id, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function hasPlayer($tid, $pid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("select 1 from player_info where tourney_id=%d and team_id=%d and player_id=%d", $tid, $this->team_id, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

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

  public function getLocation()
    {
      return new location(array('location_id'=>$this->location_id)) ;
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
    }
}
?>
