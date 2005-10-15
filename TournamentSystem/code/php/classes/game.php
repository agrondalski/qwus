<?php
class game
{
  private $game_id ;
  private $match_id ;
  private $map_id ;
  private $team1_score ;
  private $team2_score ;

  function __construct($a)
    {
      if (array_key_exists('game_id', $a))
	{
	  $this->game_id = $this->validateColumn($a['game_id'], 'game_id') ;

	  if ($this->getGameInfo()==util::NOTFOUND)
	    {
	      util::throwException("No game exists with specified id");
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

      $sql_str = sprintf("insert into game(match_id, map_id, team1_score, team2_score)" .
                         "values(%d, %d, %d, %d)",
			 $this->match_id, $this->map_id, $this->team1_score, $this->team2_score) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->game_id = mysql_insert_id() ;
    }

  private function getGameInfo()
    {
      $sql_str = sprintf("select match_id, map_id, team1_score, team2_score from game where game_id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->match_id        = $row[0] ;
      $this->map_id          = $row[1] ;
      $this->team1_score     = $row[2] ;
      $this->team2_score     = $row[3] ; 

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
      if ($col == 'game_id')
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

      elseif ($col == 'match_id')
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

      elseif ($col == 'map_id')
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
      
      elseif ($col == 'team1_score')
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
      
      elseif ($col == 'team2_score')
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

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public function getStats($a)
    {
      $sql_str = sprintf("select s.* from stats s where s.game_id=%d", $this->game_id) ;
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
	      $arr[] = new stats(array('player_id'=>$row['player_id'], 'game_id'=>$row['game_id'], 'stat_name'=>$row['stat_name'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;
	  
	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new stats(array('player_id'=>$row['player_id'], 'game_id'=>$row['game_id'], 'stat_name'=>$row['stat_name'])) ;
	    }
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function addStat($a)
    {
      $a['game_id'] = $this->game_id ;
      $s = new stats($a) ;
    }

  public function getPlayerStats($a)
    {
      $d = $this->getDivision() ;
      $stats = stats::getPlayerStats(array('game_id'=>$this->game_id, 'division_id'=>$d->getValue('division_id'))) ;

      return util::row_sort($stats, $a) ;
    }

  public function getTeamStats($a)
    {
      $d = $this->getDivision() ;
      $stats = stats_team::getTeamStats(array('game_id'=>$this->game_id, 'division_id'=>$d->getValue('division_id'))) ;

      return util::row_sort($stats, $a) ;
    }

  public function addTeamStat($a)
    {
      $a['game_id'] = $this->game_id ;
      $s = new stats_team($a) ;
    }

  public function addFile($a)
    {
      $a['id'] = $this->game_id ;
      $a['file_type'] = file::TYPE_GAME ;
      $f = new file($a) ;
    }

  public function getScreenshot($fileloc)
    {
      $ftype = file::validateColumn(file::TYPE_GAME, 'file_type') ;

      $sql_str = sprintf("select f.file_id, f.file_desc from file_table f where f.id=%d and f.file_type='%s' and f.file_desc='%s'",
			 $this->game_id, $ftype, util::SCREENSHOT) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($row=mysql_fetch_row($result))
	{
	  $arr = new file(array('file_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function addScreenshot($fileloc)
    {
      if (util::isNull($fileloc) || !is_string($fileloc))
	{
	  return null ;
	}

      $this->deleteScreenshot() ;

      $map   = $this->getMap() ;
      $m     = $this->getMatch() ;
      $t     = $m->getTourney() ;
      $teams = $m->getTeams() ;
      $team1 = $teams[0] ;
      $team2 = $teams[1] ;

      $dest_root_dir = $t->getTourneyRoot() . util::SLASH . $this->getFileDirectory() ;
      $html_root_dir = $t->getTourneyRootHtml() . util::SLASH . $this->getFileDirectory() ;

      // Create the required directory
      if (!is_dir($dest_root_dir))
	{
	  if (!util::mkdir($dest_root_dir))
	    {
	      util::throwException('unable to create required directory') ;
	    }
	}

      $pinfo = pathinfo($fileloc) ;
      $new_ss_name = $pinfo['basename'] ;
      if (rename($fileloc, $dest_root_dir . util::SLASH . $new_ss_name))      
	{
	  $this->addFile(array('file_desc'=>util::SCREENSHOT, 'url'=>$html_root_dir . util::SLASH . $new_ss_name)) ;
	}
    }

  public function getFiles()
    {
      $ftype = file::validateColumn(file::TYPE_GAME, 'file_type') ;

      $sql_str = sprintf("select f.file_id, f.file_desc from file_table f where f.id=%d and f.file_type='%s'", $this->game_id, $ftype) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[$row[1]] = new file(array('file_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function deleteScreenshot()
    {
      $ss = $this->getScreenshot() ;

      if (util::isNull($ss))
	{
	  return ;
	}

      $t  = $this->getTourney() ;

      $dest_root_dir = $t->getTourneyRoot() . util::SLASH . $this->getFileDirectory() ;
      $html_root_dir = $t->getTourneyRootHtml() . util::SLASH . $this->getFileDirectory() ;

      util::delete_files(str_replace($html_root_dir, $dest_root_dir, $ss->getValue('url'))) ;
      $ss->delete() ;
    }

  public function getTeamPlayers($team_id)
    {
      $team_id = team::validateColumn($team_id, 'team_id') ;

      $sql_str = sprintf("select s.player_id from stats s where s.game_id=%d and s.team_id=%d and s.stat_name='%s' order by value desc", $this->game_id, $team_id, util::SCORE) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getMatch()
    {
      return new match(array('match_id'=>$this->match_id)) ;
    }

  public function getMap()
    {
      return new map(array('map_id'=>$this->map_id)) ;
    }

  public function getTourney()
    {
      $m  = $this->getMatch() ;
      $ms = $m->getMatchSchedule() ;
      $d  = $ms->getDivision() ;

      return $d->getTourney() ;
    }

  public function getDivision()
    {
      $m  = $this->getMatch() ;
      $ms = $m->getMatchSchedule() ;

      return $ms->getDivision() ;
    }

  public function getFileDirectory()
    {
      return 'match_' . $this->match_id . util::SLASH . 'game_' . $this->game_id ;
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
	  $sql_str = sprintf("update game set %s=%d where game_id=%d", $col, $this->$col, $this->game_id) ;
	}
      else
	{
	  $sql_str = sprintf("update game set %s='%s' where game_id=%d", $col, $this->$col, $this->game_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from game where game_id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }

  public function deleteAll()
    {
      $m = $this->getMatch() ;
      $t = $m->getTourney() ;
      util::delete_files($t->getTourneyRoot() . util::SLASH . $this->getFileDirectory()) ;

      $found = false ;
      $dir = $t->getTourneyRoot() . util::SLASH . $m->getFileDirectory() ;
      if (is_dir($dir))
	{
	  foreach(scandir($dir) as $f)
	    {
	      if ($f!='.' && $f!='..')
		{
		  $found = true ;
		}
	    }

	  if (!$found)
	    {
	      rmdir($dir) ;
	    }
	}

      $sql_str = sprintf("delete from stats where game_id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      

      $sql_str = sprintf("delete from stats_team where game_id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      

      $sql_str = sprintf("delete from file_table where id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      

      $sql_str = sprintf("delete from game where game_id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      

      $m->syncMatchInfo() ;
    }
}
?>
