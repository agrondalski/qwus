#!/usr/bin/perl -w
use strict;

# todo:
# add real team name in addition to red\blue to graphs for ctf
# insane demo support for non tournament games--Started--refer from demoStats
# can dew support undef values?
# check ctf ax frags
# improve regexs
# bring ktpro stats up to par with pure ctf
# team stats (end of mvd)
# player minutes played ( * left the game)

use Benchmark;
use CGI qw/:standard/;
use GD::Graph::lines;
use GD::Graph::pie;
use GD::Graph::colour;
use mvdPlayer;
use mvdTeam;

my $DEBUG = 0;

package main;
my $teamOneScore = 0;
my $teamTwoScore = 0;
my $teamOneName = "red";
my $teamTwoName = "blue";
my $tempDir = "/tmp/";
my $oldSeconds = 0;
my $oldMinutes = 0;
my $mvd = shift(@ARGV);
my($tourney_id, $division_id, $match_id, $approved, $teamOneAbbr, $teamTwoAbbr,$teamOnePlayers,$teamTwoPlayers) = ("Test1","A","320","Yes","WTG","GTW","","");
my($start,$end);
my $cgi;
if (!$mvd eq "")
{
  $DEBUG = 1;
  print "-- Debug Enabled --\n";
}
else
{
  $cgi = new CGI;
  print "Content-type: text/html\n\n";
  my $referer = $ENV{"HTTP_REFERER"};
  if ($referer =~ /demoStats/)
  {
    $mvd = $cgi->param('filename');
    $tourney_id = -1;
  }
  elsif ($referer =~ /reportMatch/)
  {
    $tourney_id = $cgi->param('tourney_id');
    $division_id = $cgi->param('division_id');
    $match_id = $cgi->param('match_id');
    $approved = $cgi->param('approved');
    $mvd = $cgi->param('filename');
    $teamOneAbbr = $cgi->param('team1');
    $teamTwoAbbr = $cgi->param('team2');
    $teamOnePlayers = $cgi->param('team1players');
    $teamTwoPlayers = $cgi->param('team2players');
  }
  else { exit; }
}

if ($mvd =~ /(.*)\.gz$/)
{
  $start = new Benchmark;
  print "Uncompressing..\t\t";
  my $shell = `gzip -fd "$mvd"`;
  $mvd = $1;
  $end = new Benchmark;
  #print timestr(timediff($end,$start), 'all') . "<br>\n";
}

if ($mvd =~ /(.*)\.bz2$/)
{
  $start = new Benchmark;
  print "Uncompressing..\t\t";
  my $shell = `bzip2 -fd "$mvd"`;
  $mvd = $1;
  $end = new Benchmark;
  #print timestr(timediff($end,$start), 'all') . "<br>\n";
}

if ($mvd =~ /(.*)\.qwd$/)
{
  $start = new Benchmark;
  print "Converting to MVD..\t";
  my $shell = `qwdtools "$mvd"`;
  $mvd = $1 . ".mvd";
  $end = new Benchmark;
  #print timestr(timediff($end,$start), 'all') . "<br>\n";
}

if ($mvd !~ /(.*)\.mvd$/)
{
  $mvd = "";
  print "Error: Invalid MVD (possibly zipped?)<br>";
  exit();
}

