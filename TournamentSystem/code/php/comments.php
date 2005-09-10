<?php
require_once 'dbConnect.php' ;
?>

<?php
class comment
{
  private $comment_id ;
  private $name ;
  private $player_ip ;
  private $match_id ;
  private $comment_text ;
  private $comment_date ;
  private $comment_time ;

  function __construct($a)
    {
      $id = $a['comment_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->comment_id = $id ;

	  if ($this->getCommentInfo()==util::NOTFOUND)
	    {
	      util::throwException("No comment exists with specified id");
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

      $sql_str = sprintf("insert into comments(name, player_ip, match_id, comment_text, comment_date, comment_time)" .
                         "values('%s', '%s', %d, '%s', '%s', '%s')",
			 $this->name, $this->player_ip, $this->match_id, $this->comment_text, $this->comment_date, $this->comment_time) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->comment_id = mysql_insert_id() ;
    }

  private function getCommentInfo()
    {
      $sql_str = sprintf("select name, player_ip, match_id, comment_text, comment_date, comment_time from comments where comment_id=%d", $this->comment_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name          = $row[0] ;
      $this->player_ip     = $row[1] ;
      $this->match_id      = $row[2] ;
      $this->comment_text  = $row[3] ; 
      $this->comment_date  = $row[4] ; 
      $this->comment_time  = $row[5] ;

      mysql_free_result($row) ;

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
      if ($col == 'comment_id')
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

      elseif ($col == 'player_ip')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'match_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'comment_text')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'comment_date')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  if (!util::isValidDate($val))
	    {
	      util::throwException('invalid date specified for ' . $col) ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'comment_time')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  if (!util::isValidTime($val))
	    {
	      util::throwException('invalid time specified for ' . $col) ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public function getMatch()
    {
      return new match(array('match_id'=>$this->match_id)) ;
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
	  $sql_str = sprintf("update comments set %s=%d where comment_id=%d", $col, $this->$col, $this->comment_id) ;
	}
      else
	{
	  $sql_str = sprintf("update comments set %s='%s' where comment_id=%d", $col, $this->$col, $this->comment_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from comments where comment_id=%d", $this->comment_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
