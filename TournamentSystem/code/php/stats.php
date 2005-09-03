<?php
require_once 'dbConnect.php' ;
?>

<?php
class stats
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  private $player_id ;
  private $game_id ;
  private $score ;
  private $time ;

  function __construct($a)
    {
      $id  = $a['player_id'] ;
      $id2 = $a['game_id'] ;

      if (isset($id) && is_numeric($id) && isset($id2) && is_numeric($id2) && count($a)==2)
	{
	  $this->player_id = $id ;
	  $this->game_id = $id2 ;

	  if ($this->getStatsInfo()==self::NOTFOUND)
	    {
	      util::throwException("No stats exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      util::canNotBeNull($a, 'player_id') ;
      util::canNotBeNull($a, 'game_id') ;
      util::canNotBeNull($a, 'score') ;
      //      util::canNotBeNull($a, 'time') ;

      $this->player_id  = util::mysql_real_escape_string($a['player_id']) ;
      $this->game_id    = util::mysql_real_escape_string($a['game_id']) ;
      $this->score      = util::mysql_real_escape_string($a['score']) ;
      $this->time       = util::mysql_real_escape_string($a['time']) ;

      $sql_str = sprintf("insert into stats(player_id, game_id, score, time)" .
                         "values(%d, %d, %d, %d)",
			 $this->player_id, $this->game_id, $this->score, $this->time) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . $mysql_error) ;
    }

  private function getStatsInfo()
    {
      $sql_str = sprintf("select score, time from stats where player_id=%d and game_id=%d", $this->player_id, $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->score    = $row[0] ;
      $this->time     = $row[1] ; 

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public static function hasStatsEntry($pid, $gid)
    {
      $sql_str = sprintf("select 1 from stats where player_id=%d and game_id=%d", $pid, $gid) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return false ; 
	}

      return true ;
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
	  $sql_str = sprintf("update stats set %s=%d where player_id=%d and game_id=%d", $col, $this->$col, $this->player_id, $this->game_id) ;
	}
      else
	{
	  $sql_str = sprintf("update stats set %s='%s' where player_id=%d and game_id=%d", $col, $this->$col, $this->player_id, $this->game_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from stats where player_id=%d and game_id=%d", $this->player_id, $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
