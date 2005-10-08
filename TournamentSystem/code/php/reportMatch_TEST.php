<?php

// Tests for match reporting / stats generation

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 1 (hipark.mvd)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/hipark.mvd'>";
echo "<input type='hidden' name='team1' value='pink'>";
echo "<input type='hidden' name='team2' value='red'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 2 (aero.mvd)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/aero.mvd'>";
echo "<input type='hidden' name='team1' value=' the'>";
echo "<input type='hidden' name='team2' value='last'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 3 (qcone1m2.mvd)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/qcone1m2.mvd'>";
echo "<input type='hidden' name='team1' value='pink'>";
echo "<input type='hidden' name='team2' value='yell'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 4 (qcone1m2.mvd) (sending 1 invalid team)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/qcone1m2.mvd'>";
echo "<input type='hidden' name='team1' value='pink'>";
echo "<input type='hidden' name='team2' value='blue'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 5 (qcone1m2.mvd) (sending 1 incomplete team)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/qcone1m2.mvd'>";
echo "<input type='hidden' name='team1' value='pi'>";
echo "<input type='hidden' name='team2' value='yell'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 6 (qcone1m2.mvd) (sending chaos)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/qcone1m2.mvd'>";
echo "<input type='hidden' name='team1' value='wtf?'>";
echo "<input type='hidden' name='team2' value='bog!'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 7 (aero1.mvd.gz) (must manually gzip again after test)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/aero1.mvd.gz'>";
echo "<input type='hidden' name='team1' value=' the'>";
echo "<input type='hidden' name='team2' value='last'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 8 (fred.txt) (not mvd)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/fred.txt'>";
echo "<input type='hidden' name='team1' value=' the'>";
echo "<input type='hidden' name='team2' value='last'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 9 (fred.mvd) (doesnt exist)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/fred.mvd'>";
echo "<input type='hidden' name='team1' value=' the'>";
echo "<input type='hidden' name='team2' value='last'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
echo "</table></form>";

echo "<form action='./perl/mvdStats.pl' method=post>";
echo "<table border=0 cellpadding=4 cellspacing=0>";
echo "<tr><td><b>Test 10 with players (hipark.mvd)</b></td>";
echo "<input type='hidden' name='tourney_id' value='1'>";
echo "<input type='hidden' name='division_id' value='1'>";
echo "<input type='hidden' name='match_id' value='13'>";
echo "<input type='hidden' name='winning_team_id' value='8'>";
echo "<input type='hidden' name='approved' value='1'>";
echo "<input type='hidden' name='filename' value ='/tmp/uploads/hipark.mvd'>";
echo "<input type='hidden' name='team1' value='pink'>";
echo "<input type='hidden' name='team2' value='red'>";
echo "<input type='hidden' name='team1players' value='fred\\bob\\bill\\papi'>";
echo "<input type='hidden' name='team2players' value='bog\\gay\\fag\\drako\\'>";
echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></t\r>";
echo "</table></form>";



?>
