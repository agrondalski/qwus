#!/usr/bin/perl

# todo:
# watch out for paranthesis in image filenames? as in PIANO_DOG(DP)
# check ctf ax frags
# improve regexs
# always team #3 in ctf?
# team stats (end of mvd)
# player minutes played ( * left the game)

use CGI qw/:standard/;
use GD::Graph::lines;
use GD::Graph::pie;
use GD::Graph::colour;
use mvdPlayer;
use mvdTeam;

$DEBUG = 0;

package main;
$teamOneScore = 0;
$teamTwoScore = 0;
$teamOneName = "red";
$teamTwoName = "blue";
$tempDir = "/tmp/";

if ($DEBUG)
{
  print "-- Debug Enabled --\n";
  $mvd = shift(@ARGV);
}
else
{
  $cgi = new CGI;
  $tourney_id = $cgi->param('tourney_id');
  $division_id = $cgi->param('division_id');
  $match_id = $cgi->param('match_id');
  $winning_team_id = $cgi->param('winning_team_id');
  $winningTeamAbbr = $cgi->param('winning_team_abbr');
  $approved = $cgi->param('approved');
  $mvd = $cgi->param('filename');
  $teamOneAbbr = $cgi->param('team1');
  $teamTwoAbbr = $cgi->param('team2');
  $teamOnePlayers = $cgi->param('team1players');
  $teamTwoPlayers = $cgi->param('team2players');

  print "Content-type: text/html\n\n";
  my $referer = $ENV{"HTTP_REFERER"};
  if ($referer != /reportMatch/) { exit; }
}

if ($mvd =~ /(.*)\.gz$/)
{
  my $shell = `gzip -d "$mvd"`;
  $mvd = $1;
}
if ($mvd !~ /(.*)\.mvd$/)
{
  $mvd = "";
  outputForm();
  exit();
}

