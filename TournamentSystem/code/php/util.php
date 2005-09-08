<?php

class util
{
  const _DEBUG = 0 ;

  const DEFAULT_DATE = '0000-00-00' ;
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
	  return ' limit ' . $l ;
	}
    }

  public static function getOrderBy($a)
    {
      if (isset($a['order']))
	{
	  $o = ' order by ' . $a['order'] ;

	  if (isset($a['desc']) && strtoupper($a['desc'])=='YES')
	    {
	      $o .= ' desc' ;
	    }

	  return $o ;
	}
    }

  public static function mysql_real_escape_string($s)
    {
      if (!isset($s) || empty($s))
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

  public static function canNotBeNull($a, $c)
    {
      if (util::isNull($a[$c]))
	{
	  self::throwException($c . ' cannot be null') ;
	}
    }

  public static function throwException($m)
    {
      if (! self::_DEBUG)
	{
	  throw new Exception($m) ;
	}

      die($m) ;
    }

  public static function htmlstring($s)
    {
      return htmlentities($s) ;
    }
}

?>
