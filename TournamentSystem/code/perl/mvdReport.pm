#!/usr/bin/perl -w
use strict;
use lib ".";
use mvdPlayer;
use mvdTeam;
use qwGraph;

package mvdReport;

sub new
{
my $class = shift;
my $self = {};
	$self->{teamOneScore} = 0;
	$self->{teamTwoScore} = 0;
	$self->{teamOneName} = "red";
	$self->{teamTwoName} = "blue";
	$self->{tempDir} = "/tmp/";
	$self->{oldSeconds} = 0;
	$self->{oldMinutes} = 0;
	$self->{tourney_id} = ""; 
	$self->{division_id} = ""; 
	$self->{match_id} = ""; 
	$self->{approved} = ""; 
	$self->{teamOneAbbr} = ""; 
	$self->{teamTwoAbbr} = "";
	$self->{teamOnePlayers} = "";
	$self->{teamTwoPlayers} = "";
	$self->{mvdStrings} = [];
	$self->{'map'} = "";
	$self->{team} ="";
	$self->{player} = "";
	$self->{flagTime} = "";
	$self->{teamScore} ="";
	$self->{teams} = [];
	$self->{graphTime} = [];
	$self->{graphTeamOneScore} = [];
	$self->{graphTeamTwoScore} = [];
	$self->{graphTeams} = [];
	$self->{players} = {};
	$self->{mvd} = "";
	bless ($self, $class);
	return $self;
}

sub mvdtoStrings
{
my $self = shift;
my $mvd = shift;

if ($mvd =~ /(.*)\.gz$/)
{
  print "Uncompressing..\t\t";
  my $shell = `gzip -fd "$mvd"`;
  $mvd = $1;
}

if ($mvd =~ /(.*)\.bz2$/)
{
  print "Uncompressing..\t\t";
  my $shell = `bzip2 -fd "$mvd"`;
  $mvd = $1;
}

if ($mvd =~ /(.*)\.qwd$/)
{
  print "Converting to MVD..\t";
  my $shell = `qwdtools "$mvd"`;
  $mvd = $1 . ".mvd";
}

if ($mvd !~ /(.*)\.mvd$/)
{
  $mvd = "";
  print "Error: Invalid MVD (possibly zipped?)<br>";
  exit();
}
my $tempMvd = $mvd . ".tmp";
print "Converting ascii..\t";
my $shell = `sed -f convertAscii.sed "$mvd" > "$tempMvd"`;
print "Generating strings..\t";
my @strings = `strings -1 "$tempMvd"`;
print "Compressing..\t\t";
$shell = `rm -f "$tempMvd"`;
$shell = `gzip -f9 "$mvd"`;
$mvd .= ".gz";
$self->{mvd} = $mvd;
$self->{mvdStrings} = \@strings;
return 1;
}


sub parseStrings
{
my $self = shift;	
my $fraggee;
my $fragger;
my $stringCounter = -1;
my $team;
my $player;
print "Parsing strings..\t";
my $r =0;

my($oldString,$oldString1,$oldString2,$oldString3, $nextString);
my @strings = @{$self->{mvdStrings}};
$self->{mvdStrings} = ""; #write unset functions

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
		  $fraggee = $self->findPlayer($1);
		  $fragger = $self->findPlayer($2);
	  }
	  elsif ($string =~ /^(.*) rides (.*)'s rocket/)
	  {
		$fraggee = $self->findPlayer($1);
		$fragger = $self->findPlayer($2);
	  }
	  else
	  {
		$fraggee = $self->findPlayer($oldString2);
		$fragger = $self->findPlayer($oldString);
	  }
	  $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
	  $fragger->rocketFrags($fragger->rocketFrags() + 1);
  }