my $tempMvd = $mvd . ".tmp";
$start = new Benchmark;
print "Converting ascii..\t";
my $shell = `sed -f convertAscii.sed "$mvd" > "$tempMvd"`;
$end = new Benchmark;
print timestr(timediff($end,$start), 'all') . "<br>\n";
$start = new Benchmark;
print "Generating strings..\t";
my @strings = `strings -1 "$tempMvd"`;
$end = new Benchmark;
print timestr(timediff($end,$start), 'all') . "<br>\n";
my $fraggee = undef;
my $fragger = undef;
my $stringCounter = -1;
print "Parsing strings..\t";
$start = new Benchmark;
my($oldString,$oldString1,$oldString2,$oldString3, $nextString);
my($map,$team,$player,$flagTime,$teamScore);
my(@teams,@graphTime,@graphTeamOneScore,@graphTeamTwoScore,@graphTeams);
my %players;
foreach my $string (@strings)
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
  #ROCKETS#######################
 # elsif($string =~ /uad /){
  #print "$string\n";die;
  #}
  elsif ($string =~ /'s\s*.*?\s*rocket/g)
  {
  	  
	  if ($string =~ /^(.*) was \w+ by (.*)'s\s(.uad\s)*rocket/) 
	  {
		  $fraggee = findPlayer(\%players,$1);
		  $fragger = findPlayer(\%players,$2);
	  }
	  elsif ($string =~ /^(.*) rides (.*)'s rocket/)
	  {
		$fraggee = findPlayer(\%players,$1);
		$fragger = findPlayer(\%players,$2);
	  }
	  else
	  {
		$fraggee = findPlayer(\%players,$oldString2);
		$fragger = findPlayer(\%players,$oldString);
	  }
	  $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
	  $fragger->rocketFrags($fragger->rocketFrags() + 1);
  }
elsif ($string =~ /^(.*) rips (.*)/)
  {
    $fragger = findPlayer(\%players,$1);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
    $fraggee = findPlayer(\%players,$2);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
  }
  #END ROCKETS#######################
  elsif ($string =~ /^(.*) accepts (.*)'s shaft/)
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->lightningDeaths($fraggee->lightningDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->lightningFrags($fragger->lightningFrags() + 1);
  }
  elsif ($string =~ /^'s shaft/)
  {
    $fraggee = findPlayer(\%players,$oldString2);
    $fraggee->lightningDeaths($fraggee->lightningDeaths() + 1);
    $fragger = findPlayer(\%players,$oldString);
    $fragger->lightningFrags($fragger->lightningFrags() + 1);
  }
    elsif ($string =~ /^(.*) eats (.*)'s pineapple/)
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^(.*) was gibbed by (.*)'s grenade/) 
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^'s pineapple/)
  {
    $fraggee = findPlayer(\%players,$oldString2);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = findPlayer(\%players,$oldString);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^'s grenade/)
  {
    $fraggee = findPlayer(\%players,$oldString2);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = findPlayer(\%players,$oldString);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^(.*) chewed on (.*)'s boomstick/) 
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->shotgunDeaths($fraggee->shotgunDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->shotgunFrags($fragger->shotgunFrags() + 1);
  }
  elsif ($string =~ /^ was punctured by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->sngDeaths($fraggee->sngDeaths() + 1);
    $fragger = findPlayer(\%players,$nextString);
    $fragger->sngFrags($fragger->sngFrags() + 1);
  }
  elsif ($string =~ /^(.*) was punctured by (.*)/) 
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->sngDeaths($fraggee->sngDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->sngFrags($fragger->sngFrags() + 1);
  }
  elsif ($string =~ /^ was hooked by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->grappleDeaths($fraggee->grappleDeaths() + 1);
    $fragger = findPlayer(\%players,$nextString);
    $fragger->grappleFrags($fragger->grappleFrags() + 1);
  }
  elsif ($string =~ /^ was nailed by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->nailgunDeaths($fraggee->nailgunDeaths() + 1);
    $fragger = findPlayer(\%players,$nextString);
    $fragger->nailgunFrags($fragger->nailgunFrags() + 1);
  }
  elsif ($string =~ /^(.*) was nailed by (.*)/) 
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->nailgunDeaths($fraggee->nailgunDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->nailgunFrags($fragger->nailgunFrags() + 1);
  }
  elsif ($string =~ /^(.*) ate 2 loads of (.*)'s buckshot/) 
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->ssgDeaths($fraggee->ssgDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->ssgFrags($fragger->ssgFrags() + 1);
  }
  elsif ($string =~ /^ was ax-murdered by/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->axDeaths($fraggee->axDeaths() + 1);
    $fragger = $strings[$stringCounter + 1];
    chomp($fragger);
    $fragger = findPlayer(\%players,$fragger);
    $fragger->axFrags($fragger->axFrags() + 1);
  }
  elsif ($string =~ /^(.*) was ax-murdered by (.*)/)
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->axDeaths($fraggee->axDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->axFrags($fragger->axFrags() + 1);
  }
  

  elsif ($string =~ /^(.*) becomes bored with life/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->rocketBores($fraggee->rocketBores() + 1);
  }
  #elsif ($string =~ /^(.*) discovers blast radius/)
  #{
  #  print $string;
  #  $fraggee = findPlayer(\%players,$1);
  #  $fraggee->rocketBores($fraggee->rocketBores() + 1);
  #}
  elsif ($string =~ /^ tries to put the pin back in/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->grenadeBores($fraggee->grenadeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the water/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the slime/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the lava/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  #elsif ($string =~ /^(.*) electrocutes himself/)
  #{
  #  print $string;
  #  $fraggee = findPlayer(\%players,$1);
  #  $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  #}
  elsif ($string =~ /^(.*) accepts (.*)'s discharge/)
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->dischargeDeaths($fraggee->dischargeDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->dischargeFrags($fragger->dischargeFrags() + 1);
  }
  elsif ($string =~ /^(.*) was squished/) 
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->squishBores($fraggee->squishBores() + 1);
  }
  elsif ($string =~ /^(.*) squished a teammate/)
  {
# doesnt effect score?  wrong it does!
    $fragger = findPlayer(\%players,$1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) squishes (.*)/)
  {
    $fraggee = findPlayer(\%players,$2);
    $fraggee->squishDeaths($fraggee->squishDeaths() + 1);
    $fragger = findPlayer(\%players,$1);
    $fragger->squishFrags($fragger->squishFrags() + 1);
  }
  elsif ($string =~ /^ visits the Volcano God/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }    
  elsif ($string =~ /^ burst into flames/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }
  elsif ($string =~ /^ turned into hot slag/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }
  elsif ($string =~ /^(.*) cratered/)
  {
  #  print $string;
    $fraggee = findPlayer(\%players,$1);
    $fraggee->fallBores($fraggee->fallBores() + 1);
  }
  elsif ($string =~ /^ fell to his death/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->fallBores($fraggee->fallBores() + 1);
  }
  elsif ($string =~ /^(.*) sleeps with the fishes/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->waterBores($fraggee->waterBores() + 1);
  }
  elsif ($string =~ /^(.*) sucks it down/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->waterBores($fraggee->waterBores() + 1);
  }
  elsif ($string =~ /^(.*) gulped a load of slime/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->slimeBores($fraggee->slimeBores() + 1);
  }
  elsif ($string =~ /^(.*) can't exist on slime alone/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->slimeBores($fraggee->slimeBores() + 1);
  }
  elsif ($string =~ /^ was spiked/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^(.*) tried to leave/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^(.*) died/)
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^ suicides/)
  {
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->miscBores($fraggee->miscBores() + 2);
  }
  
  
  elsif ($string =~ /^'s boomstick/)
  {
    $fraggee = findPlayer(\%players,$oldString2);
    $fraggee->shotgunDeaths($fraggee->shotgunDeaths() + 1);
    $fragger = findPlayer(\%players,$oldString);
    $fragger->shotgunFrags($fragger->shotgunFrags() + 1);
  }
  elsif ($string =~ /^'s buckshot/)
  {
    $fraggee = findPlayer(\%players,$oldString2);
    $fraggee->ssgDeaths($fraggee->ssgDeaths() + 1);
    $fragger = findPlayer(\%players,$oldString);
    $fragger->ssgFrags($fragger->ssgFrags() + 1);
  }
  elsif ($string =~ /^ captured the/)
  {
    $fragger = findPlayer(\%players,$oldString);
    $fragger->captures($fragger->captures() + 1);
    $nextString = $strings[$stringCounter + 1];
    if ($nextString =~ /capture took/)
    {
      my $minutes = $strings[$stringCounter + 2];
      my $seconds = $strings[$stringCounter + 4];
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
    $fragger = findPlayer(\%players,$oldString);
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
    $fragger = findPlayer(\%players,$oldString);
    $fragger->flagReturns($fragger->flagReturns() + 1);
  }
  elsif ($string =~ /^ got the /)
  {
    $fragger = findPlayer(\%players,$oldString);
    $fragger->flagPickups($fragger->flagPickups() + 1);
  }
  elsif ($string =~ /^ defends the/)
  {
    $fragger = findPlayer(\%players,$oldString);
    $fragger->flagDefends($fragger->flagDefends() + 1);
  }
  elsif ($string =~ /^'s flag carrier/)
  {
    $fragger = findPlayer(\%players,$oldString2);
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
    $fragger = findPlayer(\%players,$oldString);
    $fragger->flagDrops($fragger->flagDrops() + 1);
  }
  elsif ($string =~ /^ gets an assist for returning his flag/)
  {
    $fragger = findPlayer(\%players,$oldString);
    $fragger->returnAssists($fragger->returnAssists() + 1);
  }
  elsif ($string =~ /^ gets an assist for fragging/)
  {
    $fragger = findPlayer(\%players,$oldString);
    $fragger->fragAssists($fragger->fragAssists() + 1);
  }
  elsif ($string =~ /^ was telefragged by his teammate/) 
  {
    # this seems to have no effect on score in ktpro ??
    #$fraggee = findPlayer(\%players,$oldString);
    #$fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^ was telefragged by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = findPlayer(\%players,$oldString);
    $fraggee->teleDeaths($fraggee->teleDeaths() + 1);
    $fragger = findPlayer(\%players,$nextString);
    $fragger->teleFrags($fragger->teleFrags() + 1);
  }
  elsif ($string =~ /^(.*) was telefragged by (.*)/) 
  {
    $fraggee = findPlayer(\%players,$1);
    $fraggee->teleDeaths($fraggee->teleDeaths() + 1);
    $fragger = findPlayer(\%players,$2);
    $fragger->teleFrags($fragger->teleFrags() + 1);
  }
  #elsif ($string =~ /satan/i) # doesn't change score?
  #{
  #  print $string;
  #}
  elsif ($string =~ /^(.*) loses another friend/) 
  {
    $fragger = findPlayer(\%players,$1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) mows down a teammate/)
  {
    $fragger = findPlayer(\%players,$1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) checks his glasses/) 
  {
    $fragger = findPlayer(\%players,$1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  # ctf pings.. fairly hacked
  elsif ($string =~ /^\"DMSTATS\"/)
  {
    my $step = $stringCounter - 2;
    my $name = $strings[$step];
    my $previousPlayer = undef;
    while ($name !~ /^Name/)
    {
      chomp($name);
      $player = findPlayerNoCreate(\%players,$name);
      my $ping;
      if (defined($player) || $name =~ /^-----------/)
      {
        if (defined($previousPlayer))
        {
          for my $i (3 .. 6)
          {
	    $ping = $strings[$step + $i];
            chomp($ping);
            if (defined($ping) && $ping ne "")
            {
	      $previousPlayer->ping($ping);
            }
            if ($i == 5 && $previousPlayer->ping > 99) { last; }
          }
        }
        $previousPlayer = $player;
      }
      $step--;
      $name = $strings[$step];  
    }
  }
  elsif ($string =~ m/\\map\\/)
  {
    $map = $';
    while ($map =~ /(.*)\\/) { $map = $1; }
    $map =~ s/\s+$//;      
  }
  elsif ($string =~ m/\\name\\/)
  {
    my $name = $';
    if ($string =~ m/\\team\\/)
    {
      $team = $';
 # Dont bother with spectators
      if ($string =~ m/\\*spectator\\/i)
      {
        my $spec = $';
        while ($spec =~ /(.*)\\/) { $spec = $1; }
        $spec =~ s/\s+$//;
        if ($spec > 0) { next; }      
      }
      while ($team =~ /(.*)\\/) { $team = $1; }
      while ($name =~ /(.*)\\/) { $name = $1; }
      $name =~ s/\s+$//;
      $team =~ s/\s+$//;
      
      $player = findPlayer(\%players,$name); 
      # should prevent player rejoining game on different team
      if (!defined($player->team))
      {
        if (@teams < 2)
        {
	  $team = findTeam(\@teams,$team);
          $team->addPlayer($name);
          $player->team($team);
        }
        else
        {
	   $team = findTeamNoCreate(\@teams,$team);
           if (defined($team))
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
    $player = findPlayerNoCreate(\%players,$1);
    if (defined($player)) 
    {
      $player->name($2);
      $team = findTeam(\@teams,$player->team);
      $team->addPlayer($2);
      $team->removePlayer($1);
    }
  }
  elsif ($string =~ /^(.*) min left$/) #ktpro timer
  {
    push(@graphTime, $1);
    foreach $team (@teams)
    {
      if ($team->name eq $teamOneName) { $team->pushScore($teamOneScore); }
      if ($team->name eq $teamTwoName) { $team->pushScore($teamTwoScore); }
    }
    push(@graphTeamOneScore, $teamOneScore);
    push(@graphTeamTwoScore, $teamTwoScore);
    foreach $player (values %players)
    {
      $player->addScore($player->points);
      $player->minutesPlayed($player->minutesPlayed + 1);
    }
  }
  elsif ($string =~ /^(.*):(.*) left$/) #pure ctf timer
  {
    my $minutes = $1; 
    my $seconds = $2;
    while ($minutes =~ /^0(.*)/) { $minutes = $1; }    
    if ($minutes eq "") { $minutes = 0; }

    while ($seconds =~ /^0(.*)/) { $seconds = $1; }
    if ($seconds eq "") { $seconds = 0; }

    #this is fairly ugly but yeah.. 

    my $redTeam = findTeam(\@teams,"red");
    my $blueTeam = findTeam(\@teams,"blue");
    if (@graphTime == 0 || $graphTime[@graphTime - 1] != $minutes)
    {
      push(@graphTime, $minutes);
      $redTeam->pushScore($redTeam->points(\%players));
      $blueTeam->pushScore($blueTeam->points(\%players));
      foreach $player (values %players)
      {
	$player->addScore($player->points);
	$player->minutesPlayed($player->minutesPlayed + 1);
        if ($player->hasFlag)
        {
          $flagTime = (60 * $oldMinutes + $oldSeconds) -
	              (60 * $minutes + $seconds);
          $player->flagTime($player->flagTime + $flagTime);
        }
      }
    }
    else
    {
      $redTeam->popScore(); $redTeam->pushScore($redTeam->points(\%players));
      $blueTeam->popScore(); $blueTeam->pushScore($blueTeam->points(\%players));
      foreach $player (values %players)
      {
	$player->removeScore();
        $player->addScore($player->points);
        if ($player->hasFlag) 
        {
	  $flagTime = (60 * $oldMinutes + $oldSeconds) - 
                      (60 * $minutes + $seconds);
          $player->flagTime($player->flagTime + $flagTime);
        }
      }
    }
    $oldMinutes = $minutes;
    $oldSeconds = $seconds;
  }	 
  elsif ($string =~ /^\[(.*)\](.*):\[(.*)\](.*)$/) #ktpro score display
  {
    $teamOneScore = $2;
    $teamTwoScore = $4;
    $teamOneName = $1;
    $teamTwoName = $3;
  }
  #########################################################################
  #########################################################################
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
  my $redTeam = findTeam(\@teams,"red");
  my $blueTeam = findTeam(\@teams,"blue");
  $redTeam->popScore; $redTeam->pushScore($redTeam->points(\%players));
  $blueTeam->popScore; $blueTeam->pushScore($blueTeam->points(\%players));
  CleanUpScores(\%players);
  @graphTeamOneScore = $redTeam->getScoreArray;
  @graphTeamTwoScore = $blueTeam->getScoreArray;
  
}

@graphTime = reverse(@graphTime);
push(@graphTeams, $teamOneName);
push(@graphTeams, $teamTwoName);

# first we add the last score to each players score array
# then if the size of the score array is smaller than the time
# array we pad it with leading zeroes
foreach my $player (values %players)
{
  $player->addScore($player->points);
  my @playerScoreArray = $player->scoreArray();
  for(my $i = 0; $i < @graphTime - @playerScoreArray; $i++)
  {
    $player->padScoreArray();
  }
}

# this seems like a suboptimal solution
my $teamOne = findTeam(\@teams,$teamOneName);
my $teamTwo = findTeam(\@teams,$teamTwoName);
my @tempGraphTime = @graphTime;
my $time = pop(@tempGraphTime);
$teamOne->minutesPlayed($time);
$teamTwo->minutesPlayed($time);

for (my $i = 0; $i <= $time; $i++)
{
  if(!defined($graphTeamOneScore[$i]) || !defined($graphTeamTwoScore[$i]))
  {
  #check init of arrays
  next;
  }
  # do nothing if ==
  elsif ($graphTeamOneScore[$i] > $graphTeamTwoScore[$i])
  {
    $teamOne->minutesWithLead($teamOne->minutesWithLead() + 1);
  }
  elsif ($graphTeamTwoScore[$i] > $graphTeamOneScore[$i])
  {
    $teamTwo->minutesWithLead($teamTwo->minutesWithLead() + 1);
  }
} 
$end = new Benchmark;
print timestr(timediff($end,$start), 'all') . "<br>\n"; 
$shell = `rm -f "$tempMvd"`;
$start = new Benchmark;
print "Compressing..\t\t";
$shell = `gzip -f9 "$mvd"`;
$end = new Benchmark;
print timestr(timediff($end,$start), 'all') . "<br>\n";
$mvd .= ".gz";
#calculateTeamColors();

if ($DEBUG)
{
  foreach $team (@teams)
  {
    print $team->name . "\n";
    $team->playerList();
  }
}
outputForm(\%players,\@teams,$teamOneAbbr,$teamTwoAbbr, $tourney_id, $division_id, $match_id, $approved, $mvd, $map);
exit;
#only works for ctf games right now
sub CleanUpScores
{
  my $players = shift;
  foreach my $player (values %{$players})
  {
    if ($player->frags == 0 && $player->deaths == 0)
    {
      if (defined($player->team))
      {
	$team = findTeam(\@teams,$player->team->name);
        if (defined($team))
        {
	  $team->removePlayer($player->name);
	  my @tempArray = [];
          while ($player->scoreArray)
          {
	    $teamScore = $team->popScore();
	    $teamScore -= $player->removeScore();
	    push(@tempArray, $teamScore);
          }
          while (@tempArray - 1)  # this -1 is confusing me atm
          {
	    $team->pushScore(pop(@tempArray));
          }
        }
      }
    }
  }
}

# Searches player array for the name passed in
# Returns player object if found or new player object if not
sub findPlayer
{
  my($players, $playerName) = @_;
  
  	foreach my $player (values %{$players})
  	{
    		if ($player->name() eq $playerName) { return $player; }    
  	}
  
  my $newPlayer = Player->new();
  $newPlayer->name($playerName);
  $players->{$playerName} = $newPlayer;
  return $newPlayer;
}

sub findPlayerNoCreate
{
  my $players = shift;	
  my $playerName = shift;
  foreach my $player (values %{$players})
  {
    if ($player->name() eq $playerName) { return $player; }
  }
  return undef;
}

sub findTeam
{
  my $teams = shift;
  my $teamName = shift;
  foreach my $team (@{$teams})
  {
    if ($team->name eq $teamName) { return $team; }
  }
  my $newTeam = Team->new();
  $newTeam->name($teamName);
  push(@{$teams}, $newTeam);
  return $newTeam;
}

sub findTeamNoCreate
{
  my $teams = shift;
  my $teamName = shift;
  foreach $team (@{$teams})
  {
    if ($team->name eq $teamName) { return $team; }
  }
  return undef;
}

sub teamMatchup
{
  my $teams = shift;
  my $teamOneAbbr = shift;
  my $teamTwoAbbr = shift;
  my $teamOneFound = 0;
  my $teamTwoFound = 0; 

  # first lets try to find perfect matches (minus case sensitivity)
  foreach my $team (@{$teams})
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
  foreach my $team (@teams)
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
    foreach $team (@{$teams})
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
   my $players = shift;
   my $teams = shift;
   my $teamOneAbbr = shift;
   my $teamTwoAbbr = shift;
   my $tourney_id = shift;
   my $division_id = shift;
   my $match_id = shift;
   my $approved = shift;
   my $mvd = shift;
   my $map = shift;
   print "Generating Images and Output..";
   $start = new Benchmark;
   print "<form action='../?a=statCreation' method=post name='stats'>\n";
   print "\t<input type='hidden' name='tourney_id' value='$tourney_id'>\n";
   print "\t<input type='hidden' name='division_id' value='$division_id'>\n";
   print "\t<input type='hidden' name='match_id' value='$match_id'>\n";
   print "\t<input type='hidden' name='approved' value='$approved'>\n";
   print "\t<input type='hidden' name='filename' value='$mvd'>\n";
   print "\t<input type='hidden' name='map' value='$map'>\n";

   if (@{$teams} > 1 && (keys %{$players} > 0))
   {
     outputPlayerPieCharts($players);
     teamMatchup($teams,$teamOneAbbr,$teamTwoAbbr);
     
     print "\t<input type='hidden' name='teamStats' value='";
     print "Name\\\\Matched\\\\Score\\\\MinutesPlayed\\\\MinutesWithLead'>\n";

     my $teamNumber = 1;
     foreach my $team (@{$teams})
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
       foreach my $player (@tPlayers)
       {
 	  $currentC++;
	  $player = findPlayer(\%players,$player);
	  $player->outputStats();
	  my $imagePath = $tempDir . $player->name . "_" . $map . ".png";
	  $imagePath =~ s/\s//g;
	  print $imagePath;
	  if ($currentC < $playerC) { print "\\\\"; }
       }
       print "'>\n";
       $teamNumber++;
     }

     #my $imagePath = outputTeamScoreGraph(\@graphTime, \@graphTeamOneScore,\@graphTeamTwoScore,$teamOneName, $teamTwoName, $map,$tempDir,320,200);
#     print "\t<input type='hidden' name='team_score_graph_small' " . "value='$imagePath'>\n";
#
#     $imagePath = outputTeamScoreGraph(\@graphTime,\@graphTeamOneScore,\@graphTeamTwoScore,$teamOneName,$teamTwoName,$map,$tempDir,550,480);
#     print "\t<input type='hidden' name='team_score_graph_large' " . "value='$imagePath'>\n";

     my $imagePath = outputPlayerScoreGraph(\@graphTime,\@teams,$teamOneName,$teamTwoName,$map,$tempDir,550,480);
     print "\t<input type='hidden' name='player_score_graph' " . 
                                   "value='$imagePath'>\n";  
   }
   my $playerFields = `cat mvdPlayer.pm | grep print | grep -c self` + 1;
   print "\t<input type='hidden' name='playerFields' value='$playerFields'>\n";
   Player::outputStatsHeader();
  
   print "\t<input type='submit' value='Continue' name='B1' class='button'>\n";
   print "</form>\n";
   print "<script>\n";
   print "document.stats.submit();\n";
   print "</script>\n";
   $end = new Benchmark;
   print timestr(timediff($end,$start), 'all') . "<br>\n";

}

sub outputPlayerScoreGraph
{
  my $x = 400; my $y = 300;
  my $graphTime = shift;
  my $teams = shift;
  my $teamOneName = shift;
  my $teamTwoName = shift;
  my $map = shift;
  my $tempDir = shift;
  
  if (@_) { $x = shift; $y = shift; } 
  if (@{$graphTime} < 5) { return; }
  my @data = (\@{$graphTime});
  my @legendPlayers;
  foreach my $team (@{$teams})
  {
    foreach my $player ($team->players)
    { 
      $player = findPlayer(\%players,$player);
      my @scoreArray = $player->scoreArray();
      push(@data, \@scoreArray); 
      push(@legendPlayers, $player->name);
    }
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
#
sub outputTeamScoreGraph
{
  my $x = 400; my $y = 300;
  my $graphTime = shift;
  my $graphTeams = shift;
  my $graphTeamOneScore = shift;
  my $graphTeamTwoScore = shift;
  my $teamOneName = shift;
  my $teamTwoName = shift;
  my $map = shift;
  my $teams = shift;
  my $tempDir = shift;
  
  if (@_) { $x = shift; $y = shift; }
#  if (@graphTime < 5 || @graphTeamOneScore < 1 || @graphTeamTwoScore < 1)
#  {
#    return;
#  } 
  if (@{$graphTime} != @{$graphTeamOneScore} || 
      @{$graphTeamOneScore} != @{$graphTeamTwoScore}) { return; }
  my @data = ($graphTime, $graphTeamOneScore, $graphTeamTwoScore);
  my $graph = GD::Graph::lines->new($x,$y);
  $graph->set(title   => $teamOneName . " vs " . $teamTwoName . " (" . $map . ")", 
              x_label => "time", 
              x_label_position => .5,
              y_label => "score",
              line_width => 2
             );

  $graph->set_legend(@{$graphTeams});
  my $teamOne = findTeam($teams,$teamOneName);
  my $teamTwo = findTeam($teams,$teamTwoName);
  my(@colorArray);
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
    my @tempTime = @{$graphTime};
    my $timePlayed = pop(@tempTime);

    for (my $i = 0; $i <= $timePlayed; $i++)
    {
      push(@{$pointData[0]}, $i);
      if ($graphTeamOneScore->[$i] > $graphTeamTwoScore->[$i])
      { 
        push(@{$pointData[1]}, $graphTeamOneScore->[$i]-$graphTeamTwoScore->[$i]);
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
#
sub outputPlayerPieCharts
{
my $players = shift;
  foreach my $player (values %{$players})
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
  my $teams = shift;
  foreach my $team (@{$teams})
  {
    my @teamPlayers = $team->players;
    my @colors = [];
    foreach my $player (@teamPlayers)
    {
      $player = findPlayer(\%players,$player);
      push(@colors, $player->bottomColor);
    }
    @colors = reverse(sort(@colors));
    my($modeColor,$modeCount,$currentColor,$currentCount) = (0,0,0,0);
    foreach my $color (@colors)
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
