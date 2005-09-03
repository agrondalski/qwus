<?php
require_once 'dbConnect.php' ;
?>

<?php
class news
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  private $news_id ;
  private $writer_id ;
  private $tourney_id ;
  private $isColumn ;
  private $subject ;
  private $news_date ;
  private $text ;

  function __construct($a)
    {
      $id = $a['news_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->news_id = $id ;

	  if ($this->getNewsInfo()==self::NOTFOUND)
	    {
	      util::throwException("No news exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      util::canNotBeNull($a, 'writer_id') ;
      util::canNotBeNull($a, 'subject') ;
      util::canNotBeNull($a, 'news_date') ;
      util::canNotBeNull($a, 'text') ;

      $this->writer_id   = util::mysql_real_escape_string($a['writer_id']) ;
      $this->tourney_id  = util::mysql_real_escape_string($a['tourney_id']) ;
      $this->isColumn    = util::nvl(util::mysql_real_escape_string($a['isColumn']), false) ;
      $this->subject     = util::mysql_real_escape_string($a['subject']) ;
      $this->news_date   = util::mysql_real_escape_string($a['news_date']) ;
      $this->text        = util::mysql_real_escape_string($a['text']) ;

      $sql_str = sprintf("insert into news(writer_id, tourney_id, isColumn, subject, news_date, text)" .
                         "values(%d, %s, %d, '%s', '%s', '%s')",
			 $this->writer_id, util::nvl($this->tourney_id, 'null'), $this->isColumn, $this->subject, $this->news_date, $this->text) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->news_id = mysql_insert_id() ;
    }

  private function getNewsInfo()
    {
      $sql_str = sprintf("select writer_id, tourney_id, subject, news_date, text from news where news_id=%d", $this->news_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->writer_id   = $row[0] ;
      $this->tourney_id  = $row[1] ;
      $this->subject     = $row[2] ;
      $this->news_date   = $row[3] ; 
      $this->text        = $row[4] ;

      mysql_free_result($row) ;

      return self::FOUND ;
    }

  public function getTourney()
    {
      if (isset($this->tourney_id))
	{
	  return new tourney(array('tourney_id'=>$this->tourney_id)) ;
	}

      return null ;
    }

  public function getWriter()
    {
      if (isset($this->writer_id))
	{
	  return new player(array('player_id'=>$this->writer_id)) ;
	}
    }

  public static function getNews($a)
    {
      $sql_str = sprintf("select n.news_id from news n where tourney_id is null and isColumn=false %s", util::getLimit($a)) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new news(array('news_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public static function getNewsCount()
    {
      $sql_str = sprintf("select count(*) from news n where tourney_id is null and isColumn=false") ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      $row = mysql_fetch_row($result) ;
      $val = $row[0] ;

      mysql_free_result($result) ;
      return $val ;
    }

  public function getValue($col)
    {
      if (!isset($col) || !isset($this->$col))
	{
	  return null ;
	}      

      return util::htmlstring($this->$col) ;
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
	  $sql_str = sprintf("update news set %s=%d where news_id=%d", $col, $this->$col, $this->news_id) ;
	}
      else
	{
	  $sql_str = sprintf("update news set %s='%s' where news_id=%d", $col, $this->$col, $this->news_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from news where news_id=%d", $this->news_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
