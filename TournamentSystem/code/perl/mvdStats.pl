#!/usr/bin/perl

# todo:
# nice output for easy database entry
# misc bs
# optimize
# ctf msgs
# have to check and double check score calculations
# graphs

use utf8;
use Benchmark;

package Player;
sub new
{
  my $class = shift;
  my $self = {};
  $self->{NAME} = undef;
  $self->{TEAM} = undef;
  $self->{ROCKET_FRAGS} = 0;    $self->{ROCKET_DEATHS} = 0;
  $self->{SHOTGUN_FRAGS} = 0;   $self->{SHOTGUN_DEATHS} = 0;
  $self->{SSG_FRAGS} = 0;       $self->{SSG_DEATHS} = 0;
  $self->{NAILGUN_FRAGS} = 0;   $self->{NAILGUN_DEATHS} = 0;
  $self->{SNG_FRAGS} = 0;       $self->{SNG_DEATHS} = 0;
  $self->{GRENADE_FRAGS} = 0;   $self->{GRENADE_DEATHS} = 0;
  $self->{LIGHTNING_FRAGS} = 0; $self->{LIGHTNING_DEATHS} = 0;
  $self->{AX_FRAGS} = 0;        $self->{AX_DEATHS} = 0;
  $self->{TELEFRAGS} = 0;       $self->{TELEDEATHS} = 0;
  $self->{LAVA_DEATHS} = 0;
  $self->{SLIME_DEATHS} = 0;
  $self->{WATER_DEATHS} = 0;
  $self->{FALL_DEATHS} = 0;
  $self->{SQUISH_FRAGS} = 0;    $self->{SQUISH_DEATHS} = 0;
  $self->{SQUISH_BORES} = 0;
  $self->{MISC_BORES} = 0;
 #  $self->{SATAN_FRAGS} = 0;
  $self->{ROCKET_BORES} = 0;
  $self->{GRENADE_BORES} = 0;
  $self->{DISCHARGE_BORES} = 0;
  $self->{DISCHARGE_FRAGS} = 0; $self->{DISCHARGE_DEATHS} = 0;
  $self->{TEAMKILLS} = 0;
 #  $self->{CTF_CAPTURES} = 0;
 #  $self->{CTF_FLAG_DEFENDS} = 0;
 #  $self->{CTF_CARRIER_DEFENDS} = 0;
  bless ($self, $class);
  return $self;
}

sub name
{
  my $self = shift;
  if (@_) { $self->{NAME} = shift }
  return $self->{NAME};
}

sub team
{
  my $self = shift;
  if (@_) {$self->{TEAM} = shift }
  return $self->{TEAM};
}

sub rocketDeaths
{
  my $self = shift;
  if (@_) { $self->{ROCKET_DEATHS} = shift }
  return $self->{ROCKET_DEATHS};
}

sub rocketFrags
{
  my $self = shift;
  if (@_) { $self->{ROCKET_FRAGS} = shift }
  return $self->{ROCKET_FRAGS};
}

sub shotgunDeaths
{
  my $self = shift;
  if (@_) { $self->{SHOTGUN_DEATHS} = shift }
  return $self->{SHOTGUN_DEATHS};
}

sub shotgunFrags
{
  my $self = shift;
  if (@_) { $self->{SHOTGUN_FRAGS} = shift }
  return $self->{SHOTGUN_FRAGS};
}

sub ssgDeaths
{
  my $self = shift;
  if (@_) { $self->{SSG_DEATHS} = shift }
  return $self->{SSG_DEATHS};
}

sub ssgFrags
{
  my $self = shift;
  if (@_) { $self->{SSG_FRAGS} = shift }
  return $self->{SSG_FRAGS};
}

sub nailgunDeaths
{
  my $self = shift;
  if (@_) { $self->{NAILGUN_DEATHS} = shift }
  return $self->{NAILGUN_DEATHS};
}

sub nailgunFrags
{
  my $self = shift;
  if (@_) { $self->{NAILGUN_FRAGS} = shift }
  return $self->{NAILGUN_FRAGS};
}

sub sngDeaths
{
  my $self = shift;
  if (@_) { $self->{SNG_DEATHS} = shift }
  return $self->{SNG_DEATHS};
}

sub sngFrags
{
  my $self = shift;
  if (@_) { $self->{SNG_FRAGS} = shift }
  return $self->{SNG_FRAGS};
}

