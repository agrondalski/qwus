<?php
class news
{
  private $news_id ;
  private $writer_id ;
  private $news_type ;
  private $id ;
  private $subject ;
  private $news_date ;
  private $text ;

  const TYPE_NEWS = 0 ;
  const TYPE_TOURNEY = 1 ;
  const TYPE_COLUMN = 2 ;

  function __construct($a)
    {
      if (array_key_exists('news_id', $a))
	{
	  $this->news_id = $this->validateColumn($a['news_id'], 'news_id') ;

	  if ($this->getNewsInfo()==util::NOTFOUND)
	    {
	      util::throwException("No news exists with specified id");
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

      $sql_str = sprintf("insert into news(writer_id, news_type, id, subject, news_date, text)" .
                         "values(%d, '%s', %s, '%s', '%s', '%s')",
			 $this->writer_id, $this->news_type, util::nvl($this->id, 'null'), $this->subject, $this->news_date, $this->text) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->news_id = mysql_insert_id() ;
    }

  private function getNewsInfo()
    {
      $sql_str = sprintf("select writer_id, news_type, id, subject, news_date, text from news where news_id=%d", $this->news_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->writer_id   = $row[0] ;
      $this->news_tyoe   = $row[1] ;
      $this->id          = $row[2] ;
      $this->subject     = $row[3] ;
      $this->news_date   = $row[4] ; 
      $this->text        = $row[5] ;

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
      $news_type_enum = array(self::TYPE_NEWS=>'News', self::TYPE_TOURNEY=>'Tournament', self::TYPE_COLUMN=>'Column') ;

      if ($col == 'news_id')
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

      elseif ($col == 'writer_id')
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

      elseif ($col == 'news_type')
	{
	  if (util::isNull($val))
	    {
	      $val = self::TYPE_NEWS ;
	    }
	  
	  if (!is_numeric($val))
	    {
	      util::throwException('invalid value specified for ' . $col) ;
	    }

	  return $news_type_enum[$val] ;
	}

      elseif ($col == 'id')
	{
	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'subject')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'news_date')
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

      elseif ($col == 'text')
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

  public function getWriter()
    {
      if (isset($this->writer_id))
	{
	  return new player(array('player_id'=>$this->writer_id)) ;
	}
    }

  public static function getNews($a, $l)
    {
      $sql_str = sprintf("select n.* from news n where news_type='News'") ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNUll($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new news(array('news_id'=>$row['news_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new news(array('news_id'=>$row['news_id'])) ;
	    }
	}

      if (is_array($l) && is_integer($l['limit']))
	{
	  $arr = array_slice($arr, 0, $l['limit']) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public static function getNewsCount()
    {
      $sql_str = sprintf("select count(*) from news n where news_type='News'") ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($row = mysql_fetch_row($result))
	{
	  mysql_free_result($result) ;
	  return $row[0] ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return 0 ;
	}
    }

  public function getNewsTypes()
    {
      $arr = array(self::TYPE_NEWS=>'News', self::TYPE_TOURNEY=>'Tournament', self::TYPE_COLUMN=>'Column') ;
      return $arr ;
    }

  public function getComments()
    {
      $sql_str = sprintf("select n.news_id from news n where n.comment_type='NEWS' and n.id=%d order by comment_date, comment_time", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new mews(array('news_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getValue($col)
    {
      $this->validateColumnName($col) ;

      if ($col!="text")
	{
	  return util::htmlstring($this->$col) ;
	}
      else
	{
	  return $this->$col ;
	}
    }

  public function update($col, $val)
    {
      $this->$col = $this->validateColumn($val, $col) ;

      if (is_numeric($this->$col))
	{
	  $sql_str = sprintf("update news set %s=%d where news_id=%d", $col, $this->$col, $this->news_id) ;
	}
      else
	{
	  $sql_str = sprintf("update news set %s='%s' where news_id=%d", $col, $this->$col, $this->news_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from news where news_id=%d", $this->news_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
