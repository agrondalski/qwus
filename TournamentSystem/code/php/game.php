<?php
require_once 'dbConnect.php' ;
?>

<?php
class game
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  private $game_id ;
  private $match_id ;
  private $map_id ;
  private $team1_score ;
  private $team2_score ;
  private $screenshot_url ;
  private $demo_url ;

  function __construct($a)
    {
      $id = $a['game_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->game_id = $id ;

	  if ($this->getGameInfo()==self::NOTFOUND)
	    {
	      util::throwException("No game exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      util::canNotBeNull($a, 'match_id') ;
      util::canNotBeNull($a, 'map_id') ;
      util::canNotBeNull($a, 'team1_score') ;
      util::canNotBeNull($a, 'team2_score') ;

      $this->match_id        = util::mysql_real_escape_string($a['match_id']) ;
      $this->map_id          = util::mysql_real_escape_string($a['map_id']) ;
      $this->team1_score     = util::mysql_real_escape_string($a['team1_score']) ;
      $this->team2_score     = util::mysql_real_escape_string($a['team1_score']) ;
      $this->screenshot_url  = util::mysql_real_escape_string($a['screenshot_url']) ;
      $this->demo_url        = util::mysql_real_escape_string($a['screenshot_url']) ;

      $sql_str = sprintf("insert into game(match_id, map_id, team1_score, team2_score, screenshot_url, demo_url)" .
                         "values(%d, %d, %d, %d, '%s', '%s')",
			 $this->match_id, $this->map_id, $this->team1_score, $this->team2_score, $this->screenshot_url, $this->demo_url) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->game_id = mysql_insert_id() ;
    }

  private function getGameInfo()
    {
      $sql_str = sprintf("select match_id, map_id, team1_score, team2_score, screenshot_url, demo_url from game where game_id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->match_id        = $row[0] ;
      $this->map_id          = $row[1] ;
      $this->team1_score     = $row[2] ;
      $this->team2_score     = $row[3] ; 
      $this->screenshot_url  = $row[4] ; 
      $this->demo_url        = $row[5] ;

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public function getStats()
    {
      $sql_str = sprintf("select s.player_id from stats s where s.game_id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new stats(array('player_id'=>$row[0], 'game_id'=>$this->game_id)) ;
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

  public function getValue($col)
    {
      if (!isset($col) || !isset($this->$col))
	{
	  return null ;
	}      

      return $this->$col ;
    }

  public function update($col, $val)
    {
      if (!isset($col) || !isset($val))
	{
	  return null ;
	}

      $this->$col = util::mysql_real_escape_string($val) ;

      if (is_numeric($val))
	{
	  $sql_str = sprintf("update game set %s=%d where game_id=%d", $col, $this->$col, $this->game_id) ;
	}
      else
	{
	  $sql_str = sprintf("update game set %s='%s' where game_id=%d", $col, $this->$col, $this->game_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from game where game_id=%d", $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