my $tempMvd = $mvd . ".tmp";
my $shell = `sed -f convertAscii.sed "$mvd" > "$tempMvd"`;
my @strings = `strings -1 "$tempMvd"`;
my $fraggee = undef;
my $fragger = undef;
$stringCounter = -1;
foreach $string (@strings)
{
  $stringCounter++;
  if (length($string) < 8)
  { 
    1;
  }
  elsif ($string =~ /^\[SPEC\](.*)/)
  {
    next;
  }
  elsif ($string =~ /^(.*) rides (.*)'s rocket/)
  {
    $fraggee = findPlayer($1);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
  }
  elsif ($string =~ /^(.*) accepts (.*)'s shaft/)
  {
    $fraggee = findPlayer($1);
    $fraggee->lightningDeaths($fraggee->lightningDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->lightningFrags($fragger->lightningFrags() + 1);
  }
  # ctf attempt for lg
  elsif ($string =~ /^'s shaft/)
  {
    $fraggee = findPlayer($oldString2);
    $fraggee->lightningDeaths($fraggee->lightningDeaths() + 1);
    $fragger = findPlayer($oldString);
    $fragger->lightningFrags($fragger->lightningFrags() + 1);
  }
  elsif ($string =~ /^(.*) chewed on (.*)'s boomstick/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->shotgunDeaths($fraggee->shotgunDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->shotgunFrags($fragger->shotgunFrags() + 1);
  }
  elsif ($string =~ /^ was punctured by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = findPlayer($oldString);
    $fraggee->sngDeaths($fraggee->sngDeaths() + 1);
    $fragger = findPlayer($nextString);
    $fragger->sngFrags($fragger->sngFrags() + 1);
  }
  elsif ($string =~ /^(.*) was punctured by (.*)/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->sngDeaths($fraggee->sngDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->sngFrags($fragger->sngFrags() + 1);
  }
  elsif ($string =~ /^ was hooked by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = findPlayer($oldString);
    $fraggee->grappleDeaths($fraggee->grappleDeaths() + 1);
    $fragger = findPlayer($nextString);
    $fragger->grappleFrags($fragger->grappleFrags() + 1);
  }
  elsif ($string =~ /^ was nailed by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = findPlayer($oldString);
    $fraggee->nailgunDeaths($fraggee->nailgunDeaths() + 1);
    $fragger = findPlayer($nextString);
    $fragger->nailgunFrags($fragger->nailgunFrags() + 1);
  }
  elsif ($string =~ /^(.*) was nailed by (.*)/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->nailgunDeaths($fraggee->nailgunDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->nailgunFrags($fragger->nailgunFrags() + 1);
  }
  elsif ($string =~ /^(.*) ate 2 loads of (.*)'s buckshot/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->ssgDeaths($fraggee->ssgDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->ssgFrags($fragger->ssgFrags() + 1);
  }
  elsif ($string =~ /^(.*) was ax-murdered by (.*)/)
  {
    $fraggee = findPlayer($1);
    $fraggee->axDeaths($fraggee->axDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->axFrags($fragger->axFrags() + 1);
  }
  elsif ($string =~ /^(.*) rips (.*) a new one/)
  {
    $fragger = findPlayer($1);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
    $fraggee = findPlayer($2);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
  }
  elsif ($string =~ /^(.*) was smeared by (.*)'s quad rocket/)
  {
    $fraggee = findPlayer($1);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
  }
  elsif ($string =~ /^(.*) was brutalized by (.*)'s quad rocket/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
  }
  elsif ($string =~ /^(.*) eats (.*)'s pineapple/)
  {
    $fraggee = findPlayer($1);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^(.*) was gibbed by (.*)'s grenade/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^(.*) was gibbed by (.*)'s rocket/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
  }
  elsif ($string =~ /^(.*) becomes bored with life/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->rocketBores($fraggee->rocketBores() + 1);
  }
  #elsif ($string =~ /^(.*) discovers blast radius/)
  #{
  #  print $string;
  #  $fraggee = findPlayer($1);
  #  $fraggee->rocketBores($fraggee->rocketBores() + 1);
  #}
  elsif ($string =~ /^ tries to put the pin back in/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->grenadeBores($fraggee->grenadeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the water/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the slime/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the lava/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  #elsif ($string =~ /^(.*) electrocutes himself/)
  #{
  #  print $string;
  #  $fraggee = findPlayer($1);
  #  $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  #}
  elsif ($string =~ /^(.*) accepts (.*)'s discharge/)
  {
    $fraggee = findPlayer($1);
    $fraggee->dischargeDeaths($fraggee->dischargeDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->dischargeFrags($fragger->dischargeFrags() + 1);
  }
  elsif ($string =~ /^(.*) was squished/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->squishBores($fraggee->squishBores() + 1);
  }
  elsif ($string =~ /^(.*) squished a teammate/)
  {
# doesnt effect score?  wrong it does!
    $fragger = findPlayer($1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) squishes (.*)/)
  {
    $fraggee = findPlayer($2);
    $fraggee->squishDeaths($fraggee->squishDeaths() + 1);
    $fragger = findPlayer($1);
    $fragger->squishFrags($fragger->squishFrags() + 1);
  }
  elsif ($string =~ /^ visits the Volcano God/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }    
  elsif ($string =~ /^ burst into flames/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }
  elsif ($string =~ /^ turned into hot slag/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }
  elsif ($string =~ /^(.*) cratered/)
  {
  #  print $string;
    $fraggee = findPlayer($1);
    $fraggee->fallBores($fraggee->fallBores() + 1);
  }
  elsif ($string =~ /^ fell to his death/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->fallBores($fraggee->fallBores() + 1);
  }
  elsif ($string =~ /^(.*) sleeps with the fishes/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->waterBores($fraggee->waterBores() + 1);
  }
  elsif ($string =~ /^(.*) sucks it down/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->waterBores($fraggee->waterBores() + 1);
  }
  elsif ($string =~ /^(.*) gulped a load of slime/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->slimeBores($fraggee->slimeBores() + 1);
  }
  elsif ($string =~ /^(.*) can't exist on slime alone/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->slimeBores($fraggee->slimeBores() + 1);
  }
  elsif ($string =~ /^ was spiked/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^(.*) tried to leave/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^(.*) died/)
  {
    $fraggee = findPlayer($1);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^ suicides/)
  {
    $fraggee = findPlayer($oldString);
    $fraggee->miscBores($fraggee->miscBores() + 2);
  }
  elsif ($string =~ /^'s rocket/)
  {
    $fraggee = findPlayer($oldString2);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
    $fragger = findPlayer($oldString);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
  }
  elsif ($string =~ /^'s Quad rocket/)
  {
    $fraggee = findPlayer($oldString2);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
    $fragger = findPlayer($oldString);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
  }
  elsif ($string =~ /^'s pineapple/)
  {
    $fraggee = findPlayer($oldString2);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = findPlayer($oldString);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^'s grenade/)
  {
    $fraggee = findPlayer($oldString2);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = findPlayer($oldString);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^'s boomstick/)
  {
    $fraggee = findPlayer($oldString2);
    $fraggee->shotgunDeaths($fraggee->shotgunDeaths() + 1);
    $fragger = findPlayer($oldString);
    $fragger->shotgunFrags($fragger->shotgunFrags() + 1);
  }
  elsif ($string =~ /^'s buckshot/)
  {
    $fraggee = findPlayer($oldString2);
    $fraggee->ssgDeaths($fraggee->ssgDeaths() + 1);
    $fragger = findPlayer($oldString);
    $fragger->ssgFrags($fragger->ssgFrags() + 1);
  }
  elsif ($string =~ /^ captured the/)
  {
    $fragger = findPlayer($oldString);
    $fragger->captures($fragger->captures() + 1);
    $nextString = $strings[$stringCounter + 1];
    if ($nextString =~ /capture took/)
    {
      $minutes = $strings[$stringCounter + 2];
      $seconds = $strings[$stringCounter + 4];
      chomp($minutes);  chomp($seconds);
      if ($seconds == 0) 
      { 
	$seconds = $strings[$stringCounter + 5];
	chomp($seconds);
      }
      $fragger->captureTimes(60 * $minutes + $seconds);
    }
  }
  elsif ($string =~ /^ killed the flag carrier/)
  {
    $fragger = findPlayer($oldString);
    $nextString = $strings[$stringCounter + 2];
    if ($nextString =~ /^ bonus frags/)
    {
      $fragger->carrierFragsBonus($fragger->carrierFragsBonus() + 1);
    }
    else
    {
      $fragger->carrierFragsNoBonus($fragger->carrierFragsNoBonus() + 1);
    }
  }
  elsif ($string =~ /^ returned the/)
  {
    $fragger = findPlayer($oldString);
    $fragger->flagReturns($fragger->flagReturns() + 1);
  }
  elsif ($string =~ /^ got the /)
  {
    $fragger = findPlayer($oldString);
    $fragger->flagPickups($fragger->flagPickups() + 1);
  }
  elsif ($string =~ /^ defends the/)
  {
    $fragger = findPlayer($oldString);
    $fragger->flagDefends($fragger->flagDefends() + 1);
  }
  elsif ($string =~ /^'s flag carrier/)
  {
    $fragger = findPlayer($oldString2);
    if ($string =~ /agressive/) #poor spelling
    {
      $fragger->carrierDefendsAgg($fragger->carrierDefendsAgg() + 1);
    }
    else
    {
      $fragger->carrierDefends($fragger->carrierDefends() + 1);
    }
  }
  elsif ($string =~ /^ lost the/)
  {
    $fragger = findPlayer($oldString);
    $fragger->flagDrops($fragger->flagDrops() + 1);
  }
  elsif ($string =~ /^ gets an assist for returning his flag/)
  {
    $fragger = findPlayer($oldString);
    $fragger->returnAssists($fragger->returnAssists() + 1);
  }
  elsif ($string =~ /^ gets an assist for fragging/)
  {
    $fragger = findPlayer($oldString);
    $fragger->fragAssists($fragger->fragAssists() + 1);
  }
  elsif ($string =~ /^ was telefragged by his teammate/) 
  {
    # this seems to have no effect on score in ktpro ??
    #$fraggee = findPlayer($oldString);
    #$fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^ was telefragged by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = findPlayer($oldString);
    $fraggee->teleDeaths($fraggee->teleDeaths() + 1);
    $fragger = findPlayer($nextString);
    $fragger->teleFrags($fragger->teleFrags() + 1);
  }
  elsif ($string =~ /^(.*) was telefragged by (.*)/) 
  {
    $fraggee = findPlayer($1);
    $fraggee->teleDeaths($fraggee->teleDeaths() + 1);
    $fragger = findPlayer($2);
    $fragger->teleFrags($fragger->teleFrags() + 1);
  }
  #elsif ($string =~ /satan/i) # doesn't change score?
  #{
  #  print $string;
  #}
  elsif ($string =~ /^(.*) loses another friend/) 
  {
    $fragger = findPlayer($1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) mows down a teammate/)
  {
    $fragger = findPlayer($1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) checks his glasses/) 
  {
    $fragger = findPlayer($1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ m/\\map\\/)
  {
    $map = $';
    while ($map =~ /(.*)\\/) { $map = $1; }
    $map =~ s/\s+$//;      
  }
  elsif ($string =~ m/\\name\\/)
  {
    $name = $';
    if ($string =~ m/\\team\\/)
    {
      $team = $';
 # Dont bother with spectators
      if ($string =~ m/\\*spectator\\/i)
      {
        $spec = $';
        while ($spec =~ /(.*)\\/) { $spec = $1; }
        $spec =~ s/\s+$//;
        if ($spec > 0) { next; }      
      }
      while ($team =~ /(.*)\\/) { $team = $1; }
      while ($name =~ /(.*)\\/) { $name = $1; }
      $name =~ s/\s+$//;
      $team =~ s/\s+$//;
      $player = findPlayer($name);
      # should prevent player rejoining game on different team
      if ($player->team == undef)
      {
        if (@teams < 2)
        {
	  $team = findTeam($team);
          $team->addPlayer($name);
          $player->team($team);
        }
        else
        {
	   $team = findTeamNoCreate($team);
           if ($team != null)
           {
	      $team->addPlayer($name);
              $player->team($team);
           }
           else
           {
	      next;
           }
        }
      }
      if ($string =~ m/\\bottomcolor\\/)
      {
        my $bottomColor = $';
        while ($bottomColor =~ /(.*)\\/) { $bottomColor = $1; }
        $bottomColor =~ s/\s+$//;
        $player->bottomColor($bottomColor);
      }
      if ($string =~ m/\\topcolor\\/)
      {
        my $topColor = $';
        while ($topColor =~ /(.*)\\/) { $topColor = $1; }
        $topColor =~ s/\s+$//;
        $player->topColor($topColor);
      }
    }    
  }
  elsif ($string =~ /^(.*) changed name to (.*)$/)
  {
    $player = findPlayerNoCreate($1);
    if ($player != null) 
    {
      $player->name($2);
      $team = findTeam($player->team);
      $team->addPlayer($2);
      $team->removePlayer($1);
    }
  }
  elsif ($string =~ /^(.*) min left$/) #ktpro timer
  {
    push(@graphTime, $1);
    push(@graphTeamOneScore, $teamOneScore);
    push(@graphTeamTwoScore, $teamTwoScore);
    foreach $player (@players)
    {
      $player->addScore($player->points);
      $player->minutesPlayed($player->minutesPlayed + 1);
    }
  }
  elsif ($string =~ /^(.*):(.*) left$/) #pure ctf timer
  {
    $minutes = $1;
    while ($minutes =~ /^0(.*)/) { $minutes = $1; }    
    if ($minutes eq "") { $minutes = 0; }

    #this is fairly ugly but yeah.. 

    my $redTeam = findTeam("red");
    my $blueTeam = findTeam("blue");
    if (@graphTime == 0 || $graphTime[@graphTime - 1] != $minutes)
    {
      push(@graphTime, $minutes);
      push(@graphTeamOneScore, $redTeam->points);
      push(@graphTeamTwoScore, $blueTeam->points);
      foreach $player (@players)
      {
	$player->addScore($player->points);
	$player->minutesPlayed($player->minutesPlayed + 1);
      }
    }
    else
    {
      $graphTeamOneScore[@graphTeamOneScore - 1] = $redTeam->points;
      $graphTeamTwoScore[@graphTeamTwoScore - 1] = $blueTeam->points;
      foreach $player (@players)
      {
	$player->removeScore();
        $player->addScore($player->points);
      }
    }
  }	 
  elsif ($string =~ /^\[(.*)\](.*):\[(.*)\](.*)$/) #ktpro score display
  {
    $teamOneScore = $2;
    $teamTwoScore = $4;
    $teamOneName = $1;
    $teamTwoName = $3;
  }
# once we reach this point the match is over and no good data remains
# breaking out of the loop not only provides a speed boost, but
# eliminates the disconnected player list from being added again
  elsif ($string =~ /. - disconnected player/) { last; }

#ugly stuff to add ctf support :/
  $oldString3 = $oldString2;
  $oldString2 = $oldString1;
  $oldString1 = $oldString;
  $oldString = $string;
  chomp($oldString);
}
if (@graphTime != 0 && $graphTime[@graphTime - 1] != 0)
{
  #kt pro
  push(@graphTime, 0);
  push(@graphTeamOneScore, $teamOneScore);
  push(@graphTeamTwoScore, $teamTwoScore);
}
else
{
  #pure ctf
  my $redTeam = findTeam("red");
  my $blueTeam = findTeam("blue");
  $graphTeamOneScore[@graphTeamOneScore - 1] = $redTeam->points;
  $graphTeamTwoScore[@graphTeamTwoScore - 1] = $blueTeam->points;
}
@graphTime = reverse(@graphTime);
push(@graphTeams, $teamOneName);
push(@graphTeams, $teamTwoName);

# first we add the last score to each players score array
# then if the size of the score array is smaller than the time
# array we pad it with leading zeroes
foreach $player (@players)
{
  $player->addScore($player->points);
  my @playerScoreArray = $player->scoreArray();
  for($i = 0; $i < @graphTime - @playerScoreArray; $i++)
  {
    $player->padScoreArray();
  }
}

# this seems like a suboptimal solution
my $teamOne = findTeam($teamOneName);
my $teamTwo = findTeam($teamTwoName);
my @tempGraphTime = @graphTime;
my $time = pop(@tempGraphTime);
$teamOne->minutesPlayed($time);
$teamTwo->minutesPlayed($time);
for (my $i = 0; $i <= $time; $i++)
{
  # do nothing if ==
  if ($graphTeamOneScore[$i] > $graphTeamTwoScore[$i])
  {
    $teamOne->minutesWithLead($teamOne->minutesWithLead() + 1);
  }
  elsif ($graphTeamTwoScore[$i] > $graphTeamOneScore[$i])
  {
    $teamTwo->minutesWithLead($teamTwo->minutesWithLead() + 1);
  }
}  
$shell = `rm -f "$tempMvd"`;
$shell = `gzip -9 "$mvd"`;
$mvd .= ".gz";
calculateTeamColors();

if ($DEBUG)
{
  print "Found " . @players . " players\n";
  foreach $player (@players)
  {
#     $team = findTeamNoCreate($player->team);
#     print $player->name . " " . $team->name . "\n";
  }
  for $i (0 .. @graphTime - 1)
  {
      print "$graphTime[$i] \t $graphTeamOneScore[$i] \t $graphTeamTwoScore[$i] \n";
  }
  print "@graphTime @graphTeamOneScore @graphTeamTwoScore\n";
}

outputForm();

# Searches player array for the name passed in
# Returns player object if found or new player object if not
sub findPlayer
{
  my $playerName = shift;
  foreach $player (@players)
  {
    if ($player->name() eq $playerName) { return $player }    
  }
  my $newPlayer = Player->new();
  $newPlayer->name($playerName);
  push(@players, $newPlayer);
  return $newPlayer;
}

sub findPlayerNoCreate
{
  my $playerName = shift;
  foreach $player (@players)
  {
    if ($player->name() eq $playerName) { return $player }
  }
  return null;
}

sub findTeam
{
  my $teamName = shift;
  foreach $team (@teams)
  {
    if ($team->name eq $teamName) { return $team }
  }
  my $newTeam = Team->new();
  $newTeam->name($teamName);
  push(@teams, $newTeam);
  return $newTeam;
}

sub findTeamNoCreate
{
  my $teamName = shift;
  foreach $team (@teams)
  {
    if ($team->name eq $teamName) { return $team }
  }
  return null;
}

sub teamMatchup
{
  my $teamOneFound = 0;
  my $teamTwoFound = 0; 

  # first lets try to find perfect matches (minus case sensitivity)
  foreach $team (@teams)
  {
    my $name = $team->name;
    #print "|$name|\n|$teamOneAbbr|\n|$teamTwoAbbr|\n\n";
    if ($teamOneAbbr =~ /^$name$/i && $name =~ /^$teamOneAbbr$/i)
    {
      $team->approved(1);
      $teamOneFound = 1;
    }
    elsif ($teamTwoAbbr =~ /^$name$/i && $name =~ /^$teamTwoAbbr$/i)
    {
      $team->approved(1);
      $teamTwoFound = 1;
    }
  }  
  
  # now for the non perfect matches
  foreach $team (@teams)
  {
    my $name = $team->name;
    if ($team->approved() == 0)
    {
      if ($teamOneFound == 0)
      {
        if ($teamOneAbbr =~ /$name/i || $name =~ /$teamOneAbbr/i)
        {
          $team->approved(1);
          $team->name($teamOneAbbr);
          $teamOneFound = 1;
        }
      }
      if ($teamTwoFound == 0)
      {
        if ($teamTwoAbbr =~ /$name/i || $name =~ /$teamTwoAbbr/i)
        {
          $team->approved(1);
          $team->name($teamTwoAbbr);
          $teamTwoFound = 1;
        }
      }
    }
  }
  if ($teamOneFound + $teamTwoFound == 2) { return; } #awesome!
  
  #print "total teams: $teamCount\n";  
  if ($teamOneFound + $teamTwoFound == 1 && @teams == 2) 
  # well we got 1 of 2 so we can assume the unknown is #2
  {
    my $lastTeam = undef;
    foreach $team (@teams)
    {
      $lastTeam = $team;
      if ($team->approved == 0) { last; }
    }
    if ($lastTeam->approved == 0) # should always be true here, but who knows
    {
      if ($teamOneFound == 0)
      {
	$lastTeam->name($teamOneAbbr);
        $lastTeam->approved(1);
        $teamOneFound = 1;
      }
      else
      {
	$lastTeam->name($teamTwoAbbr);
        $lastTeam->approved(1);
        $teamTwoFound = 1; 
      }
    }
  }
  else #Doh 
  {
#    print "no match\n";
  }
}

sub outputForm
{
   print "<form action='../?a=statCreation' method=post name='stats'>\n";
   print "\t<input type='hidden' name='tourney_id' value='$tourney_id'>\n";
   print "\t<input type='hidden' name='division_id' value='$division_id'>\n";
   print "\t<input type='hidden' name='match_id' value='$match_id'>\n";
   print "\t<input type='hidden' name='winning_team_id' value='$winning_team_id'>\n";
   print "\t<input type='hidden' name='approved' value='$approved'>\n";
   print "\t<input type='hidden' name='filename' value='$mvd'>\n";
   print "\t<input type='hidden' name='map' value='$map'>\n";

   if (@teams > 1 && @players > 0)
   {
     outputPlayerPieCharts();
     teamMatchup();
     
     print "\t<input type='hidden' name='teamStats' value='";
     print "Name\\\\Matched\\\\Score\\\\MinutesPlayed\\\\MinutesWithLead'>\n";

     my $teamNumber = 1;
     foreach $team (@teams)
     {
       my $a = $team->name; 
       my $b = $team->approved; 
       my $c = $team->points; 
       my $d = $team->minutesPlayed; 
       my $e = $team->minutesWithLead;
       print "\t<input type='hidden' name='team" . 
           $teamNumber . "' value='$a\\\\$b\\\\$c\\\\$d\\\\$e'>\n";
       my @tPlayers = $team->players;
       print "\t<input type='hidden' name='team" . $teamNumber . "players' value='";
       my $playerC = @tPlayers;
       my $currentC = 0;
       foreach $player (@tPlayers)
       {
 	  $currentC++;
	  $player = findPlayer($player);
	  $player->outputStats();
	  my $imagePath = $tempDir . $player->name . "_" . $map . ".png";
	  $imagePath =~ s/\s//g;
	  print $imagePath;
	  if ($currentC < $playerC) { print "\\\\"; }
       }
       print "'>\n";
       $teamNumber++;
     }

     my $imagePath = outputTeamScoreGraph(320, 200);
     print "\t<input type='hidden' name='team_score_graph_small' " . 
                                   "value='$imagePath'>\n";

     $imagePath = outputTeamScoreGraph(550, 480);
     print "\t<input type='hidden' name='team_score_graph_large' " . 
                                   "value='$imagePath'>\n";

     $imagePath = outputPlayerScoreGraph(550, 480);
     print "\t<input type='hidden' name='player_score_graph' " . 
                                   "value='$imagePath'>\n";  
   }

   print "\t<input type='hidden' name='playerFields' value='53'>\n";
   Player::outputStatsHeader();
  
   print "\t<input type='submit' value='Continue' name='B1' class='button'>\n";
   print "</form>\n";
#   print "<script>\n";
#   print "document.stats.submit();\n";
#   print "</script>\n";
}

sub outputPlayerScoreGraph
{
  my $x = 400; my $y = 300;
  if (@_) { $x = shift; $y = shift; } 
  if (@graphTime < 5) { return; }
  my @data = (\@graphTime);
  foreach $player (@players)
  { 
    my @scoreArray = $player->scoreArray();
    push(@data, \@scoreArray); 
    push(@legendPlayers, $player->name);
  }
  my $graph = GD::Graph::lines->new($x,$y);
  $graph->set(title => $teamOneName ." vs ". $teamTwoName . " (" . $map . ")",
              x_label => "time",
              x_label_position => .5,
              y_label => "score",
              line_width => 2
	      );
  my @colorArray = qw(red orange blue dgreen dyellow cyan marine purple);
  $graph->set(dclrs => [@colorArray]);
  $graph->set_legend(@legendPlayers);
  my $image = $graph->plot(\@data); # or die ("Died creating image");
  my $imagePath = $tempDir . $teamOneName . "_" . $teamTwoName . "_" . $map . "_" . "players_" . $x . "x" . $y . ".png";
  $imagePath =~ s/\s//g;
  open(OUT, ">$imagePath");
  binmode OUT;
  print OUT $image->png();
  close OUT;
  return $imagePath;
}

sub outputTeamScoreGraph
{
  my $x = 400; my $y = 300;
  if (@_) { $x = shift; $y = shift; }
#  if (@graphTime < 5 || @graphTeamOneScore < 1 || @graphTeamTwoScore < 1)
#  {
#    return;
#  } 
  if (@graphTime != @graphTeamOneScore || 
      @graphTeamOneScore != @graphTeamTwoScore) { return; }
  my @data = (\@graphTime, \@graphTeamOneScore, \@graphTeamTwoScore);
  my $graph = GD::Graph::lines->new($x,$y);
  $graph->set(title   => $teamOneName . " vs " . $teamTwoName . " (" . $map . ")", 
              x_label => "time", 
              x_label_position => .5,
              y_label => "score",
              line_width => 2
             );

  $graph->set_legend(@graphTeams);
  my $teamOne = findTeam($teamOneName);
  my $teamTwo = findTeam($teamTwoName);
  if ($teamOne->color == $teamTwo->color) 
  {
      $teamOne->color(complementColor($teamOne->color));
  }
  push(@colorArray, colorConverter($teamOne->color));
  push(@colorArray, colorConverter($teamTwo->color));
  $graph->set(dclrs => [@colorArray]);
  if ($x < 401)
  {
    $graph->set(x_label_skip => 5)
  }
  else
  { 
    my @pointData = undef;
    my @tempTime = @graphTime;
    my $timePlayed = pop(@tempTime);

    for ($i = 0; $i <= $timePlayed; $i++)
    {
      push(@{$pointData[0]}, $i);
      if ($graphTeamOneScore[$i] > $graphTeamTwoScore[$i])
      { 
        push(@{$pointData[1]}, $graphTeamOneScore[$i]-$graphTeamTwoScore[$i]);
        push(@{$pointData[2]}, undef);
      }
      else
      {
        push(@{$pointData[2]}, $graphTeamTwoScore[$i]-$graphTeamOneScore[$i]);
        push(@{$pointData[1]}, undef);
      }
    }
    $graph->set(show_values => \@pointData);
  }
  my $image = $graph->plot(\@data); # or die ("Died creating image");
  my $imagePath = $tempDir . $teamOneName . "_" . $teamTwoName . "_" . $map . "_" . $x . "x" . $y . ".png";
  $imagePath =~ s/\s//g;
  open(OUT, ">$imagePath");
  binmode OUT;
  print OUT $image->png();
  close OUT;
  return $imagePath;
}

sub outputPlayerPieCharts
{
  foreach $player (@players)
  { 
   if ($player->graphedFrags < 1) { next; }
    my @weaponList = ("SG " . $player->shotgunFrags(),
                      "SSG " . $player->ssgFrags(),
                      "NG " . $player->nailgunFrags(),
                      "SNG " . $player->sngFrags(),
                      "GL " . $player->grenadeFrags(),
                      "RL " . $player->rocketFrags(),
                      "LG " . $player->lightningFrags());
    my @stats = ($player->shotgunFrags(),
                 $player->ssgFrags(),
                 $player->nailgunFrags(),
                 $player->sngFrags(),
                 $player->grenadeFrags(),
                 $player->rocketFrags(), 
                 $player->lightningFrags()
                );
    my @data = (\@weaponList, \@stats);
    my $graph = GD::Graph::pie->new(250,175);
    $graph->set(title => "Frags by " . $player->name . " (" . 
                         $player->graphedFrags . ")",
                suppress_angle => 3
    ) or warn $graph->error;
    my @colorArray = qw(lred orange purple dgreen dyellow cyan marine);
    $graph->set(dclrs => [@colorArray]);
 
    my $image = $graph->plot(\@data); # or warn $graph->error;
    my $imagePath = $tempDir . $player->name . "_" . $map . ".png";
    $imagePath =~ s/\s//g;
    open(OUT, ">$imagePath");
    binmode OUT;
    print OUT $image->png();
    close OUT;    
  }
}

# returns the quake color corresponding to the number passed in
# white becomes black for display purposes
sub colorConverter
{
  if (!@_) { return "black" }
  my $color = shift;
  # why no switch in perl D:
  if ($color == 0) { return "black" }
  if ($color == 1) { return "brown" }
  if ($color == 2) { return "lblue" }
  if ($color == 3) { return "dgreen" }
  if ($color == 4) { return "red" }
  if ($color == 5) { return "yellow" }
  if ($color == 6) { return "pink" }
  if ($color == 7) { return "lbrown" }
  if ($color == 8) { return "purple" }
  if ($color == 9) { return "purple" }
  if ($color == 10) { return "lbrown" }
  if ($color == 11) { return "cyan" }
  if ($color == 12) { return "lyellow" }
  if ($color == 13) { return "blue" }
  return "black";
}

# available colors:
# white, lgray, gray, dgray, black, lblue, blue, dblue, gold, lyellow, yellow, dyellow, lgreen, green, dgreen, lred, red, dred, lpurple, purple, dpurple, lorange, orange, pink, dpink, marine, cyan, lbrown, dbrown.

#not yet implemented
sub complementColor
{
  my $color = shift;
  if ($color == 0) { return 4; }
  return 0;
}

sub calculateTeamColors
{
  foreach $team (@teams)
  {
    my @teamPlayers = $team->players;
    my @colors = [];
    foreach $player (@teamPlayers)
    {
      $player = findPlayer($player);
      push(@colors, $player->bottomColor);
    }
    @colors = reverse(sort(@colors));
    $modeColor = 0; $modeCount = 0; $currentColor = 0; $currentCount = 0;
    foreach $color (@colors)
    {
      if ($color == $currentColor)
      {
        $currentCount++;
        if ($currentCount > $modeCount)
        {
          $modeColor = $currentColor;
          $modeCount = $currentCount;
        }
      }
      else
      {
        $currentCount = 0;
        $currentColor = $color;
      }
    }
    $team->color($modeColor);
  }
}