sub grenadeDeaths
{
  my $self = shift;
  if (@_) { $self->{GRENADE_DEATHS} = shift }
  return $self->{GRENADE_DEATHS};
}

sub grenadeFrags
{
  my $self = shift;
  if (@_) { $self->{GRENADE_FRAGS} = shift }
  return $self->{GRENADE_FRAGS};
}

sub lightningDeaths
{
  my $self = shift;
  if (@_) { $self->{LIGHTNING_DEATHS} = shift }
  return $self->{LIGHTNING_DEATHS};
}

sub lightningFrags
{
  my $self = shift;
  if (@_) { $self->{LIGHTNING_FRAGS} = shift }
  return $self->{LIGHTNING_FRAGS};
}

sub axDeaths
{
  my $self = shift;
  if (@_) { $self->{AX_DEATHS} = shift }
  return $self->{AX_DEATHS};
}

sub axFrags
{
  my $self = shift;
  if (@_) { $self->{AX_FRAGS} = shift }
  return $self->{AX_FRAGS};
}

sub teleDeaths
{
  my $self = shift;
  if (@_) { $self->{TELEDEATHS} = shift }
  return $self->{TELEDEATHS};
}

sub teleFrags
{
  my $self = shift;
  if (@_) { $self->{TELEFRAGS} = shift }
  return $self->{TELEFRAGS};
}

sub lavaDeaths
{
  my $self = shift;
  if (@_) { $self->{LAVA_DEATHS} = shift }
  return $self->{LAVA_DEATHS};
}

sub slimeDeaths
{
  my $self = shift;
  if (@_) { $self->{SLIME_DEATHS} = shift }
  return $self->{SLIME_DEATHS};
}

sub waterDeaths
{
  my $self = shift;
  if (@_) { $self->{WATER_DEATHS} = shift }
  return $self->{WATER_DEATHS};
}

sub fallDeaths
{
  my $self = shift;
  if (@_) { $self->{FALL_DEATHS} = shift }
  return $self->{FALL_DEATHS};
}

sub squishBores
{
  my $self = shift;
  if (@_) { $self->{SQUISH_BORES} = shift }
  return $self->{SQUISH_BORES};
}

sub squishDeaths
{
  my $self = shift;
  if (@_) { $self->{SQUISH_DEATHS} = shift }
  return $self->{SQUISH_DEATHS};
}

sub squishFrags
{
  my $self = shift;
  if (@_) { $self->{SQUISH_FRAGS} = shift }
  return $self->{SQUISH_FRAGS};
}

sub miscBores
{
  my $self = shift;
  if (@_) { $self->{MISC_BORES} = shift }
  return $self->{MISC_BORES};
}

sub rocketBores
{
  my $self = shift;
  if (@_) { $self->{ROCKET_BORES} = shift }
  return $self->{ROCKET_BORES};
}

sub grenadeBores
{
  my $self = shift;
  if (@_) { $self->{GRENADE_BORES} = shift }
  return $self->{GRENADE_BORES};
}

sub teamKills
{
  my $self = shift;
  if (@_) { $self->{TEAMKILLS} = shift }
  return $self->{TEAMKILLS};
}

sub dischargeBores
{
  my $self = shift;
  if (@_) { $self->{DISCHARGE_BORES} = shift }
  return $self->{DISCHARGE_BORES};
}

sub dischargeFrags
{
  my $self = shift;
  if (@_) { $self->{DISCHARGE_FRAGS} = shift }
  return $self->{DISCHARGE_FRAGS};
}

sub dischargeDeaths
{
  my $self = shift;
  if (@_) { $self->{DISCHARGE_DEATHS} = shift }
  return $self->{DISCHARGE_DEATHS}; 
}

sub selfKills
{
  my $self = shift;
  return 
  (
    $self->rocketBores() + $self->lavaDeaths() +
    $self->slimeDeaths() + $self->waterDeaths() +
    $self->fallDeaths() + $self->squishDeaths() +
    $self->dischargeBores() + $self->grenadeBores() +
    $self->squishBores() + $self->miscBores()
  );
}

sub frags
{
  my $self = shift;
  return 
  (
    $self->axFrags() + $self->shotgunFrags() +
    $self->ssgFrags() + $self->nailgunFrags() +
    $self->sngFrags() + $self->grenadeFrags() +
    $self->rocketFrags() + $self->lightningFrags() +
    $self->dischargeFrags() + $self->squishFrags() +
    $self->teleFrags()
  );
}
 
