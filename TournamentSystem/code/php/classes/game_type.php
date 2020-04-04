<?php
class game_type
{
  private $game_type_id ;
  private $name ;

  function __construct($a)
    {
      if (array_key_exists('game_type_id', $a))
	{
	  $this->game_type_id = $this->validateColumn($a['game_type_id'], 'game_type_id') ;

	  if ($this->getGameTypeInfo()==util::NOTFOUND)
	    {
	      util::throwException("No game_type exists with specified id");
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

      $sql_str = sprintf("insert into game_type(name)" .
                         "values('%s')",
			 $this->name) ;

      $result = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->game_type_id = mysql_insert_id() ;
    }

  private function getGameTypeInfo()
    {
      $sql_str = sprintf("select name from game_type where game_type_id=%d", $this->game_type_id) ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS[link]));

      if (mysqli_num_rows($result)!=1)
	{
	  mysqli_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysqli_fetch_row($result) ;

      $this->name  = $row[0] ;

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
      if ($col == 'game_type_id')
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
    }

  public function addMap($a)
    {
      $a['game_type_id'] = $this->game_type_id ;
      $m = new map($a) ;
    }

  public static function getAllGameTypes($a)
    {
      $sql_str = sprintf('select * from game_type gt') ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS[link]));

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new game_type(array('game_type_id'=>$row['game_type_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new game_type(array('game_type_id'=>$row['game_type_id'])) ;
	    }
	}

      mysqli_free_result($result) ;
      return $arr ;
    }

  public function getTournys()
    {
      $sql_str = sprintf("select t.tourney_id from tourney t where t.game_type_id=%d", $this->game_type_id) ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS[link]));

      while ($row=mysqli_fetch_row($result))
	{
	  $arr[] = new tourney(array('tourney_id'=>$row[0])) ;
	}

      mysqli_free_result($result) ;
      return $arr ;
    }

  public function getMaps()
    {
      $sql_str = sprintf("select m.map_id from maps m where m.game_type_id=%d", $this->game_type_id) ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS[link]));

      while ($row=mysqli_fetch_row($result))
	{
	  $arr[] = new map(array('map_id'=>$row[0])) ;
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
	  $sql_str = sprintf("update game_type set %s=%d where game_type_id=%d", $col, $this->$col, $this->game_type_id) ;
	}
      else
	{
	  $sql_str = sprintf("update game_type set %s='%s' where game_type_id=%d", $col, $this->$col, $this->game_type_id) ;
	}

      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS[link]));
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from game_type where game_type_id=%d", $this->game_type_id) ;
      $result  = mysqli_query($GLOBALS[link], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS[link]));      
    }
}
?>
