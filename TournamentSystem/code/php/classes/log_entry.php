<?php
class log_entry
{
  private $log_id ;
  private $type ;
  private $str ;
  private $logged_ip ;
  private $log_date ;
  private $log_time ;

  function __construct($a)
    {
      if (array_key_exists('log_id', $a))
	{
	  $this->log_id = $this->validateColumn($a['log_id'], 'log_id') ;

	  if ($this->getLogEntryInfo()==util::NOTFOUND)
	    {
	      util::throwException("No log entry exists with specified id");
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

      $sql_str = sprintf("insert into log_table(type, str, logged_ip, log_date, log_time)" .
                         "values('%s', '%s', '%s', '%s', '%s')",
			 $this->type, $this->str, $this->logged_ip, $this->log_date, $this->log_time) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->log_id = mysql_insert_id() ;
    }

  private function getLogEntryInfo()
    {
      $sql_str = sprintf("select type, str, logged_ip, log_date, log_time from log_table where log_id=%d", $this->log_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->type       = $row[0] ;
      $this->str        = $row[1] ;
      $this->logged_in  = $row[2] ;
      $this->log_date   = $row[3] ; 
      $this->log_time   = $row[4] ;

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
      if ($col == 'log_id')
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

      elseif ($col == 'type')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'str')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'logged_ip')
	{
	  return util::nvl(util::mysql_real_escape_string($val), '0.0.0.0') ;
	}

      elseif ($col == 'log_date')
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

      elseif ($col == 'log_time')
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
	  $sql_str = sprintf("update log_table set %s=%d where log_id=%d", $col, $this->$col, $this->log_id) ;
	}
      else
	{
	  $sql_str = sprintf("update log_table set %s='%s' where log_id=%d", $col, $this->$col, $this->log_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from log_table where log_id=%d", $this->log_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
