<?php
require_once 'dbConnect.php' ;
?>

<?php
class player
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  private $player_id ;
  private $name ;
  private $superAdmin ;
  private $location_id ;
  private $password ;

  function __construct($a)
    {
      if (count($a)==1)
	{
	  $id = $a['player_id'] ;
	  if(isset($id) && is_numeric($id))
	    {
	      $this->player_id = $id ;
	      
	      if ($this->getPlayerInfo()==self::NOTFOUND)
		{
		  throw new Exception("No player exists with specified id");
		}
	      else
		{
		  return ;
		}
	    }

	  $this->name = mysql_real_escape_string($a['name']) ;
	      
	  if ($this->getPlayerInfoByName()==self::NOTFOUND)
	    {
	      throw new Exception("No player exists with specified name");
	    }
	      else
		{
		  return ;
		}
	}

      $this->name         = mysql_real_escape_string($a['name']) ;
      $this->superAdmin   = mysql_real_escape_string($a['superAdmin']) ;
      $this->location_id  = mysql_real_escape_string($a['location_id']) ;
      $this->password     = md5($a['password']) ;

      $sql_str = sprintf("insert into player(name, superAdmin, location_id, password)" .
                         "values('%s', %d, %d, '%s')",
			 $this->name, $this->superAdmin, $this->location_id, $this->password) ;

      $result = mysql_query($sql_str) or die ("Unable to execute : $sql_str " . $mysql_error) ;
      $this->player_id = mysql_insert_id() ;
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

  private function getPlayerInfo()
    {
      $sql_str = sprintf("select name, superAdmin, location_id, password from player where player_id=%d", $this->player_id) ;
      $result  = mysql_query($sql_str) or die ("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name         = $row[0] ;
      $this->superAdmin   = $row[1] ;
      $this->location_id  = $row[2] ;
      $this->password     = $row[3] ; 

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  private function getPlayerInfoByName()
    {
      $sql_str = sprintf("select player_id, superAdmin, location_id, password from player where name='%s'", $this->name) ;
      $result  = mysql_query($sql_str) or die ("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->player_id    = $row[0] ;
      $this->superAdmin   = $row[1] ;
      $this->location_id  = $row[2] ;
      $this->password     = $row[3] ; 

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public function getStats()
    {
      $sql_str = sprintf("select s.game_id from stats s where s.player_id=%d", $this->player_id) ;
      $result  = mysql_query($sql_str) or die ("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new stats(array('player_id'=>$this->player_id, 'game_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getLocation()
    {
      return new location(array('location_id'=>$this->location_id)) ;
    }

  function getValue($col)
    {
      if (! isset($col) || !isset($this->$col))
	{
	  return ;
	}      

      return $this->$col ;
    }

  function update($col, $val)
    {
      if (! isset($col) || !isset($val) || !isset($this->$col))
	{
	  return ;
	}

      if ($col=="password")
	{
	  $this->$col = md5($val) ;
	}
      else
	{
	  $this->$col = mysql_real_escape_string($val) ;
	}

      if (is_numeric($val))
	{
	  $sql_str = sprintf("update player set %s=%d where player_id=%d", $col, $this->$col, $this->player_id) ;
	}
      else
	{
	  $sql_str = sprintf("update player set %s='%s' where player_id=%d", $col, $this->$col, $this->player_id) ;
	}

      $result  = mysql_query($sql_str) or die ("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from player where player_id=%d", $this->player_id) ;
      $result  = mysql_query($sql_str) or die ("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
