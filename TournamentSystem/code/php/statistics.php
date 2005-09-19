
<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

include 'userLinks.php';
echo "<br>";

// Results section

echo "<h2>Statistics</h2>";

?>