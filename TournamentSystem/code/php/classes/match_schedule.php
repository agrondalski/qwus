<?php
class match_schedule
{
  private $schedule_id ;
  private $division_id ;
  private $name ;
  private $deadline ;

  function __construct($a)
    {
      if (array_key_exists('schedule_id', $a))
	{
	  $this->schedule_id = $this->validateColumn($a['schedule_id'], 'schedule_id') ;

	  if ($this->getMatchScheduleInfo()==util::NOTFOUND)
	    {
	      util::throwException("No match_schedule exists with specified id");
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

      $sql_str = sprintf("insert into match_schedule(division_id, name, deadline)" .
                         "values(%d, '%s', '%s')",
                         $this->division_id, $this->name, $this->deadline) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->schedule_id = mysql_insert_id() ;
    }

  private function getMatchScheduleInfo()
    {
      $sql_str = sprintf("select division_id, name, deadline from match_schedule where schedule_id=%d", $this->schedule_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->division_id  = $row[0] ;
      $this->name         = $row[1] ;
      $this->deadline     = $row[2] ;

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
      if ($col == 'schedule_id')
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

      elseif ($col == 'division_id')
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

      elseif ($col == 'deadline')
	{
	  /*
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  */

	  if (!util::isNull($val) && !util::isValidDate($val))
	    {
	      util::throwException('invalid date specified for ' . $col) ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), util::DEFAULT_DATE) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public function addMatch($a)
    {
      $a['schedule_id'] = $this->schedule_id ;
      $m = new match($a) ;
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
	  $sql_str = sprintf("update match_schedule set %s=%d where schedule_id=%d", $col, $this->$col, $this->schedule_id) ;
	}
      else
	{
	  $sql_str = sprintf("update match_schedule set %s='%s' where schedule_id=%d", $col, $this->$col, $this->schedule_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from match_schedule where schedule_id=%d", $this->schedule_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
