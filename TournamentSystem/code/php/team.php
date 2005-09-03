<?php
require_once 'dbConnect.php' ;
?>

<?php
class team
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

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

	  if ($this->getTeamInfo()==self::NOTFOUND)
	    {
	      util::throwException("No team exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      util::canNotBeNull($a, 'name') ;
      util::canNotBeNull($a, 'location_id') ;
      util::canNotBeNull($a, 'password') ;

      $this->name         = util::mysql_real_escape_string($a['name']) ;
      $this->email        = util::mysql_real_escape_string($a['email']) ;
      $this->irc_channel  = util::mysql_real_escape_string($a['irc_channel']) ;
      $this->location_id  = $a['location_id'] ;
      $this->password     = md5($a['password']) ;
      $this->approved     = $a['approved'] ;

      $sql_str = sprintf("insert into team(name, email, irc_channel, location_id, password, approved)" .
                         "values('%s', '%s', '%s', %d, '%s', %d)",
			 $this->name, $this->email, $this->irc_channel, $this->location_id, $this->password, $this->approved) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . $mysql_error) ;
      $this->team_id = mysql_insert_id() ;
    }

  private function getTeamInfo()
    {
      $sql_str = sprintf("select name, email, irc_channel, location_id, password, approved from team where team_id=%d", $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str" . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name         = $row[0] ;
      $this->email        = $row[1] ;
      $this->irc_channel  = $row[2] ;
      $this->location_id  = $row[3] ;
      $this->password     = $row[4] ; 
      $this->approved     = $row[5] ;

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public static function getAllTeams()
    {
      $sql_str = sprintf('select t.team_id from team t') ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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

  public function addplayer($tid, $pid, $itl)
    {
      $sql_str = sprintf("insert into player_info(tourney_id, team_id, player_id, isTeamLeader) values(%d, %d, %d, %d)", $tid, $this->team_id, $pid, $itl) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function hasPlayer($tid, $pid)
    {
      $sql_str = sprintf("select 1 from player_info where tourney_id=%d and team_id=%d and player_id=%d", $tid, $this->team_id, $pid) ;
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
      if (!isset($col) || !isset($val))
	{
	  return null ;
	}

      if ($col=="password")
	{
	  $this->$col = md5($val) ;
	}
      else
	{
	  $this->$col = util::mysql_real_escape_string($val) ;
	}

      if (is_numeric($val))
	{
	  $sql_str = sprintf("update team set %s=%d where team_id=%d", $col, $this->$col, $this->team_id) ;
	}
      else
	{
	  $sql_str = sprintf("update team set %s='%s' where team_id=%d", $col, $this->$col, $this->team_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from team where team_id=%d", $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