sub deaths
{
  my $self = shift;
  return
  (
    $self->axDeaths() + $self->shotgunDeaths() +
    $self->ssgDeaths() + $self->nailgunDeaths() +
    $self->sngDeaths() + $self->grenadeFrags() +
    $self->rocketDeaths() + $self->lightningDeaths() + 
    $self->dischargeDeaths() + $self->squishDeaths() +
    $self->selfKills() + $self->teleDeaths()
   );
}

sub rank
{
  my $self = shift;
  return $self->frags - $self->deaths;
}

sub eff
{
  my $self = shift;
  if ($self->deaths + $self->frags < 1) { return 0; }
  return ($self->frags / ($self->deaths + $self->frags)) * 100;
}

sub points
{
  my $self = shift;
  return ($self->frags - $self->teamKills - $self->selfKills);
}

package Team;
sub new
{
  my $class = shift;
  my $self = {};
  $self->{NAME} = undef;
  $self->{PLAYERS} = [];
  bless ($self, $class);
  return $self;
}

sub name
{
  my $self = shift;
  if (@_) { $self->{NAME} = shift }
  return $self->{NAME};
}

sub players
{
  my $self = shift;
  if (@_) { @{ $self->{PLAYERS} } = @_ }
  return @{ $self->{PLAYERS} };
}

# might need some error checking here
sub removePlayer
{
  my $self = shift;
  if (@_)
  {
    my $playerToRemove = shift;
    my $id = 0;
    foreach $player ($self->players)
    {
      if ($player eq $playerToRemove) { $playerId = $id }
      $id++;
    }
    splice(@{$self->{PLAYERS}}, $playerId, 1); 
  }
}

sub addPlayer
{
  my $self = shift;
  if (@_) 
  {
    $playerToAdd = shift; 
    foreach $player ($self->players)
    {
      if ($playerToAdd eq $player) { return; }
    }
    push(@{ $self->{PLAYERS} }, $playerToAdd); 
  }
}

sub points
{
  my $self = shift;
  $points = 0;
  foreach $player ($self->players)
  {
    $player = main::findPlayer($player);
    $points += $player->points;
  }
  return $points;
}

