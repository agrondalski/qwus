<?php
class comment
{
  private $comment_id ;
  private $comment_type ;
  private $id ;
  private $name ;
  private $player_ip ;
  private $comment_text ;
  private $comment_date ;
  private $comment_time ;

  const TYPE_MATCH = 0 ;
  const TYPE_NEWS =  1 ;
  const TYPE_COLUMN = 2 ;

  function __construct($a)
    {
      if (array_key_exists('comment_id', $a))
	{
	  $this->comment_id = $this->validateColumn($a['comment_id'], 'comment_id') ;

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

      $sql_str = sprintf("insert into comments(name, comment_type, player_ip, id, comment_text, comment_date, comment_time)" .
                         "values('%s', '%s', '%s', %s, '%s', '%s', '%s')",
			 $this->name, $this->comment_type, $this->player_ip, util::nvl($this->id, 'null'), $this->comment_text, $this->comment_date, $this->comment_time) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->comment_id = mysql_insert_id() ;
    }

  private function getCommentInfo()
    {
      $sql_str = sprintf("select name, comment_type, id, player_ip, comment_text, comment_date, comment_time from comments where comment_id=%d", $this->comment_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name          = $row[0] ;
      $this->comment_type  = $row[1] ; 
      $this->id            = $row[2] ;
      $this->player_ip     = $row[3] ;
      $this->comment_text  = $row[4] ; 
      $this->comment_date  = $row[5] ; 
      $this->comment_time  = $row[6] ;

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
      $comment_type_enum = array(self::TYPE_MATCH=>'Match', self::TYPE_NEWS=>'News', self::TYPE_COLUMN=>'Column') ;

      if ($col == 'comment_id')
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

      elseif ($col == 'comment_type')
	{
	  if (util::isNull($val))
	    {
	      $val = self::TYPE_MATCH ;
	    }
	  
	  if (!is_numeric($val))
	    {
	      util::throwException('invalid value specified for ' . $col) ;
	    }

	  return $comment_type_enum[$val] ;
	}

      elseif ($col == 'id')
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
	  return util::nvl(util::mysql_real_escape_string($val), $_SERVER['REMOTE_ADDR']) ;
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

  public function getCommentTypes()
    {
      $arr = array(self::TYPE_MATCH=>'Match', self::TYPE_NEWS=>'News', self::TYPE_COLUMN=>'Column') ;
      return $arr ;
    }

  public function getValue($col, $quote_style=ENT_QUOTES)
    {
      $this->validateColumnName($col) ;

      if ($quote_style!=ENT_COMPAT && $quote_style!=ENT_QUOTES && $quote_style!=ENT_NOQUOTES)
	{
	  util::throwException('invalid quote_style value') ;
	}

      return htmlentities($this->$col, $quote_style) ;
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
      mysql_free_result($result) ;

      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from comments where comment_id=%d", $this->comment_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
