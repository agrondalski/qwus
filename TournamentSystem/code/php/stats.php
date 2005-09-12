<?php
require_once 'dbConnect.php' ;
?>

<?php
class stats
{
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

	  if ($this->getStatsInfo()==util::NOTFOUND)
	    {
	      util::throwException("No stats exists with specified id");
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

      $sql_str = sprintf("insert into stats(player_id, game_id, score, time)" .
                         "values(%d, %d, %d, %d)",
			 $this->player_id, $this->game_id, $this->score, $this->time) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
    }

  private function getStatsInfo()
    {
      $sql_str = sprintf("select score, time from stats where player_id=%d and game_id=%d", $this->player_id, $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->score    = $row[0] ;
      $this->time     = $row[1] ; 

      mysql_free_result($result) ;

      return util::FOUND ;
    }

  public function validateColumnName($col)
    {
      $found ;
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
      if ($col == 'player_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'game_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'score')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'time')
	{
	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public static function hasStatsEntry($pid, $gid)
    {
      $pid = player::validateColumn($pid, 'player_id') ;
      $gid = game::validateColumn($gid, 'game_id') ;

      $sql_str = sprintf("select 1 from stats where player_id=%d and game_id=%d", $pid, $gid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return false ; 
	}

      return true ;
    }

  public function getValue($col)
    {
      $this->validateColumnName($col) ;
      return htmlentities($this->$col) ;
    }

  public function update($col, $val)
    {
      $this->$col = $this->validateColumn($val, $col) ;

      if (is_numeric($this->$col))
	{
	  $sql_str = sprintf("update stats set %s=%d where player_id=%d and game_id=%d", $col, $this->$col, $this->player_id, $this->game_id) ;
	}
      else
	{
	  $sql_str = sprintf("update stats set %s='%s' where player_id=%d and game_id=%d", $col, $this->$col, $this->player_id, $this->game_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from stats where player_id=%d and game_id=%d", $this->player_id, $this->game_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
