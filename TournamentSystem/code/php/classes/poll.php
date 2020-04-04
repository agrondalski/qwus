<?php
class poll
{
  private $poll_id ;
  private $topic ;
  private $poll_type ;
  private $id ;
  private $isCurrent ;

  const TYPE_MATCH =  0 ;
  const TYPE_NEWS = 1 ;
  const TYPE_TOURNEY = 2 ;

  function __construct($a)
    {
      if (array_key_exists('poll_id', $a))
	{
	  $this->poll_id = $this->validateColumn($a['poll_id'], 'poll_id') ;

	  if ($this->getPollInfo()==util::NOTFOUND)
	    {
	      util::throwException("No poll exists with specified id");
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

      $sql_str = sprintf("insert into poll(topic, poll_type, id, isCurrent)" .
                         "values('%s', '%s', %s, %d)",
			 $this->topic, $this->poll_type, util::nvl($this->id, 'null'), $this->isCurrent) ;

      $result = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->poll_id = mysql_insert_id() ;
    }

  private function getPollInfo()
    {
      $sql_str = sprintf("select topic, poll_type, id, isCurrent from poll where poll_id=%d", $this->poll_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->topic       = $row[0] ;
      $this->poll_tyoe   = $row[1] ;
      $this->id          = $row[2] ;
      $this->isCurrent   = $row[3] ;

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
      $poll_type_enum = array(self::TYPE_MATCH=>'Match', self::TYPE_NEWS=>'News', self::TYPE_TOURNEY=>'Tournament') ;

      if ($col == 'poll_id')
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

      elseif ($col == 'topic')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'poll_type')
	{
	  if (util::isNull($val))
	    {
	      $val = self::TYPE_MATCH ;
	    }
	  
	  if (!is_numeric($val))
	    {
	      util::throwException('invalid value specified for ' . $col) ;
	    }

	  return $poll_type_enum[$val] ;
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

      elseif ($col == 'isCurrent')
	{
	  return util::nvl(util::mysql_real_escape_string($val), true) ;
	}

      elseif ($col == 'option_id')
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

      elseif ($col == 'poll_option')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public function getPollTypes()
    {
      $arr = array(self::TYPE_MATCH=>'Match', self::TYPE_NEWS=>'News', self::TYPE_TOURNEY=>'Tournament') ;
      return $arr ;
    }

  public function addPollOption($popt)
    {
      $ptop = $this->validateColumn($popt, 'poll_option') ;

      $sql_str = sprintf("select max(option_id) from poll_options po where po.poll_id=%d", $this->poll_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $oid = 1 ;
      if ($row=mysql_fetch_row($result))
	{
	  if (!util::isNull($row[0]))
	    {
	      $oid = $row[0] + 1 ;
	    }
	}

      $sql_str = sprintf("insert into poll_options(poll_id, option_id, poll_option, votes) values(%d, %d, '%s', 0)", $this->poll_id, $oid, $popt) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
    }

  public function incPollOption($oid)
    {
      $oid = $this->validateColumn($oid, 'option_id') ;

      $sql_str = sprintf("select 1 from poll_votes pv where pv.poll_id=%d and vote_ip='%s'", $this->poll_id, $_SERVER['REMOTE_ADDR']) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (!($row==mysql_fetch_row($result)))
	{
	  return ;
	}

      $sql_str = sprintf("insert into poll_votes(poll_id, vote_ip) values(%d, '%s')", $this->poll_id, $_SERVER['REMOTE_ADDR']) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sql_str = sprintf("update poll_options po set votes=votes+1 where po.poll_id=%d and po.option_id=%d", $this->poll_id, $oid) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
    }

  public function getPollOptions()
    {
      $sql_str = sprintf("select sum(votes) from poll_options po where po.poll_id=%d", $this->poll_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($row=mysql_fetch_row($result))
	{
	  if (!util::isNull($row[0]))
	    {
	      $total_votes = $row[0] ;
	    }
	}

      $sql_str = sprintf("select option_id, poll_option, votes from poll p, poll_options po where p.poll_id=%d and p.poll_id=po.poll_id", $this->poll_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = array('option_id'=>$row[0], 'poll_option'=>$row[1], 'votes'=>$row[2], 'vote_pct'=>round(($row[2]/$total_votes)*100, 1)) ;
	}

      mysql_free_result($result) ;
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
	  $sql_str = sprintf("update poll set %s=%d where poll_id=%d", $col, $this->$col, $this->poll_id) ;
	}
      else
	{
	  $sql_str = sprintf("update poll set %s='%s' where poll_id=%d", $col, $this->$col, $this->poll_id) ;
	}

      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from poll where poll_id=%d", $this->poll_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