package main;
$start = new Benchmark;
outputHeader();
foreach $mvd (@ARGV)
{
  $tempMvd = $mvd . ".tmp";
  $shell = `sed -f convertAscii.sed "$mvd" > "$tempMvd"`;
  @strings = `strings -2 "$tempMvd"`;

  foreach $string (@strings)
  {
    if (length($string) < 8)
    { 
      1;
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
    elsif ($string =~ /^(.*) chewed on (.*)'s boomstick/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->shotgunDeaths($fraggee->shotgunDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->shotgunFrags($fragger->shotgunFrags() + 1);
    }
    elsif ($string =~ /^(.*) was punctured by (.*)/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->sngDeaths($fraggee->sngDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->sngFrags($fragger->sngFrags() + 1);
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
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->rocketBores($fraggee->rocketBores() + 1);
    }
    elsif ($string =~ /^(.*) discovers blast radius/)
    {
print $string;
      $fraggee = findPlayer($1);
      $fraggee->rocketBores($fraggee->rocketBores() + 1);
    }
    elsif ($string =~ /^ tries to put the pin back in/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->grenadeBores($fraggee->grenadeBores() + 1);
    }
    elsif ($string =~ /^ discharges into the water/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
    }
    elsif ($string =~ /^ discharges into the slime/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
    }
    elsif ($string =~ /^ discharges into the lava/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
    }
    elsif ($string =~ /^(.*) electrocutes himself/)
    {
print $string;
      $fraggee = findPlayer($1);
      $fraggee->dischargeBores($fraggee->dischargeBores() + 1);
    }
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
	print $string;
      $fragger = findPlayer($1);
      $fragger->teamKills($fragger->teamKills() + 1);
    }
    elsif ($string =~ /^(.*) squishes (.*)/)
    {
      $fraggee = findPlayer($2);
      $fraggee->squishDeaths($fraggee->squishDeaths() + 1);
      $fragger = findPlayer($1);
      $fraggee->squishFrags($fragger->squishFrags() + 1);
    }
    elsif ($string =~ /^ visits the Volcano God/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->lavaDeaths($fraggee->lavaDeaths() + 1);
    }    
    elsif ($string =~ /^ burst into flames/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->lavaDeaths($fraggee->lavaDeaths() + 1);
    }
    elsif ($string =~ /^ turned into hot slag/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->lavaDeaths($fraggee->lavaDeaths() + 1);
    }
    elsif ($string =~ /^(.*) cratered/)
    {
	print $string;
      $fraggee = findPlayer($1);
      $fraggee->fallDeaths($fraggee->fallDeaths() + 1);
    }
    elsif ($string =~ /^ fell to his death/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->fallDeaths($fraggee->fallDeaths() + 1);
    }
    elsif ($string =~ /^(.*) sleeps with the fishes/)
    {
      $fraggee = findPlayer($1);
      $fraggee->waterDeaths($fraggee->waterDeaths() + 1);
    }
    elsif ($string =~ /^(.*) sucks it down/)
    {
	print $string;
      $fraggee = findPlayer($1);
      $fraggee->waterDeaths($fraggee->waterDeaths() + 1);
    }
    elsif ($string =~ /^(.*) gulped a load of slime/)
    {
print $string;
      $fraggee = findPlayer($1);
      $fraggee->slimeDeaths($fraggee->slimeDeaths() + 1);
    }
    elsif ($string =~ /^(.*) can't exist on slime alone/)
    {
print $oldString . $string;
      $fraggee = findPlayer($1);
      $fraggee->slimeDeaths($fraggee->slimeDeaths() + 1);
    }
    elsif ($string =~ /^ was spiked/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->miscBores($fraggee->miscBores() + 1);
    }
    elsif ($string =~ /^(.*) tried to leave/)
    {
print $string;
      $fraggee = findPlayer($1);
      $fraggee->miscBores($fraggee->miscBores() + 1);
    }
    elsif ($string =~ /^(.*) died/)
    {
print $string;
      $fraggee = findPlayer($1);
      $fraggee->miscBores($fraggee->miscBores() + 1);
    }
    elsif ($string =~ /^ suicides/)
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->miscBores($fraggee->miscBores() + 2);
    }
    elsif ($string =~ /^ was telefragged by his teammate/) 
    {
      chomp($oldString);
      $fraggee = findPlayer($oldString);
      $fraggee->miscBores($fraggee->miscBores() + 1);
    }
    elsif ($string =~ /^(.*) was telefragged by (.*)/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->teleDeaths($fraggee->teleDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->teleFrags($fragger->teleFrags() + 1);
    }
    elsif ($string =~ /satan/i)
    {
      print $string;
    }
# todo: satan, ctf, fall frags

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
        $player->team($team);
        $team = findTeam($team);
        $team->addPlayer($name);
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
    elsif ($string =~ /^\[(.*)\](.*):\[(.*)\](.*)$/)
    {
      $teamOneScore = $2;
      $teamTwoScore = $4;
     # print "$1\t$2\t$3\t$4\n";
      push(@Scores1, $2);
      push(@Scores2, $4);
    }
# once we reach this point the match is over and no good data remains
# breaking out of the loop not only provides a speed boost, but
# eliminates the disconnected player list from being added again
    elsif ($string =~ /. - disconnected players/) { last; }
    $oldString = $string;
  }
 # $shell = `rm "$tempMvd"`;
}

#outputHTML();
outputTeamHTML();
#outputForm();

$end = new Benchmark;
$diff = Benchmark::timediff($end, $start);
print "\n\nBenchmark: " . Benchmark::timestr($diff, 'all') . "\n"; 

sub outputTeamHTML
{
  print "Map:\t" . $map . "\n";
  foreach $team (@teams)
  {
    my @teamPlayers = $team->players;
    print $team->name . ":\t" . $team->points . "\n";
    foreach $player (@teamPlayers)
    {
      $player = findPlayer($player);
      print "\t\t" . $player->name . "\t" . $player->points . "\n";
    }  
  }
  foreach $player (@players)
  {
    if ($player->team eq undef)
    {
	print "unknown team:\t\t" .  $player->name . "\t" . $player->points . "\n";
    }
  }
}

sub outputHTML
{
  print "<HTML><BODY><TABLE>\n";
  print "\t<TR>\n" .
        "\t\t<TD>Name</TD>\n" .
        "\t\t<TD>Team</TD>\n" .
        "\t\t<TD>Frags</TD>\n" . 
        "\t\t<TD>Deaths</TD\n" .
        "\t\t<TD>SG</TD>\n" .
        "\t\t<TD>SSG</TD>\n" .
        "\t\t<TD>NG</TD>\n" .
        "\t\t<TD>SNG</TD>\n" .
        "\t\t<TD>GL</TD>\n" .
        "\t\t<TD>RL</TD>\n" .
        "\t\t<TD>LG</TD>\n" .
        "\t\t<TD>TK</TD>\n" .  
        "\t\t<TD>eff</TD>\n" .
        "\t\t<TD>lava</TD>\n" .
        "\t\t<TD>tele</TD>\n" .
        "\t\t<TD>self</TD>\n" .
        "\t\t<TD>bore rl</TD>\n" .
        "\t\t<TD>bore lg</TD>\n" .
        "\t\t<TD>bore m</TD>\n" .
        "\t</TR>\n";
  foreach $player (@players)
  {
     print "\t<TR>\n" . 
      "\t\t<TD>" . $player->name . "</TD>\n" .
      "\t\t<TD>" . $player->team . "</TD>\n" .
      "\t\t<TD>" . $player->frags . "</TD>\n" . 
      "\t\t<TD>" . $player->deaths . "</TD>\n" .
      "\t\t<TD>" . $player->shotgunFrags . "</TD>\n" .
      "\t\t<TD>" . $player->ssgFrags . "</TD>\n" .
      "\t\t<TD>" . $player->nailgunFrags . "</TD>\n" .
      "\t\t<TD>" . $player->sngFrags . "</TD>\n" .
      "\t\t<TD>" . $player->grenadeFrags . "</TD>\n" .
      "\t\t<TD>" . $player->rocketFrags . "</TD>\n" .
      "\t\t<TD>" . $player->lightningFrags . "</TD>\n" .
      "\t\t<TD>" . $player->teamKills . "</TD>\n" .
      "\t\t<TD>" . $player->eff . "</TD>\n" .
      "\t\t<TD>" . $player->lavaDeaths . "</TD>\n" .
      "\t\t<TD>" . $player->teleFrags . "</TD>\n" .
      "\t\t<TD>" . $player->selfKills . "</TD>\n" .
      "\t\t<TD>" . $player->rocketBores . "</TD>\n" . 
      "\t\t<TD>" . $player->dischargeBores . "</TD>\n" . 
      "\t\t<TD>" . $player->miscBores . "</TD>\n" .
      "\t</TR>\n";
  }
  print "</TABLE></BODY></HTML>";
}


# Searches player array for the name passed in
# Returns player object if found or new player object if not
sub findPlayer
{
  $playerName = shift;
  foreach $player (@players)
  {
    if ($player->name() eq $playerName) { return $player }    
  }
  $newPlayer = Player->new();
  $newPlayer->name($playerName);
  push(@players, $newPlayer);
  return $newPlayer;
}

sub findPlayerNoCreate
{
  $playerName = shift;
  foreach $player (@players)
  {
      if ($player->name() eq $playerName) { return $player }
  }
  return null;
}

sub findTeam
{
  $teamName = shift;
  foreach $team (@teams)
  {
    if ($team->name eq $teamName) { return $team }
  }
  $newTeam = Team->new();
  $newTeam->name($teamName);
  push(@teams, $newTeam);
  return $newTeam;
}

sub outputForm
{
  print "<HTML>\n";
  print "<HEAD></HEAD>\n";
  print "<BODY>\n";
  print "Match Report for $mvd\n";
  print "<TABLE border=0 cellpadding=2 cellspacing=0>\n";
  foreach $team (@teams)
  {
    print "\t<TR>\n";
    print "\t\t<TD>" . $team->name . "</TD>\n";
    print "\t</TR>\n";
    my @teamPlayers = $team->players;
    foreach $player (@teamPlayers)
    {
      $player = findPlayer($player);
      print "\t<TR>\n";
      print "\t\t<TD></TD>\n";
      print "\t\t<TD>" . $player->name . "</TD>\n";
      print "\t\t<TD>" . $player->points . "</TD>\n";
      print "\t</TR>\n";
    }    
    print "\t<TR>\n";
    print "\t\t<TD></TD><TD></TD><TD>" . $team->points . "</TD>\n";
    print "\t</TR>\n";
  }
#  print "<input type='hidden' name
  print "</TABLE>\n";
  print "</BODY>\n";
  print "</HTML>\n";
}

sub outputHeader
{
  print "<HTML>\n";
  print "<HEAD>\n";
  print "\t<TITLE>QuakeWorld.US MVD Analyzer</TITLE>\n";
  print "<BODY>\n";
}
