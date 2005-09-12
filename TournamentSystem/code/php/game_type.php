<?php
require_once 'dbConnect.php' ;
?>

<?php
class game_type
{
  private $game_type_id ;
  private $name ;

  function __construct($a)
    {
      $id = $a['game_type_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->game_type_id = $id ;

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

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->game_type_id = mysql_insert_id() ;
    }

  private function getGameTypeInfo()
    {
      $sql_str = sprintf("select name from game_type where game_type_id=%d", $this->game_type_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name  = $row[0] ;

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
      if ($col == 'game_type_id')
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
    }

  public static function getAllGameTypes()
    {
      $sql_str = sprintf('select gt.game_type_id from game_type gt') ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new game_type(array('game_type_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTournys()
    {
      $sql_str = sprintf("select t.tourney_id from tourney t where t.game_type_id=%d", $this->game_type_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new tourney(array('tourney_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getMaps()
    {
      $sql_str = sprintf("select m.map_id from maps m where m.game_type_id=%d", $this->game_type_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new map(array('map_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
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
	  $sql_str = sprintf("update game_type set %s=%d where game_type_id=%d", $col, $this->$col, $this->game_type_id) ;
	}
      else
	{
	  $sql_str = sprintf("update game_type set %s='%s' where game_type_id=%d", $col, $this->$col, $this->game_type_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from game_type where game_type_id=%d", $this->game_type_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
