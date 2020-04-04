<?php
class map
{
  private $map_id ;
  private $map_name ;
  private $map_abbr ;
  private $game_type_id ;

  function __construct($a)
    {
      if (array_key_exists('map_id', $a))
	{
	  $this->map_id = $this->validateColumn($a['map_id'], 'map_id') ;

	  if ($this->getMapsInfo()==util::NOTFOUND)
	    {
	      util::throwException("No maps exist with specified id");
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

      $sql_str = sprintf("insert into maps(map_name, map_abbr, game_type_id)" .
                         "values('%s', '%s', '%s')",
			 $this->map_name, $this->map_abbr, $this->game_type_id) ;

      $result = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->map_id = mysql_insert_id() ;
    }

  private function getMapsInfo()
    {
      $sql_str = sprintf("select map_name, map_abbr, game_type_id from maps where map_id=%d", $this->map_id) ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS[link]));

      if (mysqli_num_rows($result)!=1)
	{
	  mysqli_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysqli_fetch_row($result) ;

      $this->map_name      = $row[0] ;
      $this->map_abbr      = $row[1] ;
      $this->game_type_id  = $row[2] ; 

      mysqli_free_result($result) ;

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
      if ($col == 'map_id')
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

      elseif ($col == 'map_name')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'map_abbr')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
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

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public static function getAllMaps()
    {
      $sql_str = sprintf('select m.map_id from maps m') ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS[link]));

      while ($row=mysqli_fetch_row($result))
	{
	  $arr[] = new map(array('map_id'=>$row[0])) ;
	}

      mysqli_free_result($result) ;
      return $arr ;
    }

  public function getGames()
    {
      $sql_str = sprintf("select g.game_id from game g where g.map_id=%d", $this->map_id) ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS[link]));

      while ($row=mysqli_fetch_row($result))
	{
	  $arr[] = new game(array('game_id'=>$row[0])) ;
	}

      mysqli_free_result($result) ;
      return $arr ;
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
	  $sql_str = sprintf("update maps set %s=%d where map_id=%d", $col, $this->$col, $this->map_id) ;
	}
      else
	{
	  $sql_str = sprintf("update maps set %s='%s' where map_id=%d", $col, $this->$col, $this->map_id) ;
	}

      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS[link]));
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from maps where map_id=%d", $this->map_id) ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS[link]));      
    }
}
?>
