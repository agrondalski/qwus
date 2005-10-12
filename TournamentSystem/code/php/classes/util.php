<?php

class util
{
  const _DEBUG   = 0 ;

  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  const SCORE = 'Score' ;

  const DEFAULT_DATE = '0000-00-00' ;
  const DEFAULT_TIME = '00:00:00' ;
  const DEFAULT_INT  = -1 ;
  const DEFAULT_STR  = null ;

  const SLASH          = '/' ;
  const ROOT_DIR       = '/usr/quake/demos/tourney' ;
  const HTML_ROOT_DIR  = 'http://www.quakeworld.us/tourney' ;

  const SCREENSHOT              = 'SCREENSHOT' ;
  const MVD_DEMO                = 'MVD_DEMO' ;
  const TEAM_SCORE_GRAPH_SMALL  = 'TEAM_SCORE_GRAPH_SMALL' ;
  const TEAM_SCORE_GRAPH_LARGE  = 'TEAM_SCORE_GRAPH_LARGE' ;
  const PLAYER_SCORE_GRAPH      = 'PLAYER_SCORE_GRAPH' ;
  const PIECHART                = 'PIECHART' ;

  public static function getLimit($a)
    {
      $l = trim($a['limit']) ;

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

  public static function choose($b, $v1, $v2)
    {
      if ($b)
	{
	  return $v1 ;
	}

      return $v2 ;
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

  public static function throwException($m, $exc_type=null)
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
      return self::htmlentities($s) ;
    }

  public static function htmlentities($s, $quote_style=ENT_QUOTES)
    {
      if ($quote_style!=ENT_COMPAT && $quote_style!=ENT_QUOTES && $quote_style!=ENT_NOQUOTES)
	{
	  self::throwException('invalid quote_style value') ;
	}

      return htmlentities($s, $quote_style) ;
    }

  public static function strtolower($s)
    {
      if (!is_string($s))
	{
	  return $s ;
	}

      return strtolower($s) ;
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

  public static function isValidIP($ip)
    {
      if (isset($ip) && !empty($ip))
	{
	  if(preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip))
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

      for ($i=0; $i<count($weeks); $i++) 
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

  function row_sort($data, $sort_a)
    { 
      if (!is_array($data) || !is_array($sort_a) || count($sort_a)==0)
	{ 
	  return $data ;
	} 
  
      $str_idxs = array() ; 
      for ($i=0; $i<count($sort_a); $i++) 
	{ 
	  if (is_string($sort_a[$i])) 
	    { 
	      $str_idxs[] = $i ; 
	    } 
  
	  elseif (!is_integer($sort_a[$i])) 
	    { 
	      return $data ; 
	    } 
	} 

      $s = array() ; 
      foreach($data as $key=>$row) 
	{ 
	  $i=0 ; 
	  foreach($str_idxs as $idx) 
	    { 
	      $s[$i++][$key] = self::strtolower($row[$sort_a[$idx]]) ; 
	    } 
	} 
  
  
      $amlist = null ; 
      $j=0 ; 
      for ($i=0; $i<count($sort_a); $i++) 
	{ 
	  if (array_search($i, $str_idxs)===false) 
	    { 
	      if ($amlist == null) 
		{ 
		  $amlist .= "$sort_a[$i]" ; 
		} 
	      else 
		{ 
		  $amlist .= ",$sort_a[$i]" ; 
		} 
	    } 
	  else 
	    { 
	      if ($amlist == null) 
		{ 
		  $amlist .= '$s[' . $j++ . ']' ; 
		} 
	      else 
		{ 
		  $amlist .= ',$s[' . $j++ . ']' ; 
		} 
	    } 
	} 

      eval('array_multisort(' . $amlist . ',$data);') ; 

      return $data ; 
    }

  public static function strbool($b)
    {
      if ($b)
	{
	  return 'Yes' ;
	}

      return 'No' ;
    }

  public static function getStringMatchScore ($s1, $s2) 
    { 
      if (!is_string($s1) || !is_string($s2))
	{
	  return null ;
	}

      return levenshtein($s1, $s2) ;
    }

  public static function playerMatch($a1, $a2)
    {
      if (!is_array($a1) || !is_array($a2) || count($a1)>count($a2))
	{
	  return null ;
	}

      $matx = array() ;
      foreach($a1 as $k1=>$e1)
	{
	  foreach($a2 as $k2=>$e2)
	    {
	      $matx[$e1][$e2]  = self::getStringMatchScore(strtolower($e1), strtolower($e2)) ;
	    }
	}

      $results = array() ;

      while (count($matx)>0)
	{
	  $min = -1 ;

	  foreach($matx as $k1=>$e1)
	    {
	      foreach($e1 as $k2=>$e2)
		{
		  if ($matx[$k1][$k2] < $min || $min==-1)
		    {
		      $min = $matx[$k1][$k2] ;
		      $idx1 = $k1 ;
		      $idx2 = $k2 ;

		      if ($min==0)
			{
			  break ;
			}
		    }
		}
	    }

	  $results[$idx1] = $idx2 ;

	  unset($matx[$idx1]) ;
	  foreach($matx as $k1=>$e1)
	    {
	      unset($matx[$k1][$idx2]) ;
	    }
	}

      return $results ;
    }

  public static function findBestMatch($a, $s)
    {
      if (!is_array($a) || !is_string($s))
	{
	  return null ;
	}

      $idx_match = -1 ;
      $min = -1 ;

      foreach($a as $k=>$e)
	{
	  $lev = levenshtein(strtolower($e), strtolower($s)) ;

	  if ($lev == 0)
	    {
	      $idx_match = $k ;
	      break ;
	    }

	  if ($lev < $min || $min==-1)
	    {
	      $idx_match = $k ;
	      $min = $lev ;
	    }
	}

      return $idx_match ;
    }


  public static function isLoggedInAsPlayer()
    {
      if (!self::isNull($_SESSION['user_id']))
	{
	  return true ;
	}

      return false ;
    }

  public static function isLoggedInAsTeam()
    {
      if (!self::isNull($_SESSION['team_id']))
	{
	  return true ;
	}

      return false ;
    }


  public static function generateRandomStr($len)
    {
      if (!is_integer($len))
	{
	  return null ;
	}

      $str = '' ;
      for ($i=0; $i<$len; $i++)
	{
	  $str .= self::generateRandomChar() ;
	}

      return $str ;
    }


  public static function generateRandomChar()
    {
      $chars = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',  'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',  'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9');

      return $chars[mt_rand(1,35)] ;

      /*
      $rand = mt_rand(1, 5) ;

      switch ($rand)
	{
	case 1:
	  $val = mt_rand(48, 57) ;
	  break ;

	case 2:
	case 3:
	  $val = mt_rand(65, 90) ;
	  break ;

	case 4:
	case 5:
	  $val = mt_rand(97, 122) ;
	  break ;
	}

      return chr($val) ;
      */
    }

  public static function createTextImage($str)
    {
      header("Content-type: image/png");
      header('Cache-control: no-cache, no-store');
      $img = imagecreate(150, 50);

      $black = imagecolorallocate ($img, 0, 0, 0);
      $white = imagecolorallocate ($img, 255, 255, 255);
      
      imagefill($img, 0, 0, $white) ;

      $width = 0 ;
      imagelayereffect($img, 3) ;
      for ($i=0; $i<strlen($str); $i++)
	{
	  $width += 20 ;
	  imagechar($img, mt_rand(2, 5), $width, mt_rand(15, 20), $str[$i], $black) ;
	}

      imagepng($img) ;
      imagedestroy($img) ;
    }

  public static function delete_files($target, $exceptions)
    {
      $sourcedir = opendir($target);

      while(false !== ($filename = readdir($sourcedir)))
	{
	  if(!in_array($filename, $exceptions))
	    {
	      if(is_dir($target."/".$filename))
		{
		  // recurse subdirectory; call of function recursive
		  util::delete_files($target."/".$filename, $exceptions);
		}
	      else if(is_file($target."/".$filename))
		{
		  // unlink file
		  unlink($target."/".$filename);
		}
	    }
	}

      closedir($sourcedir);

      if(rmdir($target))
	{
	  return true;
	}
      else
	{
	  return false;
	}
    }
}
?>
