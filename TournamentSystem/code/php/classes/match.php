<?php
class match
{
  private $match_id ;
  private $schedule_id ;
  private $team1_id ;
  private $team2_id ;
  private $winning_team_id ;
  private $approved ;
  private $match_date ;

  function __construct($a)
    {
      if (array_key_exists('match_id', $a))
	{
	  $this->match_id = $this->validateColumn($a['match_id'], 'match_id') ;

	  if ($this->getMatchInfo()==util::NOTFOUND)
	    {
	      util::throwException("No match exists with specified id");
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

      $sql_str = sprintf("insert into match_table(schedule_id, team1_id, team2_id, winning_team_id, approved, match_date)" .
                         "values(%d, %d, %d, %s, %d, '%s')",
			 $this->schedule_id, $this->team1_id, $this->team2_id, util::nvl($this->winning_team_id, 'null'), $this->approved, $this->match_date) ;

      $result = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->match_id = mysql_insert_id() ;
    }

  private function getMatchInfo()
    {
      $sql_str = sprintf("select schedule_id, team1_id, team2_id, winning_team_id, approved, match_date from match_table where match_id=%d", $this->match_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($link));

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->schedule_id      = $row[0] ;
      $this->team1_id         = $row[1] ;
      $this->team2_id         = $row[2] ;
      $this->winning_team_id  = $row[3] ; 
      $this->approved         = $row[4] ; 
      $this->match_date       = $row[5] ;

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
      if ($col == 'match_id')
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

      elseif ($col == 'schedule_id')
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

      elseif ($col == 'team1_id')
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

      elseif ($col == 'team2_id')
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

      elseif ($col == 'winning_team_id')
	{
	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'approved')
	{
	  return util::nvl(util::mysql_real_escape_string($val, false)) ;
	}

      elseif ($col == 'match_date')
	{
	  if (!util::isNull($val) && !util::isValidDate($val))
	    {
	      util::throwException('invalid date specified for ' . $col) ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), util::DEFAULT_DATE) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public function addGame($a)
    {
      $a['match_id'] = $this->match_id ;
      $g = new game($a) ;

      $this->update('match_date', util::curdate()) ;
      $this->syncMatchInfo() ;
    }

  public function addGameWithStats($a)
    {
      try
	{
      if (!is_array($a))
	{
	  return ;
	}

      if (!array_key_exists('filename', $a) || !array_key_exists('map', $a) || !array_key_exists('teamStats', $a) ||
	  !array_key_exists('team1', $a) || !array_key_exists('team2', $a) || !array_key_exists('team_score_graph_small', $a) ||
	  !array_key_exists('team_score_graph_large', $a) || !array_key_exists('player_score_graph', $a) || !array_key_exists('playerFields', $a) ||
	  !array_key_exists('PlayerStats', $a) || !array_key_exists('team1players', $a) || !array_key_exists('team2players', $a))
	{
	  util::throwException('not enough info to process stats') ;
	}

      if (!is_file($a['filename']))
	{
	  util::throwException('uploaded file does not exist') ;	  
	}

      if (util::isNull($a['map']))
	{
	  util::throwException('mvdstats.pl probably failed due to a bad mvd') ;
	}

      $t = $this->getTourney() ;

      // Find the map
      $maps = $t->getMaps() ;
      foreach($maps as $m)
	{
	  $maps_abbr[$m->getValue('map_id')] = $m->getValue('map_abbr') ;
	}

      $idx = util::findBestMatch($maps_abbr, $a['map']) ;

      $map = new map(array('map_id'=>$idx)) ;

      $this->update('match_date', util::curdate()) ;

      // Find the players in the game
      $team1_stats_all = explode('\\\\', $a['team1players']) ;
      $team2_stats_all = explode('\\\\', $a['team2players']) ;
      $player_stats    = explode('\\\\', $a['PlayerStats']) ;

      if (is_numeric($a['playerFields']))
	{
	  $field_count = $a['playerFields'] ;
	}
      else
	{
	  util::throwException('invalid data') ;
	}

      if (fmod(count($team1_stats_all), $field_count)!=0 || fmod(count($team1_stats_all), $field_count)!=0)
	{
	  util::throwException('invalid data') ;
	}	  

      $team1_game_players = array() ;
      $team2_game_players = array() ;

      for($cnt=0; $cnt<count($team1_stats_all); $cnt++)      
	{
	  $curheader = $player_stats[fmod($cnt, $field_count)] ;

	  if ($curheader==$player_stats[0])
	    {
	      $team1_game_players[] = $team1_stats_all[$cnt] ;
	    }
	}

      for($cnt=0; $cnt<count($team2_stats_all); $cnt++)      
	{
	  $curheader = $player_stats[fmod($cnt, $field_count)] ;

	  if ($curheader==$player_stats[0])
	    {
	      $team2_game_players[] = $team2_stats_all[$cnt] ;
	    }
	}

      // Find the teams
      $teams = $this->getTeams() ;
      $team1 = $teams[0] ;
      $team2 = $teams[1] ;

      $team_stat_header = explode('\\\\', $a['teamStats']) ;
      $team1_stats_arr = explode('\\\\', $a['team1']) ;
      $team2_stats_arr = explode('\\\\', $a['team2']) ;

      for($cnt=0; $cnt<count($team_stat_header); $cnt++)
	{
	  $h = $team_stat_header[$cnt] ;

	  $team1_stats[$h] = $team1_stats_arr[$cnt] ;
	  $team2_stats[$h] = $team2_stats_arr[$cnt] ;
	}

      /*
      $teamMatch = util::stringMatch(array($team1_stats['Name'], $team2_stats['Name']),
				     array($team1->getValue('name_abbr'), $team2->getValue('name_abbr'))) ;
      */

      $teamMatch = util::matchTeamsByPlayers($team1, $team2, array($team1_stats['Name'], $team2_stats['Name']),
					     $team1_game_players, $team2_game_players, $t->getValue('tourney_id')) ;

      // If needed swap the teams
      if ($teamMatch[$team1_stats['Name']] == $team2->getValue('name_abbr'))
	{
	  $t1 = $a['team1'] ;
	  $t2 = $a['team2'] ;
	  $a['team1'] = $t2 ;
	  $a['team2'] = $t1 ;

	  $t1 = $a['team1players'] ;
	  $t2 = $a['team2players'] ;
	  $a['team1players'] = $t2 ;
	  $a['team2players'] = $t1 ;

	  $t1 = $team1_stats_arr ;
	  $t2 = $team2_stats_arr ;
	  $team1_stats_arr = $t2 ;
	  $team2_stats_arr = $t1 ;

	  $t1 = $team1_stats ;
	  $t2 = $team2_stats ;
	  $team1_stats = $t2 ;
	  $team2_stats = $t1 ;

	  $t1 = $team1_stats_all ;
	  $t2 = $team2_stats_all ;
	  $team1_stats_all = $t2 ;
	  $team2_stats_all = $t1 ;

	  $t1 = $team1_game_players ;
	  $t2 = $team2_game_players ;
	  $team1_game_players = $t2 ;
	  $team2_game_players = $t1 ;
	}

      $g = new game(array('match_id'=>$this->match_id, 'map_id'=>$map->getValue('map_id'), 'team1_score'=>$team1_stats[util::SCORE], 'team2_score'=>$team2_stats[util::SCORE])) ;

      $dest_root_dir = $t->getTourneyRoot() . util::SLASH . $g->getFileDirectory() ;
      $html_root_dir = $t->getTourneyRootHtml() . util::SLASH . $g->getFileDirectory() ;

      // Create the required directory
      if (!is_dir($dest_root_dir))
	{
	  if (!util::mkdir($dest_root_dir))
	    {
	      $g->deleteAll() ;
	      util::throwException('unable to create required directory') ;
	    }
	}

      foreach($team1_stats as $k=>$ts)
	{
	  if ($k!=util::SCORE && $k!=util::NAME && $k!=util::MATCHED)
	    {
	      $g->addTeamStat(array('team_id'=>$this->team1_id, 'stat_name'=>$k,  'value'=>$ts)) ;
	    }
	}

      foreach($team2_stats as $k=>$ts)
	{
	  if ($k!=util::SCORE && $k!=util::NAME && $k!=util::MATCHED)
	    {
	      $g->addTeamStat(array('team_id'=>$this->team2_id, 'stat_name'=>$k, 'value'=>$ts)) ;
	    }
	}

      $team1_players = $team1->getPlayers($t->getValue('tourney_id')) ;
      foreach($team1_players as $p)
	{
	  $team1_names[$p->getValue('player_id')] = $p->getValue('name') ;
	}

      $team2_players = $team2->getPlayers($t->getValue('tourney_id')) ;
      foreach($team2_players as $p)
	{
	  $team2_names[$p->getValue('player_id')] = $p->getValue('name') ;
	}

      // Team 1 Player Stats
      $team1_stats = array() ;

      $team1_player_match = util::stringMatch($team1_game_players, $team1_names) ;
      $team1_names = array_flip($team1_names) ;

      for($cnt=0; $cnt<count($team1_stats_all); $cnt++)      
	{
	  $curheader = $player_stats[fmod($cnt, $field_count)] ;

	  if ($curheader==$player_stats[0])
	    {
	      $player_name = $team1_player_match[$team1_stats_all[$cnt]] ;
	      $player_id = $team1_names[$player_name] ;

	      $team1_stats[$player_id] = array() ;
	      $team1_stats[$player_id][$curheader] = $team1_stats_all[$cnt] ;
	    }

	  $team1_stats[$player_id][$curheader] = $team1_stats_all[$cnt] ;
	}

      foreach($team1_stats as $k1=>$p)
	{
	  foreach($p as $k2=>$s)
	    {
	      if ($k2 == 'PieChart')
		{
		  $p = new player(array('player_id'=>$k1)) ;

		  $pinfo = pathinfo($s) ;
		  $new_file_name = $pinfo['basename'] ;

		  $piechart = $p->getPieChartIdx($g->getValue('game_id')) ;
		  if (rename($s, $dest_root_dir . util::SLASH . $new_file_name))
		    {
		      $g->addFile(array(file_desc=>$piechart, url=>$html_root_dir . util::SLASH . $new_file_name)) ;
		    }
		  else
		    {
		      unlink($html_root_dir . util::SLASH . $new_file_name) ;
		    }
		}

	      elseif ($k2 == util::CAPTURE_TIMES && !util::isNull($s))
		{
		  $caps = split(' ', $s) ;
		  foreach($caps as $t)
		    {
		      $g->addStat(array('player_id'=>$k1, 'team_id'=>$this->team1_id, 'stat_name'=>$k2, 'value'=>$t)) ;
		    }
		}

	      elseif ($k2!=util::NAME && $k2!=util::MATCHED &&
		      ($s!=0 || $k2==util::SCORE || $k2==util::RANK || $k2==util::EFFICIENCY))
		{
		  $g->addStat(array('player_id'=>$k1, 'team_id'=>$this->team1_id, 'stat_name'=>$k2, 'value'=>$s)) ;
		}
	    }
	}

      // Team 2 Player Stats
      $team2_stats = array() ;

      $team2_player_match = util::stringMatch($team2_game_players, $team2_names) ;
      $team2_names = array_flip($team2_names) ;

      for($cnt=0; $cnt<count($team2_stats_all); $cnt++)      
	{
	  $curheader = $player_stats[fmod($cnt, $field_count)] ;

	  if ($curheader==$player_stats[0])
	    {
	      $player_name = $team2_player_match[$team2_stats_all[$cnt]] ;
	      $player_id = $team2_names[$player_name] ;

	      $team2_stats[$player_id] = array() ;
	      $team2_stats[$player_id][$curheader] = $team2_stats_all[$cnt] ;
	    }

	  $team2_stats[$player_id][$curheader] = $team2_stats_all[$cnt] ;
	}

      foreach($team2_stats as $k1=>$p)
	{
	  foreach($p as $k2=>$s)
	    {
	      if ($k2 == 'PieChart')
		{
		  $p = new player(array('player_id'=>$k1)) ;

		  $pinfo = pathinfo($s) ;
		  $new_file_name = $pinfo['basename'] ;

		  $piechart = $p->getPieChartIdx($g->getValue('game_id')) ;
		  if (rename($s, $dest_root_dir . util::SLASH . $new_file_name))
		    {
		      $g->addFile(array(file_desc=>$piechart, url=>$html_root_dir . util::SLASH . $new_file_name)) ;
		    }
		  else
		    {
		      unlink($html_root_dir . util::SLASH . $new_file_name) ;
		    }
		}

	      elseif ($k2 == util::CAPTURE_TIMES && !util::isNull($s))
		{
		  $caps = split(' ', $s) ;

		  foreach($caps as $t)
		    {
		      $g->addStat(array('player_id'=>$k1, 'team_id'=>$this->team2_id, 'stat_name'=>$k2, 'value'=>$t)) ;
		    }
		}

	      elseif ($k2!=util::NAME && $k2!=util::MATCHED &&
		      ($s!=0 || $k2==util::SCORE || $k2==util::RANK || $k2==util::EFFICIENCY))
		{
		  $g->addStat(array('player_id'=>$k1, 'team_id'=>$this->team2_id, 'stat_name'=>$k2, 'value'=>$s)) ;
		}
	    }
	}

      // Move the files to their permanent home
      $pinfo = pathinfo($a['filename']) ;
      $new_demo_name = $pinfo['basename'] ;
      if (rename($a['filename'], $dest_root_dir . util::SLASH . $new_demo_name))      
	{
	  $g->addFile(array('file_desc'=>util::MVD_DEMO, 'url'=>$html_root_dir . util::SLASH . $new_demo_name)) ;
	}

      $pinfo = pathinfo($a['team_score_graph_small']) ;
      $new_tsgs_name = $pinfo['basename'] ;
      if (rename($a['team_score_graph_small'], $dest_root_dir . util::SLASH . $new_tsgs_name))
	{
	  $g->addFile(array(file_desc=>util::TEAM_SCORE_GRAPH_SMALL, url=>$html_root_dir . util::SLASH . $new_tsgs_name)) ;
	}

      $pinfo = pathinfo($a['team_score_graph_large']) ;
      $new_tsgl_name = $pinfo['basename'] ;
      if (rename($a['team_score_graph_large'], $dest_root_dir . util::SLASH . $new_tsgl_name))
	{
	  $g->addFile(array('file_desc'=>util::TEAM_SCORE_GRAPH_LARGE, 'url'=>$html_root_dir . util::SLASH . $new_tsgl_name)) ;
	}

      $pinfo = pathinfo($a['player_score_graph']) ;
      $new_psg_name = $pinfo['basename'] ;
      if (rename($a['player_score_graph'], $dest_root_dir . util::SLASH . $new_psg_name))
	{
	  $g->addFile(array('file_desc'=>util::PLAYER_SCORE_GRAPH, 'url'=>$html_root_dir . util::SLASH . $new_psg_name)) ;
	}

      if (!util::isNull($a['screenshot_url']))
	{
	  $pinfo = pathinfo($a['screenshot_url']) ;
	  $new_ss_name = $pinfo['basename'] ;
	  if (rename($a['screenshot_url'], $dest_root_dir . util::SLASH . $new_ss_name))      
	    {
	      $g->addFile(array('file_desc'=>util::SCREENSHOT, 'url'=>$html_root_dir . util::SLASH . $new_ss_name)) ;
	    }
	}

      $this->syncMatchInfo() ;
	}
      catch(Exception $e)
	{
	  if (!util::isNull($g))
	    {
	      $g->deleteAll() ;
	    }

	  throw $e;
	}

    }

  public function addComment($a)
    {
      $a['id'] = $this->match_id ;
      $a['comment_type'] = comment::TYPE_MATCH ;

      try
	{
	  $c = new comment($a) ;
	}
      catch (Exception $e) {}
    }

  public function addPoll($a)
    {
      $a['id'] = $this->match_id ;
      $a['poll_type'] = comment::TYPE_MATCH ;
      $p = new poll($a) ;
    }

  public function addFile($a)
    {
      $a['id'] = $this->match_id ;
      $a['file_type'] = file::TYPE_MATCH ;
      $f = new file($a) ;
    }

  public function getComments()
    {
      $sql_str = sprintf("select c.comment_id from comments c where c.comment_type='MATCH' and c.id=%d order by comment_date, comment_time", $this->match_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($link));

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new comment(array('comment_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function syncMatchInfo()
    {
      $team1_wins = 0 ;
      $team2_wins = 0 ;

      foreach($this->getGames() as $g)
	{
	  if ($g->getValue('team1_score')>$g->getValue('team2_score'))
	    {
	      $team1_wins++ ;
	    }
	  elseif ($g->getValue('team1_score')<$g->getValue('team2_score'))
	    {
	      $team2_wins++ ;
	    }
	}

      if ($team1_wins > $team2_wins)
	{
	  $this->update('winning_team_id', $this->team1_id) ;
	}
      elseif ($team1_wins < $team2_wins)
	{
	  $this->update('winning_team_id', $this->team2_id) ;
	}
      else
	{
	  $this->update('winning_team_id', null) ;
	  $this->update('match_date', util::DEFAULT_DATE) ;
	  $this->update('approved', false) ;
	}
    }

  public function getGames()
    {
      $sql_str = sprintf("select g.game_id from game g where g.match_id=%d", $this->match_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($link));

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new game(array('game_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getFiles()
    {
      $ftype = file::validateColumn(file::TYPE_GAME, 'file_type') ;

      $sql_str = sprintf("select f.file_id, f.file_desc from file_table f where f.id=%d and f.file_type='%s'", $this->match_id, $ftype) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($link));

      while ($row=mysql_fetch_row($result))
	{
	  $arr[$row[1]] = new file(array('file_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTourney()
    {
      $sql_str = sprintf("select d.tourney_id from match_table m, match_schedule ms, division d
                          where m.match_id=%d and m.schedule_id=ms.schedule_id and ms.division_id=d.division_id", $this->match_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($link));

      if ($row=mysql_fetch_row($result))
	{
	  $arr = new tourney(array('tourney_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getDivision()
    {
      $sql_str = sprintf("select ms.division_id from match_table m, match_schedule ms
                          where m.match_id=%d and m.schedule_id=ms.schedule_id", $this->match_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($link));

      if ($row=mysql_fetch_row($result))
	{
	  $arr = new division(array('division_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTeams()
    {
      return array(new team(array('team_id'=>$this->team1_id)), new team(array('team_id'=>$this->team2_id))) ;
    }

  public function getWinningTeam()
    {
      return new team(array('team_id'=>$this->winning_team_id)) ;
    }

  public function getMatchSchedule()
    {
      return new match_schedule(array('schedule_id'=>$this->schedule_id)) ;
    }

  public function getFileDirectory()
    {
      return 'match_' . $this->match_id ;
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
	  $sql_str = sprintf("update match_table set %s=%d where match_id=%d", $col, $this->$col, $this->match_id) ;
	}
      elseif ($col=='winning_team_id')
	{
	  $sql_str = sprintf("update match_table set %s=%s where match_id=%d", $col, util::nvl($this->$col, 'null'), $this->match_id) ;
	}
      else
	{
	  $sql_str = sprintf("update match_table set %s='%s' where match_id=%d", $col, $this->$col, $this->match_id) ;
	}

      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($link));
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from match_table where match_id=%d", $this->match_id) ;
      $result  = mysqli_query($link, $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($link));      
    }
}
?>
