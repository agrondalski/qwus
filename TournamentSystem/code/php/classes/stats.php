<?php
class stats
{
  private $player_id ;
  private $game_id ;
  private $stat_name ;
  private $team_id ;
  private $value ;

  function __construct($a)
    {
      if (array_key_exists('player_id', $a) && array_key_exists('game_id', $a) && array_key_exists('stat_name', $a) && !array_key_exists('value', $a))
	{
	  $this->player_id = $this->validateColumn($a['player_id'], 'player_id') ;
	  $this->game_id   = $this->validateColumn($a['game_id'], 'game_id') ;
	  $this->stat_name = $this->validateColumn($a['stat_name'], 'stat_name') ;
	  
	  if ($this->getPlayerInfo()==util::NOTFOUND)
	    {
	      util::throwException("No player exists with specified id");
	    }
	  else
	    {
	      return ;
	    }

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

      $sql_str = sprintf("insert into stats(player_id, game_id, stat_name, team_id, value)" .
                         "values(%d, %d, '%s', %d, %d)",
			 $this->player_id, $this->game_id, $this->stat_name, $this->team_id, $this->value) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
    }

  private function getStatsInfo()
    {
      $sql_str = sprintf("select team_id, value from stats where player_id=%d and game_id=%d", $this->player_id, $this->game_id, $this->stat_name) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->value    = $row[0] ; 
      $this->team_id  = $row[0] ; 

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
	  
	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'game_id')
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

      elseif ($col == 'stat_name')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'team_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'value')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  
	  return util::mysql_real_escape_string($val) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public static function hasStatsEntry($pid, $gid, $sn)
    {
      $pid = player::validateColumn($pid, 'player_id') ;
      $gid = game::validateColumn($gid, 'game_id') ;
      $sn  = self::validateColumn($sn, 'stat_name') ;

      $sql_str = sprintf("select 1 from stats where player_id=%d and game_id=%d and stat_name='%s'", $pid, $gid, $sn) ;
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
