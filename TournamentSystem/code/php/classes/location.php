<?php
class location
{
  private $location_id ;
  private $country_name ;
  private $logo_url ;

  function __construct($a)
    {
      if (array_key_exists('location_id', $a))
	{
	  $this->location_id = $this->validateColumn($a['location_id'], 'location_id') ;

	  if ($this->getLocationInfo()==util::NOTFOUND)
	    {
	      util::throwException("No location exists with specified id");
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

      $sql_str = sprintf("insert into location(country_name, logo_url)" .
                         "values('%s', '%s', '%s')",
			 $this->country_name, $this->logo_url) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->location_id = mysql_insert_id() ;
    }

  private function getLocationInfo()
    {
      $sql_str = sprintf("select country_name, logo_url from location where location_id=%d", $this->location_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->country_name  = $row[0] ;
      $this->logo_url      = $row[1] ; 

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
      if ($col == 'location_id')
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

      elseif ($col == 'country_name')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      /*
      elseif ($col == 'state_name')
	{
	  return util::mysql_real_escape_string($val) ;
	}
      */

      elseif ($col = 'logo_url')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public static function getAllLocations($a)
    {
      $sql_str = sprintf('select * from location l') ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysql_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new location(array('location_id'=>$row['location_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new location(array('location_id'=>$row['location_id'])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTeams()
    {
      $sql_str = sprintf("select t.team_id from team t where t.location_id", $this->location_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new team(array('team_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getPlayers()
    {
      $sql_str = sprintf("select p.player_id from player p where p.location_id", $this->location_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
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
	  $sql_str = sprintf("update location set %s=%d where location_id=%d", $col, $this->$col, $this->location_id) ;
	}
      else
	{
	  $sql_str = sprintf("update location set %s='%s' where location_id=%d", $col, $this->$col, $this->location_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from location where location_id=%d", $this->location_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
