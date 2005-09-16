<?php

class util
{
  const _DEBUG   = 0 ;

  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  const DEFAULT_DATE = '0000-00-00' ;
  const DEFAULT_TIME = '00:00:00' ;
  const DEFAULT_INT  = -1 ;
  const DEFAULT_STR  = null ;

  public static function getLimit($a)
    {
      $l = ltrim(rtrim($a['limit'])) ;

      if (isset($l) && !empty($l))
	{
	  if(!preg_match('/^([0-9]+[ ]*,){1}[ ]*[0-9]*$/', $l) && !preg_match('/^[0-9]+[ ]+OFFSET[ ]+[0-9]+$/', $l))
	    {
	      self::throwException("Invalid limit clause") ;
	    }
	  return self::mysql_real_escape_string(' limit ' . $l) ;
	}

      return null ;
    }

  public static function getOrderBy($a)
    {
      if (isset($a['order']))
	{
	  $o = ' order by ' . $a['order'] ;
	  return self::mysql_real_escape_string($o) ;
	}

      return null ;
    }

  public static function mysql_real_escape_string($s)
    {
      if (!isset($s) || $s==="")
	{
	  return null ;
	}

      return mysql_real_escape_string($s) ;
    }

  public static function nvl($v1, $v2)
    {
      if (!isset($v1))
	{
	  return $v2 ;
	}

      return $v1 ;
    }

  public static function isNull($v)
    {
      if (!isset($v) || $v==="")
	{
	  return true ;
	}

      return false ;
    }

  public static function throwSQLException($m)
    {
      self::throwException($m, 'SQL') ;
    }

  public static function throwException($m, $exc_type)
    {
      if (!self::isNull($exc_type))
	{
	  $l = new log_entry(array('type'=>$exc_type, 'str'=>$m, 'logged_ip'=>$_SERVER['REMOTE_ADDR'], 'log_date'=>self::curdate(), 'log_time'=>self::curtime()));
	}

      if (! self::_DEBUG)
	{
	  throw new Exception($m) ;
	}
      else
	{
	  die($m) ;
	}
    }

  public static function htmlstring($s)
    {
      return htmlentities($s) ;
    }

  public static function curdate()
    {
      return date('Y-m-d', time()) ;
    }

  public static function isValidDate($d)
    {
      if (isset($d) && !empty($d))
	{
	  if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $d))
	    {
	      return true ;
	    }
	}

      return false ;
    }

  public static function curtime()
    {
      return date('H:i:s', time()) ;
    }

  public static function isValidTime($d)
    {
      if (isset($d) && !empty($d))
	{
	  if(preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $d))
	    {
	      return true ;
	    }
	}

      return false ;
    }

  public static function random_integer($val)
    {
      srand() ;
      return rand(0, $val-1) ;
    }

  public static function array_value_count($a, $val)
    {
      $count=0 ;
      for ($i=0; $i<count($a); $i++)
	{
	  if ($a[$i] == $val)
	    {
	      $count++ ;
	    }
	}

      return $count ;
    }

  public static function findbestweek($weeks, $start_idx, $t1, $t2)
    {
      $min     = count($weeks, COUNT_RECURSIVE)+1 ; 
      $min_idx = $start_idx ; 
      $j       = $start_idx ; 

      for($i=0; $i<count($weeks); $i++) 
	{ 
	  $curval = util::array_value_count($weeks[$j], $t1) + util::array_value_count($weeks[$j], $t2) ; 

	  if ($curval < $min) 
	    { 
	      $min = $curval ; 
	      $min_idx = $j ; 
	    } 

	  if (++$j==count($weeks)) 
	    { 
	      $j = 0 ; 
	    } 
	} 

      return $min_idx ; 
    }

  public static function masort_asc($arr, $sort_key)
    {
      usort($arr, create_function('$a, $b', "return (int)\$a['" . $sort_key . "']>(int)\$b['" . $sort_key . "'];")) ;
      return $arr ;
    }

  public static function masort_desc($arr, $sort_key)
    {
      usort($arr, create_function('$a, $b', "return (int)\$a['" . $sort_key . "']<(int)\$b['" . $sort_key . "'];")) ;
      return $arr ;
    }
}

?>
