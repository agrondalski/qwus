<?php

class util
{
  const _DEBUG   = 0 ;

  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  // QW Stats
  const NAME         = 'Name' ;
  const SCORE        = 'Score' ;
  const RANK         = 'Rank' ;
  const EFFICIENCY   = 'Efficiency' ;
  const MATCHED      = 'Matched' ;
  const TOTAL_DEATHS = 'Total Deaths' ;
  const TOTAL_FRAGS  = 'Total Frags' ;
  const AX_FRAGS     = 'Ax Frags';
  const AX_DEATHS    = 'Ax Deaths';
  const SG_FRAGS     = 'Shotgun Frags';
  const SG_DEATHS    = 'Shotgun Deaths';
  const SSG_FRAGS    = 'SSG Frags';
  const SSG_DEATHS   = 'SSG Deaths';
  const NG_FRAGS     = 'Nailgun Frags';
  const NG_DEATHS    = 'Nailgun Deaths';
  const SNG_FRAGS    = 'SNG Frags';
  const SNG_DEATHS   = 'SNG Deaths';
  const GL_FRAGS     = 'Grenade Frags';
  const GL_DEATHS    = 'Grenade Deaths';
  const RL_FRAGS     = 'Rocket Frags';
  const RL_DEATHS    = 'Rocket Deaths';
  const LG_FRAGS     = 'LG Frags';
  const LG_DEATHS    = 'LG Deaths';
  const TELE_FRAGS   = 'Tele Frags';
  const TELE_DEATHS  = 'Tele Deaths';
  const DIS_FRAGS    = 'Discharge Frags';
  const DIS_DEATHS   = 'Discharge Deaths';
  const DIS_BORES    = 'Discharge Bores';
  const SQ_FRAGS     = 'Squish Frags';
  const SQ_DEATHS    = 'Squish Deaths';
  const SQ_BORES     = 'Squish Bores';
  const LAVA_BORES   = 'Lava Bores';
  const SLIME_BORES  = 'Slime Bores';
  const WATER_BORES  = 'Water Bores';
  const FALL_BORES   = 'Fall Bores';
  const MISC_BORES   = 'Misc Bores';
  const GL_BORES     = 'Grenade Bores';
  const RL_BORES     = 'Rocket Bores';
  const SELF_KILLS   = 'Self Kills';
  const TEAM_KILLS   = 'Team Kills';
  const FRAG_STREAK  = 'Frag Streak';
  // Team Only Stats
  const LG_ACCURACY 	= 'LG Accuracy';				 
  const DIRECT_ROCKETS	= 'Direct Rockets';				 
  const SSG_ACCURACY	= 'SSG Accuracy';				 
  const SG_ACCURACY 	= 'SG Accuracy'; 				   								
  const QUADS 			= 'Quads'; 				 
  const PENTS 			= 'Pents'; 					 
  const RINGS 			= 'Rings'; 						   								
  const RED_ARMORS		= 'Red Armors';				 
  const YELLOW_ARMORS 	= 'Yellow Armors'; 				 
  const GREEN_ARMORS	= 'Green Armors';				   								
  const DAMAGE_GIVEN	= 'Damage Given';				 
  const DAMAGE_TAKEN	= 'Damage Taken';				   								
  const MINUTESPLAYED 	= 'MinutesPlayed'; 				 
  const MINUTESWITHLEAD = 'MinutesWithLead';	

  // QWCTF Stats
  const CAPTURES	    =  'Captures';
  const CAPTURE_TIMES   =  'Capture Times' ;
  const CARRIER_DEFENDS =  'Carrier Defends';
  const FLAG_DEFENDS 	=  'Flag Defends';
  const FLAG_DROPS	    =  'Flag Drops';
  const FLAG_RETURNS 	=  'Flag Returns';
  const FRAG_ASSISTS 	=  'Frag Assists';
  const RETURN_ASSISTS 	=  'Return Assists';
  const GRAPPLE_FRAGS	=  'Grapple Frags';
  const GRAPPLE_DEATHS	=  'Grapple Deaths';
  const CARRIER_FRAGS	=  'Carrier Frags';  
  const FLAG_TIME	    =  'Flag Time';  

  const POINTS          = 'Points' ;
  const GAMES_PLAYED    = 'Games_Played' ;
  const GAMES_WON       = 'Games_Won' ;
  const GAMES_LOST      = 'Games_Lost' ;
  const MATCHES_PLAYED  = 'Matches_Played' ;
  const MATCHES_WON     = 'Matches_Won' ;
  const MATCHES_LOST    = 'Matches_Lost' ;
  const TOTAL_SCORE     = 'Total_Score' ;
  const TOTAL_SCORE_OPP = 'Total_Score_Opp' ;
  const SCORE_PER_GAME  = 'Score_Per_Game';
  const FRAGS_PER_GAME  = 'Frags_Per_Game';
  const SCORE_DIFF      = 'Score_Diff' ;

