<?php

class util
{
  const _DEBUG = 0 ;

  public static function getLimit($a)
    {
      $l = ltrim(rtrim($a['limit'])) ;

      if (isset($l))
	{
	  if(!preg_match('/^([0-9]+[ ]*,){1}[ ]*[0-9]*$/', $l) && !preg_match('/^[0-9]+[ ]+OFFSET[ ]+[0-9]+$/', $l))
	    {
	      throw new Exception("Invalid limit clause") ;
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

  public static function throwException($m)
    {
      if (! self::_DEBUG)
	{
	  throw new Exception($m) ;
	}

      die($m) ;
    }
}

?>
