<?php
$link = mysqli_connect("localhost", "test_user", "password")
     or util::throwException('Could not login to mysql: ' . mysqli_error($link) . 'Contact <a href="mailto:ultimo@quakeworld.us>ult</a> if problem persists.');

     mysqli_select_db($link, "dew") or util::throwException('Cound not select database') ;
;?>
