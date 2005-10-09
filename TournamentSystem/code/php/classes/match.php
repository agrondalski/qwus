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

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->match_id = mysql_insert_id() ;
    }

  private function getMatchInfo()
    {
      $sql_str = sprintf("select schedule_id, team1_id, team2_id, winning_team_id, approved, match_date from match_table where match_id=%d", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

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
      if (!is_array($a))
	{
	  return ;
	}

      /*
      if ( !array_key_exists('playerFields', $a) ||
	  !array_key_exists('PlayerStats', $a) || !array_key_exists('team1players', $a) || !array_key_exists('team2players', $a))
      */

      if (!array_key_exists('winning_team_id', $a) || !array_key_exists('filename', $a) || !array_key_exists('map', $a) || !array_key_exists('teamStats', $a) ||
	  !array_key_exists('team1', $a) || !array_key_exists('team2', $a) || !array_key_exists('team_score_graph_small', $a) ||
	  !array_key_exists('team_score_graph_large', $a) || !array_key_exists('player_score_graph', $a) || !array_key_exists('playerFields', $a) ||
	  !array_key_exists('PlayerStats', $a) || !array_key_exists('team1players', $a) || !array_key_exists('team2players', $a))
	{
	  util::throwException('not enough info to process stats') ;
	}

      if (util::isNull($a['map']))
	{
	  util::throwException('mvdstats.pl probably failed due to a bad mvd') ;
	}

      $t = $this->getTourney() ;

      // Find the map match
      $maps = $t->getMaps() ;
      foreach($maps as $m)
	{
	  $maps_abbr[$m->getValue('map_id')] = $m->getValue('map_abbr') ;
	}

      $idx = util::findBestMatch($maps_abbr, $a['map']) ;
      $map = $maps[$idx] ;

      if ($a['winning_team_id'] = $team1_id || $a['winning_team_id'] = $team2_id)
	{
	  $this->update('winning_team_id', $a['winning_team_id']) ;
	}

      $teams = $this->getTeams() ;
      $team1 = $teams[0] ;
      $team2 = $teams[1] ;

      $team_stat_header = explode('\\\\', $a['teamStats']) ;
      $team1_stats2 = explode('\\\\', $a['team1']) ;
      $team2_stats2 = explode('\\\\', $a['team2']) ;
      $teams = array($team1_stats[0], $team2_stats[0]) ;

      for($cnt=0; $cnt<count($team_stat_header); $cnt++)
	{
	  $h = $team_stat_header[$cnt] ;

	  $team1_stats[$h] = $team1_stats2[$cnt] ;
	  $team2_stats[$h] = $team2_stats2[$cnt] ;
	}

      $g = new game(array('match_id'=>$this->match_id, 'map_id'=>$map->getValue('map_id'), 'team1_score'=>$team1_stats[util::SCORE . 's'], 'team2_score'=>$team2_stats[util::SCORE . 's'])) ;

      foreach($team1_stats as $k=>$ts)
	{
	  if ($k!=util::SCORE . 's' && $k!='Name' && $k!='Matched')
	    {
	      $g->addTeamStat(array('team_id'=>$this->team1_id, 'stat_name'=>$k,  'value'=>$ts)) ;
	    }
	}

      foreach($team2_stats as $k=>$ts)
	{
	  if ($k!=util::SCORE . 's' && $k!='Name' && $k!='Matched')
	    {
	      $g->addTeamStat(array('team_id'=>$this->team2_id, 'stat_name'=>$k,  'value'=>$ts)) ;
	    }
	}

      // Move the files to their permanent home
      $dest_root_dir = util::ROOT_DIR . util::SLASH . $t->getValue('name') ;
      $html_root_dir = util::HTML_ROOT_DIR . util::SLASH . $t->getValue('name') ;

      if (!is_dir($dest_root_dir))
	{
	  if (!mkdir($dest_root_dir))
	    {
	      util::throwException('unable to create required directory') ;
	    }
	}

      $prefix = $team1->getValue('name_abbr') . '_' . $team2->getValue('name_abbr') . '_' . $map->getValue('map_abbr') . '_' . $this->match_id . '_' . $g->getValue('game_id') ;

      $pinfo = pathinfo($a['filename']) ;
      $new_demo_name = $prefix . '_demo.' . $pinfo['extension'] ;
      //if (move_uploaded_file($a['filename'], $dest_root_dir . util::SLASH . $new_demo_name))
      if (rename($a['filename'], $dest_root_dir . util::SLASH . $new_demo_name))      
	{
	  $g->addFile(array('file_desc'=>util::MVD_DEMO, 'url'=>$html_root_dir . util::SLASH . $new_demo_name)) ;
	}

      $pinfo = pathinfo($a['team_score_graph_small']) ;
      $new_tsgs_name = $prefix . '_tsgs.' . $pinfo['extension'] ;
      if (rename($a['team_score_graph_small'], $dest_root_dir . util::SLASH . $new_tsgs_name))
	{
	  $g->addFile(array(file_desc=>util::TEAM_SCORE_GRAPH_SMALL, url=>$html_root_dir . util::SLASH . $new_tsgs_name)) ;
	}

      $pinfo = pathinfo($a['team_score_graph_large']) ;
      $new_tsgl_name = $prefix . '_tsgl.' . $pinfo['extension'] ;
      if (rename($a['team_score_graph_large'], $dest_root_dir . util::SLASH . $new_tsgl_name))
	{
	  $g->addFile(array('file_desc'=>util::TEAM_SCORE_GRAPH_LARGE, 'url'=>$html_root_dir . util::SLASH . $new_tsgl_name)) ;
	}

      $pinfo = pathinfo($a['player_score_graph']) ;
      $new_psg_name = $prefix . '_psg.' . $pinfo['extension'] ;
      if (rename($a['player_score_graph'], $dest_root_dir . util::SLASH . $new_psg_name))
	{
	  $g->addFile(array('file_desc'=>util::PLAYER_SCORE_GRAPH, 'url'=>$html_root_dir . util::SLASH . $new_psg_name)) ;
	}
    }

  public function addComment($a)
    {
      $a['id'] = $this->match_id ;
      $a['comment_type'] = comment::TYPE_MATCH ;
      $c = new comment($a) ;
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

  public static function getAllMatches()
    {
      $sql_str = sprintf('select mt.match_id from match_table mt') ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new match(array('match_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getComments()
    {
      $sql_str = sprintf("select c.comment_id from comments c where c.comment_type='MATCH' and c.id=%d order by comment_date, comment_time", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new comment(array('comment_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getGames()
    {
      $sql_str = sprintf("select g.game_id from game g where g.match_id=%d", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($row=mysql_fetch_row($result))
	{
	  $arr = new tourney(array('tourney_id'=>$row[0])) ;
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
      else
	{
	  $sql_str = sprintf("update match_table set %s='%s' where match_id=%d", $col, $this->$col, $this->match_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from match_table where match_id=%d", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
