<?php
class file
{
  private $file_id ;
  private $file_type ;
  private $id ;
  private $file_desc ;
  private $url ;

  const TYPE_GAME = 0 ;
  const TYPE_MATCH = 1 ;

  function __construct($a)
    {
      if (array_key_exists('file_id', $a))
	{
	  $this->file_id = $this->validateColumn($a['file_id'], 'file_id') ;

	  if ($this->getFileInfo()==util::NOTFOUND)
	    {
	      util::throwException("No file exists with specified id");
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

      $sql_str = sprintf("insert into file_table(file_type, id, file_desc, url)" .
                         "values('%s', %s, '%s', '%s')",
			 $this->file_type, util::nvl($this->id, 'null'), $this->file_desc, $this->url) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->file_id = mysql_insert_id() ;
    }

  private function getFileInfo()
    {
      $sql_str = sprintf("select file_type, id, file_desc, url from file_table where file_id=%d", $this->file_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->file_type   = $row[0] ;
      $this->id          = $row[1] ;
      $this->file_desc   = $row[2] ;
      $this->url         = $row[3] ;

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
      $file_type_enum = array(self::TYPE_GAME=>'Game', self::TYPE_MATCH=>'Match') ;

      if ($col == 'file_id')
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

      elseif ($col == 'file_type')
	{
	  if (util::isNull($val))
	    {
	      $val = self::TYPE_GAME ;
	    }
	  
	  if (!is_numeric($val))
	    {
	      util::throwException('invalid value specified for ' . $col) ;
	    }

	  return $file_type_enum[$val] ;
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

      elseif ($col == 'file_desc')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'url')
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

  public function getFileTypes()
    {
      $arr = array(self::TYPE_GAME=>'Game', self::TYPE_MATCH=>'Match') ;
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
	  $sql_str = sprintf("update division set %s=%d where division_id=%d", $col, $this->$col, $this->division_id) ;
	}
      else
	{
	  $sql_str = sprintf("update division set %s='%s' where division_id=%d", $col, $this->$col, $this->division_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from file_table where file_id=%d", $this->file_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
