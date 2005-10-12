<?php
require 'includes.php';

include 'userLinks.php';
echo "<br>";
echo "<h2>Rules</h2>";

$tid = $_REQUEST['tourney_id'];
$t = new tourney(array('tourney_id'=>$tid));

echo $t->getValue('rules') ;
?>
