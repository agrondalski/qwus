<?php
$link = mysql_connect("localhost", "test_user", "8s6T1OL")
     or util::throwException('Could not login to mysql: ' . mysql_error() . 'Contact <a href="mailto:ultimo@quakeworld.us>ult</a> if problem persists.');

     mysql_select_db("dew", $link) or util::throwException('Cound not select database') ;
;?>
