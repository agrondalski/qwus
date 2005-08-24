<?php

$link = mysql_connect("localhost", "qwus", "@XantoM@")
     or die ('Could not login to mysql: ' . mysql_error() . 'Contact <a href="mailto:ultimo@quakeworld.us>ult</a> if problem persists.');

     mysql_select_db("dew_test", $link) or die ('Cound not select database') ;
;?>