  const WINNING_STREAK     = 'Winning_Streak' ;
  const LOSING_STREAK      = 'Losing_Streak' ;
  const MAX_WINNING_STREAK = 'Max_Winning_Streak' ;
  const MAX_LOSING_STREAK  = 'Max_Losing_Streak' ;
  const MAX_SCORE          = 'Max_Score' ;
  const MIN_SCORE          = 'Min_Score' ;
  
  const FORFEIT_MAP        = 'Forfeit' ;

  const DEFAULT_DATE = '0000-00-00' ;
  const DEFAULT_TIME = '00:00:00' ;
  const DEFAULT_INT  = -1 ;
  const DEFAULT_STR  = null ;

  const SLASH          = '/' ;
  const ROOT_DIR       = '/usr/quake/demos/tourney' ;
  const HTML_ROOT_DIR  = 'http://www.quakeworld.us/tourney' ;

  const UPLOAD_DIR     = '/usr/quake/demos/tourney/';

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
      if (!isset($v1) || util::isNull($v1))
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

      $i = similar_text($s1, $s2, $c) ;
      return $c ;
      //return levenshtein($s1, $s2) ;
    }

  public static function stringMatch($a1, $a2)
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
	  $max = -1 ;

	  foreach($matx as $k1=>$e1)
	    {
	      foreach($e1 as $k2=>$e2)
		{
		  if ($matx[$k1][$k2] > $max || $max==-1)
		    {
		      $max = $matx[$k1][$k2] ;
		      $idx1 = $k1 ;
		      $idx2 = $k2 ;

		      if ($max==100)
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

  public static function matchTeamsByPlayers($t1, $t2, $abbr, $p1_game, $p2_game, $tid)
    {
      $p1_real = $t1->getPlayers($tid) ;
      $p2_real = $t2->getPlayers($tid) ;

      $matx = array() ;
      $score_11 = 0 ;
      $score_22 = 0 ;
      $score_12 = 0 ;
      $score_21 = 0 ;

      foreach($p1_game as $k1=>$e1)
	{
	  foreach($p1_real as $k2=>$e2)
	    {
	      $matx[$k2]  = self::getStringMatchScore(strtolower($e1), strtolower($e2->getValue('name'))) ;
	    }
	  $score_11 += max($matx) ;
	}

      foreach($p2_game as $k1=>$e1)
	{
	  foreach($p2_real as $k2=>$e2)
	    {
	      $matx[$k2]  = self::getStringMatchScore(strtolower($e1), strtolower($e2->getValue('name'))) ;
	    }
	  $score_22 += max($matx) ;
	}

      foreach($p1_game as $k1=>$e1)
	{
	  foreach($p2_real as $k2=>$e2)
	    {
	      $matx[$k2]  = self::getStringMatchScore(strtolower($e1), strtolower($e2->getValue('name'))) ;
	    }
	  $score_12 += max($matx) ;
	}

      foreach($p2_game as $k1=>$e1)
	{
	  foreach($p1_real as $k2=>$e2)
	    {
	      $matx[$k2]  = self::getStringMatchScore(strtolower($e1), strtolower($e2->getValue('name'))) ;
	    }
	  $score_21 += max($matx) ;
	}

      if (($score_11+$score_22) > ($score_12+$score_21))
	{
	  $ret = array($abbr[0]=>$t1->getValue('name_abbr'), $abbr[1]=>$t2->getValue('name_abbr')) ;
	}
      else
	{
	  $ret = array($abbr[1]=>$t1->getValue('name_abbr'), $abbr[0]=>$t2->getValue('name_abbr')) ;
	}

      return $ret ;
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

  public static function mkdir($target)
    {
      if (!is_string($target))
	{
	  return false ;
	}

      do
	{
	  $dir = $target;

	  while (!mkdir($dir, 0755))
	    {
	      $dir = dirname($dir);
	  
	      if ($dir == '/' || is_dir($dir))
		break;
	    }
	} while ($dir != $target) ;

      if (is_dir($target))
	{
	  return true ;
	}
      else
	{
	  return false ;
	}
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
		  if ($filename!='.' && $filename!='..')
		    {
		      util::delete_files($target."/".$filename, $exceptions);
		    }
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

  public static function html_encode($s)
    {
      if (self::isNull($s))
	{
	  return null ;
	}

      $ents = array('[b]'   => '<b>',
		    '[/b]'  => '</b>',
		    '[br]'  => '<br>',
		    //'[/br]' => '</br>',
		    '
'  => '<br>',
		    '[i]'   => '<i>',
		    '[/i]'  => '</i>',
		    '[p]'   => '<p>',
		    '[/p]'  => '</p>',
		    '[strike]' => '<strike>',
		    '[/strike]' => '</strike>',
		    '[u]'   => '<u>',
		    '[/u]'  => '</u>') ;

      return strtr($s, $ents) ;
    }
}
?>
