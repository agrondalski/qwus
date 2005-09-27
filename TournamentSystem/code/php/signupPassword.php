<?php
include_once 'includes.php' ;

session_start() ;
util::createTextImage($_SESSION['validate_pw']) ;
?>
