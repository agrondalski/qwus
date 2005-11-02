<?php
$link = mysql_connect("localhost", "qwus", "XPy6Z87")
     or util::throwException('Could not login to mysql: ' . mysql_error() . 'Contact <a href="mailto:ultimo@quakeworld.us>ult</a> if problem persists.');

     mysql_select_db("qwus", $link) or util::throwException('Cound not select database') ;
;?>
