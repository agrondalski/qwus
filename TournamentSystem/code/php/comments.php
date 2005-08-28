<?php
require_once 'dbConnect.php' ;
?>

<?php
class comment
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  private $comment_id ;
  private $name ;
  private $player_ip ;
  private $match_id ;
  private $comment_text ;
  private $comment_date ;
  private $comment_time ;

  function __construct($a)
    {
      $id = $a['comment_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->comment_id = $id ;

	  if ($this->getCommentInfo()==self::NOTFOUND)
	    {
	      throw new Exception("No comment exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      $this->name          = mysql_real_escape_string($a['name']) ;
      $this->player_ip     = mysql_real_escape_string($a['player_ip']) ;
      $this->match_id      = mysql_real_escape_string($a['match_id']) ;
      $this->comment_text  = mysql_real_escape_string($a['comment_text']) ;
      $this->comment_date  = mysql_real_escape_string($a['comment_date']) ;
      $this->comment_time  = mysql_real_escape_string($a['comment_time']) ;

      $sql_str = sprintf("insert into comments(name, player_ip, match_id, comment_text, comment_date, comment_time)" .
                         "values('%s', '%s', %d, '%s', '%s', '%s')",
			 $this->name, $this->player_ip, $this->match_id, $this->comment_text, $this->comment_date, $this->comment_time) ;

      $result = mysql_query($sql_str) or die ("Unable to execute : $sql_str " . $mysql_error) ;
      $this->comment_id = mysql_insert_id() ;
    }

  private function getCommentInfo()
    {
      $sql_str = sprintf("select name, player_ip, match_id, comment_text, comment_date, comment_time from comments where comment_id=%d", $this->comment_id) ;
      $result  = mysql_query($sql_str) or die ("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name          = $row[0] ;
      $this->player_ip     = $row[1] ;
      $this->match_id      = $row[2] ;
      $this->comment_text  = $row[3] ; 
      $this->comment_date  = $row[3] ; 
      $this->comment_time  = $row[4] ;

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public function getMatch()
    {
      return new match(array('match_id'=>$this->match_id)) ;
    }

  function getValue($col)
    {
      if (! isset($col) || !isset($this->$col))
	{
	  return ;
	}      

      return $this->$col ;
    }

  function update($col, $val)
    {
      if (! isset($col) || !isset($val) || !isset($this->$col))
	{
	  return ;
	}

      $this->$col = mysql_real_escape_string($val) ;

      if (is_numeric($val))
	{
	  $sql_str = sprintf("update comments set %s=%d where comment_id=%d", $col, $this->$col, $this->comment_id) ;
	}
      else
	{
	  $sql_str = sprintf("update comments set %s='%s' where comment_id=%d", $col, $this->$col, $this->comment_id) ;
	}

      $result  = mysql_query($sql_str) or die ("Unable to execute : $sql_str : " . mysql_error());
    }

  function delete()
    {
      $sql_str = sprintf("delete from comments where comment_id=%d", $this->comment_id) ;
      $result  = mysql_query($sql_str) or die ("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
