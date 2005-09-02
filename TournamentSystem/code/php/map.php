<?php
require_once 'dbConnect.php' ;
?>

<?php
class map
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  private $map_id ;
  private $map_name ;
  private $map_abbr ;
  private $game_type_id ;

  function __construct($a)
    {
      $id = $a['map_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->map_id = $id ;

	  if ($this->getMapsInfo()==self::NOTFOUND)
	    {
	      util::throwException("No maps exist with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      $this->map_name      = util::mysql_real_escape_string($a['map_name']) ;
      $this->map_abbr      = util::mysql_real_escape_string($a['map_abbr']) ;
      $this->game_type_id  = util::mysql_real_escape_string($a['game_type_id']) ;

      $sql_str = sprintf("insert into maps(map_name, map_abbr, game_type_id)" .
                         "values('%s', '%s', '%s')",
			 $this->map_name, $this->map_abbr, $this->game_type_id) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->map_id = mysql_insert_id() ;
    }

  private function getMapsInfo()
    {
      $sql_str = sprintf("select map_name, map_abbr, game_type_id from maps where map_id=%d", $this->map_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->map_name      = $row[0] ;
      $this->map_abbr      = $row[1] ;
      $this->game_type_id  = $row[2] ; 

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public function getGames()
    {
      $sql_str = sprintf("select g.game_id from game g where g.map_id=%d", $this->map_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new game(array('game_id'=>$row[0])) ;
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
	  $sql_str = sprintf("update map set %s=%d where map_id=%d", $col, $this->$col, $this->map_id) ;
	}
      else
	{
	  $sql_str = sprintf("update map set %s='%s' where map_id=%d", $col, $this->$col, $this->map_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from maps where map_id=%d", $this->map_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
