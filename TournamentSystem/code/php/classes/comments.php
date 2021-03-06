<?php
class comment
{
  private $comment_id ;
  private $comment_type ;
  private $id ;
  private $name ;
  private $comment_ip ;
  private $comment_text ;
  private $comment_date ;
  private $comment_time ;

  const TYPE_MATCH = 0 ;
  const TYPE_NEWS =  1 ;
  const TYPE_COLUMN = 2 ;
  const TYPE_COMMENTARY = 3 ;

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

      if ($this->getLastCommentTimeByID($this->id, $this->comment_type)<300 || $this->getLastCommentTime()<30)
	{
	  util::throwException("No spamming comments");
	}

      $sql_str = sprintf("insert into comments(name, comment_type, comment_ip, id, comment_text, comment_date, comment_time)" .
                         "values('%s', '%s', '%s', %s, '%s', '%s', '%s')",
			 $this->name, $this->comment_type, $this->comment_ip, util::nvl($this->id, 'null'), $this->comment_text, $this->comment_date, $this->comment_time) ;

      $result = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link'])) ;
      $this->comment_id = mysql_insert_id() ;
    }

  private function getCommentInfo()
    {
      $sql_str = sprintf("select name, comment_type, id, comment_ip, comment_text, comment_date, comment_time from comments where comment_id=%d", $this->comment_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));

      if (mysqli_num_rows($result)!=1)
	{
	  mysqli_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysqli_fetch_row($result) ;

      $this->name          = $row[0] ;
      $this->comment_type  = $row[1] ; 
      $this->id            = $row[2] ;
      $this->comment_ip     = $row[3] ;
      $this->comment_text  = $row[4] ; 
      $this->comment_date  = $row[5] ; 
      $this->comment_time  = $row[6] ;

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
      $comment_type_enum = array(self::TYPE_MATCH=>'Match', self::TYPE_NEWS=>'News', self::TYPE_COLUMN=>'Column', self::TYPE_COMMENTARY=>'Commentary') ;

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

      elseif ($col == 'comment_ip')
	{
	  if (util::isNull($val))
	    {
	      $val = $_SERVER['REMOTE_ADDR'] ;
	    }

	  if (!util::isValidIP($val))
	    {
	      util::throwException('invalid IP specified for ' . $col) ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'comment_text')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  if (strlen($val)>5000)
	    {
	      util::throwException($col . ' is too long') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'comment_date')
	{
	  if (util::isNull($val))
	    {
	      $val = util::curdate() ;
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
	      $val = util::curtime() ;
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
      $arr = array(self::TYPE_MATCH=>'Match', self::TYPE_NEWS=>'News', self::TYPE_COLUMN=>'Column', self::TYPE_COMMENTARY=>'Commentary') ;
      return $arr ;
    }

  private function getLastCommentTime()
    {
      $sql_str = sprintf("select min(time_to_sec(timediff(concat(curdate(), ' ', curtime()), concat(comment_date, ' ', comment_time)))) from comments where comment_ip='%s'", $_SERVER['REMOTE_ADDR']) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
      
      if ($row = mysqli_fetch_row($result))
	{
	  mysqli_free_result($result) ;
	  return util::nvl($row[0], 1000000000) ;
	}
      else
	{
	  mysqli_free_result($result) ;
	  return 0 ;
	}
    }

  private function getLastCommentTimeByID($cid, $ctype)
    {
      $cid   = $this->validateColumn($cid, 'id') ;
      //$ctype = $this->validateColumn($ctype, 'comment_type') ;

      $sql_str = sprintf("select min(time_to_sec(timediff(concat(curdate(), ' ', curtime()), concat(comment_date, ' ', comment_time))))
                          from comments
                          where id=%d and comment_type='%s' and comment_ip='%s'",
			 $cid, $ctype, $_SERVER['REMOTE_ADDR']) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
      
      if ($row = mysqli_fetch_row($result))
	{
	  mysqli_free_result($result) ;
	  return util::nvl($row[0], 1000000000) ;
	}
      else
	{
	  mysqli_free_result($result) ;
	  return 0 ;
	}
    }

  public function getValue($col, $quote_style=ENT_QUOTES)
    {
      $this->validateColumnName($col) ;

      if ($col=='comment_text')
	{
	  $str = $this->$col ;
	  $str = util::html_encode($str) ;
	  return $str ;
	}
      else
	{
	  return util::htmlentities($this->$col, $quote_style) ;
	}
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

      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from comments where comment_id=%d", $this->comment_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));      
    }
}
?>
