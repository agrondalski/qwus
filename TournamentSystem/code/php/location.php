<?php
require_once 'dbConnect.php' ;
?>

<?php
class location
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  private $location_id ;
  private $country_name ;
  private $state_name ;
  private $logo_url ;

  function __construct($a)
    {
      $id = $a['location_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->location_id = $id ;

	  if ($this->getLocationInfo()==self::NOTFOUND)
	    {
	      util::throwException("No location exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      $this->country_name  = util::mysql_real_escape_string($a['country_name']) ;
      $this->state_name    = util::mysql_real_escape_string($a['state_name']) ;
      $this->logo_url      = util::mysql_real_escape_string($a['logo_url']) ;

      $sql_str = sprintf("insert into location(country_name, state_name, logo_url)" .
                         "values('%s', '%s', '%s')",
			 $this->country_name, $this->state_name, $this->logo_url) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->location_id = mysql_insert_id() ;
    }

  private function getLocationInfo()
    {
      $sql_str = sprintf("select country_name, state_name, logo_url from location where location_id=%d", $this->location_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->country_name  = $row[0] ;
      $this->state_name    = $row[1] ;
      $this->logo_url      = $row[2] ; 

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public function getTeams()
    {
      $sql_str = sprintf("select t.team_id from team t where t.location_id", $this->location_id) ;
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
      $sql_str = sprintf("select p.player_id from player p where p.location_id", $this->location_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getValue($col)
    {
      if (! isset($col) || !isset($this->$col))
	{
	  return ;
	}      

      return $this->$col ;
    }

  public function update($col, $val)
    {
      if (! isset($col) || !isset($val) || !isset($this->$col))
	{
	  return ;
	}

      $this->$col = util::mysql_real_escape_string($val) ;

      if (is_numeric($val))
	{
	  $sql_str = sprintf("update location set %s=%d where location_id=%d", $col, $this->$col, $this->location_id) ;
	}
      else
	{
	  $sql_str = sprintf("update location set %s='%s' where location_id=%d", $col, $this->$col, $this->location_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from location where location_id=%d", $this->location_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