elsif ($string =~ /^(.*) rips (.*)/)
  {
    $fragger = $self->findPlayer($1);
    $fragger->rocketFrags($fragger->rocketFrags() + 1);
    $fraggee = $self->findPlayer($2);
    $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
  }
  #END ROCKETS#######################
  elsif ($string =~ /^(.*) accepts (.*)'s shaft/)
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->lightningDeaths($fraggee->lightningDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->lightningFrags($fragger->lightningFrags() + 1);
  }
  elsif ($string =~ /^'s shaft/)
  {
    $fraggee = $self->findPlayer($oldString2);
    $fraggee->lightningDeaths($fraggee->lightningDeaths() + 1);
    $fragger = $self->findPlayer($oldString);
    $fragger->lightningFrags($fragger->lightningFrags() + 1);
  }
    elsif ($string =~ /^(.*) eats (.*)'s pineapple/)
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^(.*) was gibbed by (.*)'s grenade/) 
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^'s pineapple/)
  {
    $fraggee = $self->findPlayer($oldString2);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = $self->findPlayer($oldString);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^'s grenade/)
  {
    $fraggee = $self->findPlayer($oldString2);
    $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
    $fragger = $self->findPlayer($oldString);
    $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
  }
  elsif ($string =~ /^(.*) chewed on (.*)'s boomstick/) 
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->shotgunDeaths($fraggee->shotgunDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->shotgunFrags($fragger->shotgunFrags() + 1);
  }
  elsif ($string =~ /^ was punctured by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = $self->findPlayer($oldString);
    $fraggee->sngDeaths($fraggee->sngDeaths() + 1);
    $fragger = $self->findPlayer($nextString);
    $fragger->sngFrags($fragger->sngFrags() + 1);
  }
  elsif ($string =~ /^(.*) was punctured by (.*)/) 
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->sngDeaths($fraggee->sngDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->sngFrags($fragger->sngFrags() + 1);
  }
  elsif ($string =~ /^ was hooked by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = $self->findPlayer($oldString);
    $fraggee->grappleDeaths($fraggee->grappleDeaths() + 1);
    $fragger = $self->findPlayer($nextString);
    $fragger->grappleFrags($fragger->grappleFrags() + 1);
  }
  elsif ($string =~ /^ was disemboweled by/)
  {
  $nextString = $strings[$stringCounter + 1];
  chomp($nextString);
  $fraggee = $self->findPlayer($oldString);
  $fraggee->grappleDeaths($fraggee->grappleDeaths() + 1);
  $fragger = $self->findPlayer($nextString);
  $fragger->grappleFrags($fragger->grappleFrags() + 1);
  } 
  elsif ($string =~ /^ was nailed by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = $self->findPlayer($oldString);
    $fraggee->nailgunDeaths($fraggee->nailgunDeaths() + 1);
    $fragger = $self->findPlayer($nextString);
    $fragger->nailgunFrags($fragger->nailgunFrags() + 1);
  }
  elsif ($string =~ /^(.*) was nailed by (.*)/) 
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->nailgunDeaths($fraggee->nailgunDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->nailgunFrags($fragger->nailgunFrags() + 1);
  }
  elsif ($string =~ /^(.*) ate 2 loads of (.*)'s buckshot/) 
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->ssgDeaths($fraggee->ssgDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->ssgFrags($fragger->ssgFrags() + 1);
  }
  elsif ($string =~ /^ was ax-murdered by/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->axDeaths($fraggee->axDeaths() + 1);
    $fragger = $strings[$stringCounter + 1];
    chomp($fragger);
    $fragger = $self->findPlayer($fragger);
    $fragger->axFrags($fragger->axFrags() + 1);
  }
  elsif ($string =~ /^(.*) was ax-murdered by (.*)/)
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->axDeaths($fraggee->axDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->axFrags($fragger->axFrags() + 1);
  }
  

  elsif ($string =~ /^(.*) becomes bored with life/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->rocketBores($fraggee->rocketBores() + 1);
  }
  #elsif ($string =~ /^(.*) discovers blast radius/)
  #{
  #  print $string;
  #  $fraggee = $self->findPlayer($1);
  #  $fraggee->rocketBores($fraggee->rocketBores() + 1);
  #}
  elsif ($string =~ /^ tries to put the pin back in/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->grenadeBores($fraggee->grenadeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the water/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the slime/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  elsif ($string =~ /^ discharges into the lava/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  }
  #elsif ($string =~ /^(.*) electrocutes himself/)
  #{
  #  print $string;
  #  $fraggee = $self->findPlayer($1);
  #  $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
  #}
  elsif ($string =~ /^(.*) accepts (.*)'s discharge/)
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->dischargeDeaths($fraggee->dischargeDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->dischargeFrags($fragger->dischargeFrags() + 1);
  }
  elsif ($string =~ /^(.*) was squished/) 
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->squishBores($fraggee->squishBores() + 1);
  }
  elsif ($string =~ /^(.*) squished a teammate/)
  {
# doesnt effect score?  wrong it does!
    $fragger = $self->findPlayer($1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) squishes (.*)/)
  {
    $fraggee = $self->findPlayer($2);
    $fraggee->squishDeaths($fraggee->squishDeaths() + 1);
    $fragger = $self->findPlayer($1);
    $fragger->squishFrags($fragger->squishFrags() + 1);
  }
  elsif ($string =~ /^ visits the Volcano God/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }    
  elsif ($string =~ /^ burst into flames/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }
  elsif ($string =~ /^ turned into hot slag/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }
  elsif ($string =~ /^(.*) cratered/)
  {
  #  print $string;
    $fraggee = $self->findPlayer($1);
    $fraggee->fallBores($fraggee->fallBores() + 1);
  }
  elsif ($string =~ /^ fell to his death/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->fallBores($fraggee->fallBores() + 1);
  }
  elsif ($string =~ /^(.*) sleeps with the fishes/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->waterBores($fraggee->waterBores() + 1);
  }
  elsif ($string =~ /^(.*) sucks it down/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->waterBores($fraggee->waterBores() + 1);
  }
  elsif ($string =~ /^(.*) gulped a load of slime/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->slimeBores($fraggee->slimeBores() + 1);
  }
  elsif ($string =~ /^(.*) can't exist on slime alone/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->slimeBores($fraggee->slimeBores() + 1);
  }
  elsif ($string =~ /^ was spiked/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^(.*) tried to leave/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^(.*) died/)
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^ suicides/)
  {
    $fraggee = $self->findPlayer($oldString);
    $fraggee->miscBores($fraggee->miscBores() + 2);
  }
  elsif ($string =~ /^'s boomstick/)
  {
    $fraggee = $self->findPlayer($oldString2);
    $fraggee->shotgunDeaths($fraggee->shotgunDeaths() + 1);
    $fragger = $self->findPlayer($oldString);
    $fragger->shotgunFrags($fragger->shotgunFrags() + 1);
  }
  elsif ($string =~ /^'s buckshot/)
  {
    $fraggee = $self->findPlayer($oldString2);
    $fraggee->ssgDeaths($fraggee->ssgDeaths() + 1);
    $fragger = $self->findPlayer($oldString);
    $fragger->ssgFrags($fragger->ssgFrags() + 1);
  }
  elsif ($string =~ /^ captured the/)
  {
    $fragger = $self->findPlayer($oldString);
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
    $fragger = $self->findPlayer($oldString);
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
    $fragger = $self->findPlayer($oldString);
    $fragger->flagReturns($fragger->flagReturns() + 1);
  }
  elsif ($string =~ /^ got the /)
  {
    $fragger = $self->findPlayer($oldString);
    $fragger->flagPickups($fragger->flagPickups() + 1);
  }
  elsif ($string =~ /^ defends the/)
  {
    $fragger = $self->findPlayer($oldString);
    $fragger->flagDefends($fragger->flagDefends() + 1);
  }
  elsif ($string =~ /^'s flag carrier/)
  {
    $fragger = $self->findPlayer($oldString2);
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
    $fragger = $self->findPlayer($oldString);
    $fragger->flagDrops($fragger->flagDrops() + 1);
  }
  elsif ($string =~ /^ gets an assist for returning his flag/)
  {
    $fragger = $self->findPlayer($oldString);
    $fragger->returnAssists($fragger->returnAssists() + 1);
  }
  elsif ($string =~ /^ gets an assist for fragging/)
  {
    $fragger = $self->findPlayer($oldString);
    $fragger->fragAssists($fragger->fragAssists() + 1);
  }
  elsif ($string =~ /^ was telefragged by his teammate/) 
  {
    # this seems to have no effect on score in ktpro ??
    #$fraggee = $self->findPlayer($oldString);
    #$fraggee->miscBores($fraggee->miscBores() + 1);
  }
  elsif ($string =~ /^ was telefragged by/)
  {
    $nextString = $strings[$stringCounter + 1];
    chomp($nextString);
    $fraggee = $self->findPlayer($oldString);
    $fraggee->teleDeaths($fraggee->teleDeaths() + 1);
    $fragger = $self->findPlayer($nextString);
    $fragger->teleFrags($fragger->teleFrags() + 1);
  }
  elsif ($string =~ /^(.*) was telefragged by (.*)/) 
  {
    $fraggee = $self->findPlayer($1);
    $fraggee->teleDeaths($fraggee->teleDeaths() + 1);
    $fragger = $self->findPlayer($2);
    $fragger->teleFrags($fragger->teleFrags() + 1);
  }
  #elsif ($string =~ /satan/i) # doesn't change score?
  #{
  #  print $string;
  #}
  elsif ($string =~ /^(.*) loses another friend/) 
  {
    $fragger = $self->findPlayer($1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) mows down a teammate/)
  {
    $fragger = $self->findPlayer($1);
    $fragger->teamKills($fragger->teamKills() + 1);
  }
  elsif ($string =~ /^(.*) checks his glasses/) 
  {
    $fragger = $self->findPlayer($1);
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
      $player = $self->findPlayerNoCreate($name);
      my $ping = 0;
      if (defined($player) || $name =~ /^-----------/)
      {
        if (defined($previousPlayer))
        {
          for my $i (3 .. 6)
          {
	    $ping = $strings[$step + $i];
            chomp($ping);
            if (defined($ping) && $ping ne "" && !($ping =~ /^\s*$/))
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
    my $map = $';
    while ($map =~ /(.*)\\/) { $map = $1; }
    $map =~ s/\s+$//;      
    $self->{'map'} = $map;
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
      
      $player = $self->findPlayer($name); 
      # should prevent player rejoining game on different team
      if (!defined($player->team))
      {
        if (@{$self->{teams}} < 2)
        {
	  $team = $self->findTeam($team);
          $team->addPlayer($name);
          $player->team($team);
        }
        else
        {
	   
	   $team = $self->findTeamNoCreate($team);
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
    $player = $self->findPlayerNoCreate($1);
    if (defined($player)) 
    {
      $player->name($2);
      $team = $self->findTeam($player->team);
      $team->addPlayer($2);
      $team->removePlayer($1);
    }
  }
  elsif ($string =~ /^(.*) min left$/) #ktpro timer
  {
    push(@{$self->{graphTime}}, $1);
    foreach $team (@{$self->{teams}})
    {
      if ($team->name eq $self->{teamOneName}) { $team->pushScore($self->{teamOneScore}); }
      if ($team->name eq $self->{teamTwoName}) { $team->pushScore($self->{teamTwoScore}); }
    }
    push(@{$self->{graphTeamOneScore}}, $self->{teamOneScore});
    push(@{$self->{graphTeamTwoScore}}, $self->{teamTwoScore});
    foreach $player (values %{$self->{players}})
    {
      $player->addScore($player->points($self));
      $player->minutesPlayed($player->minutesPlayed + 1);
    }
  }
  elsif ($string =~ /^(.*):(.*) left$/) #pure ctf timer
  {
    my $minutes = $1; 
    my $seconds = $2;
    my $oldMinutes = $self->{oldMinutes};
    my $oldSeconds = $self->{oldSeconds};
    while ($minutes =~ /^0(.*)/) { $minutes = $1; }    
    if ($minutes eq "") { $minutes = 0; }

    while ($seconds =~ /^0(.*)/) { $seconds = $1; }
    if ($seconds eq "") { $seconds = 0; }

    #this is fairly ugly but yeah.. 

    my $redTeam = $self->findTeam("red");
    my $blueTeam = $self->findTeam("blue");
    my $flagTime;
    if (@{$self->{graphTime}} == 0 || $self->{graphTime}[@{$self->{graphTime}} - 1] != $minutes)
    {
      push(@{$self->{graphTime}}, $minutes);
      $redTeam->pushScore($redTeam->points($self));
      $blueTeam->pushScore($blueTeam->points($self));
      foreach $player (values %{$self->{players}})
      {
	$player->addScore($player->points($self));
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
      $redTeam->popScore(); $redTeam->pushScore($redTeam->points($self));
      $blueTeam->popScore(); $blueTeam->pushScore($blueTeam->points($self));
      foreach $player (values %{$self->{players}})
      {
	$player->removeScore();
        $player->addScore($player->points($self));
        if ($player->hasFlag) 
        {
	  $flagTime = (60 * $oldMinutes + $oldSeconds) - 
                      (60 * $minutes + $seconds);
          $player->flagTime($player->flagTime + $flagTime);
        }
      }
    }
    $self->{oldMinutes} = $minutes;
    $self->{oldSeconds} = $seconds;
  }	 
  elsif ($string =~ /^\[(.*)\](.*):\[(.*)\](.*)$/) #ktpro score display
  {
    $self->{teamOneScore} = $2;
    $self->{teamTwoScore} = $4;
    $self->{teamOneName} = $1;
    $self->{teamTwoName} = $3;
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
#print "start step2\n";
if (@{$self->{graphTime}} != 0 && $self->{graphTime}[@{$self->{graphTime}} - 1] != 0)
{
  #kt pro
  push(@{$self->{graphTime}}, 0);
  push(@{$self->{graphTeamOneScore}}, $self->{teamOneScore});
  push(@{$self->{graphTeamTwoScore}}, $self->{teamTwoScore});
}
else
{
  #pure ctf
  my $redTeam = $self->findTeam("red");
  my $blueTeam = $self->findTeam("blue");
  $redTeam->popScore; $redTeam->pushScore($redTeam->points($self));
  $blueTeam->popScore; $blueTeam->pushScore($blueTeam->points($self));
  $self->CleanUpScores();
  @{$self->{graphTeamOneScore}} = $redTeam->getScoreArray;
  @{$self->{graphTeamTwoScore}} = $blueTeam->getScoreArray;
  
}
my @graphTime = @{$self->{graphTime}};
@graphTime = reverse(@graphTime);
my @graphTeams = @{$self->{graphTeams}};
push(@graphTeams, $self->{teamOneName});
push(@graphTeams,  $self->{teamTwoName});
$self->{graphTime} = \@graphTime;
$self->{graphTeams} = \@graphTeams;
# first we add the last score to each players score array
# then if the size of the score array is smaller than the time
# array we pad it with leading zeroes
foreach my $player (values %{$self->{players}})
{
  $player->addScore($player->points($self));
  my @playerScoreArray = $player->scoreArray();
  for(my $i = 0; $i < @graphTime - @playerScoreArray; $i++)
  {
    $player->padScoreArray();
  }
}

# this seems like a suboptimal solution
my $teamOne = $self->findTeam($self->{teamOneName});
my $teamTwo = $self->findTeam($self->{teamTwoName});
my @tempGraphTime = @graphTime;
my $time = pop(@tempGraphTime);
$teamOne->minutesPlayed($time);
$teamTwo->minutesPlayed($time);

for (my $i = 0; $i <= $time; $i++)
{
  if(!defined(${$self->{graphTeamOneScore}}[$i]) || !defined(${$self->{graphTeamTwoScore}}[$i]))
  {
  #check init of arrays
  next;
  }
  # do nothing if ==
  elsif (${$self->{graphTeamOneScore}}[$i] > ${$self->{graphTeamTwoScore}}[$i])
  {
    $teamOne->minutesWithLead($teamOne->minutesWithLead() + 1);
  }
  elsif (${$self->{graphTeamTwoScore}}[$i] > ${$self->{graphTeamOneScore}}[$i])
  {
    $teamTwo->minutesWithLead($teamTwo->minutesWithLead() + 1);
  }
}

}

sub CleanUpScores
{
  my $self = shift;
  foreach my $player (values %{$self->{players}})
  {
    if ($player->frags == 0 && $player->deaths == 0)
    {
      if (defined($player->team))
      {
	my $team = $self->findTeam($player->team->name);
        if (defined($team))
        {
	  $team->removePlayer($player->name);
	  my @tempArray = [];
          while ($player->scoreArray)
          {
	    my $teamScore = $team->popScore();
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
	my $self = shift;
	my($playerName) = shift;
  	#if(!defined($playerName)){return undef;}
	$playerName =~ s/ /\_/g;
  	foreach my $player (values %{$self->{players}})
  	{
    		if ($player->name() eq $playerName) { return $player; }    
  	}
  
  my $newPlayer = Player->new();
  $newPlayer->name($playerName);
  $self->{players}->{$playerName} = $newPlayer;
  return $newPlayer;
}

sub findPlayerNoCreate
{
	my $self = shift;	
	my $playerName = shift;
  foreach my $player (values %{$self->{players}})
  {
    if ($player->name() eq $playerName) { return $player; }
  }
  return undef;
}

sub findTeam
{
  my $self = shift;
  my $teamName = shift;

  foreach my $team (@{$self->{teams}})
  {
    if ($team->name eq $teamName) { return $team; }
  }

  my $newTeam = Team->new();
  $newTeam->name($teamName);
  push(@{$self->{teams}}, $newTeam);
  return $newTeam;
}

sub findTeamNoCreate
{
  my $self = shift;
  my $teamName = shift;
  
  foreach my $team (@{$self->{teams}})
  {
    if ($team->name eq $teamName) { return $team; }
  }
  return undef;
}

sub teamMatchup
{
  my $self = shift;
  my $teamOneAbbr = $self->{teamOneAbbr};
  my $teamTwoAbbr = $self->{teamTwoAbbr};
  my $teamOneFound = 0;
  my $teamTwoFound = 0; 
  my @teams = @{$self->{teams}};
  #die "$teamOneAbbr $teamTwoAbbr\n";
  # first lets try to find perfect matches (minus case sensitivity)
  foreach my $team (@teams)
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
    foreach my $team (@teams)
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
	my $self = shift;
   	my $players = $self->{players};
  	my $teams = $self->{teams};
   	my $teamOneAbbr = $self->{teamOneAbbr};
   	my $teamTwoAbbr = $self->{teamTwoAbbr};
   	my $tourney_id = $self->{tourney_id};
   	my $division_id = $self->{division_id};
   	my $match_id = $self->{match_id};
   	my $approved = $self->{approved};
   	my $mvd = $self->{mvd};
   	my $map = $self->{map};
	my $tempDir = $self->{tempDir};
   print "Generating Images and Output..";
   print "\n";
   print "<form action='../?a=statCreation' method=post name='stats'>\n";
   print "\t<input type='hidden' name='tourney_id' value='$tourney_id'>\n";
   print "\t<input type='hidden' name='division_id' value='$division_id'>\n";
   print "\t<input type='hidden' name='match_id' value='$match_id'>\n";
   print "\t<input type='hidden' name='approved' value='$approved'>\n";
   print "\t<input type='hidden' name='filename' value='$mvd'>\n";
   print "\t<input type='hidden' name='map' value='$map'>\n";

   if (@{$teams} > 1 && (keys %{$players} > 0))
   {
     $self->outputPlayerPieCharts();
     $self->teamMatchup();
     
     Team::outputStatsHeader();
     my $teamNumber = 1;   
     foreach my $team (@{$teams})
     { 
       print "\t<input type='hidden' name='team" . $teamNumber . "' value='";
       $team->outputStats($self);  
       my @tPlayers = $team->players;
       print "\t<input type='hidden' name='team" . $teamNumber . "players' value='";
       my $playerC = @tPlayers;
       my $currentC = 0;
       foreach my $player (@tPlayers)
       {
 	  $currentC++;
	  $player = $self->findPlayer($player);
	  
	  $player->outputStats($self);
	 
	  my $imagePath = $player->{PIE_CHART};
	  print $imagePath;
	  if ($currentC < $playerC) { print "\\\\"; }
       }
       print "'>\n";
       $teamNumber++;
     }

     my $imagePath = $self->outputTeamScoreGraph(320,200);
     print "\t<input type='hidden' name='team_score_graph_small' " . "value='$imagePath'>\n";

     $imagePath = $self->outputTeamScoreGraph(550,480);
     print "\t<input type='hidden' name='team_score_graph_large' " . "value='$imagePath'>\n";

     $imagePath = $self->outputPlayerScoreGraph(550,480);
     print "\t<input type='hidden' name='player_score_graph' " . 
                                   "value='$imagePath'>\n";  
   }
   my $playerFields = `cat mvdPlayer.pm | grep print | grep -c self` + 1;
   print "\t<input type='hidden' name='playerFields' value='$playerFields'>\n";
   Player::outputStatsHeader();
  
   print "\t<input type='submit' value='Continue' name='B1' class='button'>\n";
   print "</form>\n";
#   print "<script>\n";
#   print "document.stats.submit();\n";
#   print "</script>\n";
}
#
sub outputPlayerScoreGraph
{
  my $self = shift;
  my $x = 400; my $y = 300;
  if (@_) { $x = shift; $y = shift; } 
  
  if (@{$self->{graphTime}} < 5) { return; }
  my @data = ($self->{graphTime});
  my @legendPlayers;
  foreach my $team (@{$self->{teams}})
  {
    foreach my $player ($team->players)
    { 
      $player = $self->findPlayer($player);
      my @scoreArray = $player->scoreArray();
      push(@data, \@scoreArray); 
      push(@legendPlayers, $player->name);
    }
  }
  
  my @colorArray = qw(red orange blue dgreen dyellow cyan marine purple);
 
  my %qwhash = (	'data'=>\@data,
    			'x'	=> $x,
			'y'	=> $y,
			'x_label'=>"time",
			'y_label'=>"score",
			'legend'=> \@legendPlayers,
			'title'	=> $self->{teamOneName} ." vs ". $self->{teamTwoName} . " (" . $self->{map} . ")",
    			'colors'=> \@colorArray,
			'imagePath'=> $self->{tempDir} . $self->{teamOneName} . " vs " . $self->{teamTwoName} . "_players_(" . $self->{map} . ")_" . $x . "x" . $y . ".png"
			);
    
   
    my $imagePath = qwGraph::line_graph(\%qwhash);
    return $imagePath;
}
##
sub outputTeamScoreGraph
{
	my $self = shift;	
  	my $x = 400; 
	my $y = 300;
	if (@_) { $x = shift; $y = shift; }
  	my @graphTime = @{$self->{graphTime}};
  	my @graphTeams = @{$self->{graphTeams}};
  	my @graphTeamOneScore = @{$self->{graphTeamOneScore}};
  	my @graphTeamTwoScore = @{$self->{graphTeamTwoScore}};
  	my $teamOneName = $self->{teamOneName};
  	my $teamTwoName = $self->{teamTwoName};
  	my @teams = @{$self->{teams}};
  	my $tempDir = $self->{tempDir};
	my @pointData;

  if ((@graphTime != @graphTeamOneScore) || (@graphTeamOneScore != @graphTeamTwoScore)) { return "0"; }
  my @data = (\@graphTime, \@graphTeamOneScore, \@graphTeamTwoScore);
   
  my $teamOne = $self->findTeam($teamOneName);
  my $teamTwo = $self->findTeam($teamTwoName);
  
  my(@colorArray);
  if ($teamOne->color == $teamTwo->color) 
  {
      $teamOne->color(complementColor($teamOne->color));
  }
  push(@colorArray, colorConverter($teamOne->color));
  push(@colorArray, colorConverter($teamTwo->color));
  
  if ($x < 401)
  {
   
  }
  else
  { 
    @pointData = undef;
    my @tempTime = @graphTime;
    my $timePlayed = pop(@tempTime);
    for (my $i = 0; $i <= $timePlayed; $i++)
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
    
  }
  
  my %qwhash = (	'data'=>\@data,
    			'x'	=> $x,
			'y'	=> $y,
			'x_label'=>"time",
			'y_label'=>"score",
			'showvalues'=>\@pointData,
			'legend'=> \@graphTeams,
			'title'	=> $teamOneName ." vs ". $teamTwoName . " (" . $self->{map} . ")",
    			'colors'=> \@colorArray,
                        'imagePath'=> $tempDir . $teamOneName . " vs " . $teamTwoName . "_(" . $self->{map} . ")_" . $x . "x" . $y . ".png"
			);
  my $imagePath = qwGraph::line_graph(\%qwhash);
  return $imagePath;
}

##
sub outputPlayerPieCharts
{
my $self = shift;
  foreach my $player (values %{$self->{'players'}})
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
    my @colorArray = qw(lred orange purple dgreen dyellow cyan marine);
    my %qwhash = (	'data'=>\@data,
    			'x'	=> '250',
			'y'	=> '175',
			'title'	=> "Frags by " . $player->name . " (" . $player->graphedFrags . ")",
    			'colors'=> \@colorArray,
			'tempDir'=> $self->{'tempDir'}
			);
 
    my $imagePath = qwGraph::pie_graph(\%qwhash);
    $player->{PIE_CHART} = "$imagePath";	  
  
  }
}
#
## returns the quake color corresponding to the number passed in
## white becomes black for display purposes
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

## available colors:
## white, lgray, gray, dgray, black, lblue, blue, dblue, gold, lyellow, yellow, dyellow, lgreen, green, dgreen, lred, red, dred, lpurple, purple, dpurple, lorange, orange, pink, dpink, marine, cyan, lbrown, dbrown.
#
##not yet implemented
sub complementColor
{
  my $color = shift;
  if ($color == 0) { return 4; }
  return 0;
}

sub calculateTeamColors
{
my $self = shift;
  my $teams = $self->{teams};
  foreach my $team (@{$teams})
  {
    my @teamPlayers = $team->players;
    my @colors = [];
    foreach my $player (@teamPlayers)
    {
      $player = $self->findPlayer($player);
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
1;
