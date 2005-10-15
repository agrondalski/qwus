#!/usr/bin/perl

# todo:
# ctf msgs
# team stats (end of mvd)
# player minutes played ( * left the game)
# player line graph should check for not null team

use CGI qw/:standard/;
use GD::Graph::lines;
use GD::Graph::pie;
use GD::Graph::colour;

package Player;
sub new
{
  my $class = shift;
  my $self = {};
  $self->{NAME} = undef;
  $self->{TEAM} = undef;
  $self->{APPROVED} = 0;
  $self->{SCORE_GRAPH} = [];
  $self->{MINUTES_PLAYED} = 0;
  $self->{CURRENTLY_PLAYING} = 1;
  $self->{TOP_COLOR} = undef;
  $self->{BOTTOM_COLOR} = undef;
  $self->{ROCKET_FRAGS} = 0;    $self->{ROCKET_DEATHS} = 0;
  $self->{SHOTGUN_FRAGS} = 0;   $self->{SHOTGUN_DEATHS} = 0;
  $self->{SSG_FRAGS} = 0;       $self->{SSG_DEATHS} = 0;
  $self->{NAILGUN_FRAGS} = 0;   $self->{NAILGUN_DEATHS} = 0;
  $self->{SNG_FRAGS} = 0;       $self->{SNG_DEATHS} = 0;
  $self->{GRENADE_FRAGS} = 0;   $self->{GRENADE_DEATHS} = 0;
  $self->{LIGHTNING_FRAGS} = 0; $self->{LIGHTNING_DEATHS} = 0;
  $self->{AX_FRAGS} = 0;        $self->{AX_DEATHS} = 0;
  $self->{TELEFRAGS} = 0;       $self->{TELEDEATHS} = 0;
  $self->{LAVA_BORES} = 0;
  $self->{SLIME_BORES} = 0;
  $self->{WATER_BORES} = 0;
  $self->{FALL_BORES} = 0;
  $self->{SQUISH_FRAGS} = 0;    $self->{SQUISH_DEATHS} = 0;
  $self->{SQUISH_BORES} = 0;
  $self->{MISC_BORES} = 0;
  $self->{ROCKET_BORES} = 0;
  $self->{GRENADE_BORES} = 0;
  $self->{DISCHARGE_BORES} = 0;
  $self->{DISCHARGE_FRAGS} = 0; $self->{DISCHARGE_DEATHS} = 0;
  $self->{TEAMKILLS} = 0;
  $self->{MAX_FRAG_STREAK} = 0;
  $self->{CURRENT_FRAG_STREAK} = 0;
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

sub approved
{
  my $self = shift;
  if (@_) {$self->{APPROVED} = shift }
  return $self->{APPROVED};
}

sub minutesPlayed
{
  my $self = shift;
  if (@_ && $self->{CURRENTLY_PLAYING})
  {
    $self->{MINUTES_PLAYED} = shift;
  }
  return $self->{MINUTES_PLAYED};
}

sub playing
{
  my $self = shift;
  if (@_) { $self->{CURRENTLY_PLAYING} = shift }
  return $self->{CURRENTLY_PLAYING};
}

sub topColor
{
  my $self = shift;
  if (@_) {$self->{TOP_COLOR} = shift }
  return $self->{TOP_COLOR};
}

sub bottomColor
{
  my $self = shift;
  if (@_) {$self->{BOTTOM_COLOR} = shift }
  return $self->{BOTTOM_COLOR};
}

sub scoreArray
{
  my $self = shift;
  if (@_) { @{ $self->{SCORE_GRAPH} } = @_ }
  return @{ $self->{SCORE_GRAPH} };
}

sub padScoreArray
{
  my $self = shift;
  unshift(@{$self->{SCORE_GRAPH}}, 0);
}

sub addScore
{
  my $self = shift;
  if (@_)
  {
    my $scoreToAdd = shift;
    push(@{ $self->{SCORE_GRAPH} }, $scoreToAdd);
  }
}

sub rocketDeaths
{
  my $self = shift;
  if (@_) { $self->{ROCKET_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{ROCKET_DEATHS};
}

sub rocketFrags
{
  my $self = shift;
  if (@_) { $self->{ROCKET_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{ROCKET_FRAGS};
}

sub shotgunDeaths
{
  my $self = shift;
  if (@_) { $self->{SHOTGUN_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{SHOTGUN_DEATHS};
}

sub shotgunFrags
{
  my $self = shift;
  if (@_) { $self->{SHOTGUN_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{SHOTGUN_FRAGS};
}

sub ssgDeaths
{
  my $self = shift;
  if (@_) { $self->{SSG_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{SSG_DEATHS};
}

sub ssgFrags
{
  my $self = shift;
  if (@_) { $self->{SSG_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{SSG_FRAGS};
}

sub nailgunDeaths
{
  my $self = shift;
  if (@_) { $self->{NAILGUN_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{NAILGUN_DEATHS};
}

sub nailgunFrags
{
  my $self = shift;
  if (@_) { $self->{NAILGUN_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{NAILGUN_FRAGS};
}

sub sngDeaths
{
  my $self = shift;
  if (@_) { $self->{SNG_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{SNG_DEATHS};
}

sub sngFrags
{
  my $self = shift;
  if (@_) { $self->{SNG_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{SNG_FRAGS};
}

sub grenadeDeaths
{
  my $self = shift;
  if (@_) { $self->{GRENADE_DEATHS} = shift; $self->resetFragStreak }
  return $self->{GRENADE_DEATHS};
}

sub grenadeFrags
{
  my $self = shift;
  if (@_) { $self->{GRENADE_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{GRENADE_FRAGS};
}

sub lightningDeaths
{
  my $self = shift;
  if (@_) { $self->{LIGHTNING_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{LIGHTNING_DEATHS};
}

sub lightningFrags
{
  my $self = shift;
  if (@_) { $self->{LIGHTNING_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{LIGHTNING_FRAGS};
}

sub axDeaths
{
  my $self = shift;
  if (@_) { $self->{AX_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{AX_DEATHS};
}

sub axFrags
{
  my $self = shift;
  if (@_) { $self->{AX_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{AX_FRAGS};
}

sub teleDeaths
{
  my $self = shift;
  if (@_) { $self->{TELEDEATHS} = shift; $self->resetFragStreak; }
  return $self->{TELEDEATHS};
}

sub teleFrags
{
  my $self = shift;
  if (@_) { $self->{TELEFRAGS} = shift; $self->incrementFragStreak; }
  return $self->{TELEFRAGS};
}

sub lavaBores
{
  my $self = shift;
  if (@_) { $self->{LAVA_BORES} = shift; $self->resetFragStreak; }
  return $self->{LAVA_BORES};
}

sub slimeBores
{
  my $self = shift;
  if (@_) { $self->{SLIME_BORES} = shift; $self->resetFragStreak; }
  return $self->{SLIME_BORES};
}

sub waterBores
{
  my $self = shift;
  if (@_) { $self->{WATER_BORES} = shift; $self->resetFragStreak; }
  return $self->{WATER_BORES};
}

sub fallBores
{
  my $self = shift;
  if (@_) { $self->{FALL_BORES} = shift; $self->resetFragStreak; }
  return $self->{FALL_BORES};
}

sub squishBores
{
  my $self = shift;
  if (@_) { $self->{SQUISH_BORES} = shift; $self->resetFragStreak; }
  return $self->{SQUISH_BORES};
}

sub squishDeaths
{
  my $self = shift;
  if (@_) { $self->{SQUISH_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{SQUISH_DEATHS};
}

sub squishFrags
{
  my $self = shift;
  if (@_) { $self->{SQUISH_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{SQUISH_FRAGS};
}

sub miscBores
{
  my $self = shift;
  if (@_) { $self->{MISC_BORES} = shift; $self->resetFragStreak; }
  return $self->{MISC_BORES};
}

sub rocketBores
{
  my $self = shift;
  if (@_) { $self->{ROCKET_BORES} = shift; $self->resetFragStreak; }
  return $self->{ROCKET_BORES};
}

sub grenadeBores
{
  my $self = shift;
  if (@_) { $self->{GRENADE_BORES} = shift; $self->resetFragStreak; }
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
  if (@_) { $self->{DISCHARGE_BORES} = shift; $self->resetFragStreak; }
  return $self->{DISCHARGE_BORES};
}

sub dischargeFrags
{
  my $self = shift;
  if (@_) { $self->{DISCHARGE_FRAGS} = shift; $self->incrementFragStreak; }
  return $self->{DISCHARGE_FRAGS};
}

sub dischargeDeaths
{
  my $self = shift;
  if (@_) { $self->{DISCHARGE_DEATHS} = shift; $self->resetFragStreak; }
  return $self->{DISCHARGE_DEATHS}; 
}

sub selfKills
{
  my $self = shift;
  return 
  (
    $self->rocketBores() + $self->lavaBores() +
    $self->slimeBores() + $self->waterBores() +
    $self->fallBores() +  
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

sub graphedFrags
{
  my $self = shift;
  return
  ( 
    $self->shotgunFrags() + $self->ssgFrags() +
    $self->nailgunFrags() + $self->sngFrags() +
    $self->rocketFrags()  + $self->grenadeFrags() +
    $self->lightningFrags()
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
  return $self->frags - $self->deaths; # - teamkills??
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

sub incrementFragStreak
{
  my $self = shift;
  $self->{CURRENT_FRAG_STREAK} = $self->{CURRENT_FRAG_STREAK} + 1;
  if ($self->{CURRENT_FRAG_STREAK} > $self->{MAX_FRAG_STREAK})
  {
    $self->{MAX_FRAG_STREAK} = $self->{CURRENT_FRAG_STREAK};
  }  
}

sub resetFragStreak
{
  my $self = shift;
  $self->{CURRENT_FRAG_STREAK} = 0;
}

sub fragStreak
{
  my $self = shift;
  return $self->{MAX_FRAG_STREAK};
}

sub outputStatsHeader
{
  print "\t<input type='hidden' name='PlayerStats' value='";
  print "Name\\\\";
  print "Matched\\\\";
  print "AxFrags\\\\";
  print "AxDeaths\\\\";
  print "ShotgunFrags\\\\";
  print "ShotgunDeaths\\\\";
  print "SuperShotgunFrags\\\\";
  print "SuperShotgunDeaths\\\\";
  print "NailgunFrags\\\\";
  print "NailgunDeaths\\\\";
  print "SuperNailgunFrags\\\\";
  print "SuperNailgunDeaths\\\\";
  print "GrenadeLauncherFrags\\\\";
  print "GrenadeLauncherDeaths\\\\";
  print "RocketLauncherFrags\\\\";
  print "RocketLauncherDeaths\\\\";
  print "LightningGunFrags\\\\";
  print "LightningGunDeaths\\\\";
  print "TeleFrags\\\\";
  print "TeleDeaths\\\\";
  print "DischargeFrags\\\\";
  print "DischargeDeaths\\\\";
  print "DischargeBores\\\\";
  print "SquishFrags\\\\";
  print "SquishDeaths\\\\";
  print "SquishBores\\\\";
  print "LavaBores\\\\";
  print "SlimeBores\\\\";
  print "WaterBores\\\\";
  print "FallBores\\\\"; 
  print "MiscBores\\\\";
  print "GrenadeBores\\\\";
  print "RocketBores\\\\";
  print "SelfKills\\\\";
  print "TeamKills\\\\"; 
  print "TotalFrags\\\\";
  print "TotalDeaths\\\\";
  print "Rank\\\\";
  print "Efficiency\\\\";
  print "Score\\\\";
  print "FragStreak\\\\";
  print "PieChart";
  print "'>\n";
}

sub outputStats
{
  my $self = shift;
  print $self->name . "\\\\";
  print $self->approved . "\\\\";
  print $self->axFrags . "\\\\";
  print $self->axDeaths . "\\\\";
  print $self->shotgunFrags . "\\\\";
  print $self->shotgunDeaths . "\\\\";
  print $self->ssgFrags . "\\\\";
  print $self->ssgDeaths . "\\\\";
  print $self->nailgunFrags . "\\\\";
  print $self->nailgunDeaths . "\\\\";
  print $self->sngFrags . "\\\\";
  print $self->sngDeaths . "\\\\";
  print $self->grenadeFrags . "\\\\";
  print $self->grenadeDeaths . "\\\\";
  print $self->rocketFrags . "\\\\";
  print $self->rocketDeaths . "\\\\";
  print $self->lightningFrags . "\\\\";
  print $self->lightningDeaths . "\\\\";
  print $self->teleFrags . "\\\\";
  print $self->teleDeaths . "\\\\";
  print $self->dischargeFrags . "\\\\";
  print $self->dischargeDeaths . "\\\\";
  print $self->dischargeBores . "\\\\";
  print $self->squishFrags . "\\\\";
  print $self->squishDeaths . "\\\\";
  print $self->squishBores . "\\\\";
  print $self->lavaBores . "\\\\";
  print $self->slimeBores . "\\\\";
  print $self->waterBores . "\\\\";
  print $self->fallBores . "\\\\";
  print $self->miscBores . "\\\\";
  print $self->grenadeBores . "\\\\";
  print $self->rocketBores . "\\\\";
  print $self->selfKills . "\\\\";
  print $self->teamKills . "\\\\";
  print $self->frags . "\\\\";
  print $self->deaths . "\\\\";
  print $self->rank . "\\\\";
  print $self->eff . "\\\\";
  print $self->points . "\\\\";
  print $self->fragStreak . "\\\\";
}

package Team;
sub new
{
  my $class = shift;
  my $self = {};
  $self->{NAME} = undef;
  $self->{APPROVED} = 0;
  $self->{PLAYERS} = [];
  $self->{COLOR} = undef;
  $self->{MINUTES_PLAYED} = 0;
  $self->{MINUTES_WITH_LEAD} = 0;
  bless ($self, $class);
  return $self;
}

sub name
{
  my $self = shift;
  if (@_) { $self->{NAME} = shift }
  return $self->{NAME};
}

sub approved
{
  my $self = shift;
  if (@_) { $self->{APPROVED} = shift }
  return $self->{APPROVED};
}

sub color
{
  my $self = shift;
  if (@_) { $self->{COLOR} = shift }
  return $self->{COLOR};
}

sub players
{
  my $self = shift;
  if (@_) { @{ $self->{PLAYERS} } = @_ }
  return @{ $self->{PLAYERS} };
}

sub minutesPlayed
{
  my $self = shift;
  if (@_) { $self->{MINUTES_PLAYED} = shift }
  return $self->{MINUTES_PLAYED};
}

sub minutesWithLead
{
  my $self = shift;
  if (@_) { $self->{MINUTES_WITH_LEAD} = shift }
  return $self->{MINUTES_WITH_LEAD};
}

# might need some error checking here
sub removePlayer
{
  my $self = shift;
  if (@_)
  {
    my $playerToRemove = shift;
    my $id = 0;
    my $playerId = null;
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
    my $playerToAdd = shift; 
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
  my $points = 0;
  foreach $player ($self->players)
  {
    $player = main::findPlayer($player);
    $points += $player->points;
  }
  return $points;
}

package main;
$teamOneScore = 0;
$teamTwoScore = 0;
$tempDir = "/tmp/";

my $cgi = new CGI;
my $tourney_id = $cgi->param('tourney_id');
my $division_id = $cgi->param('division_id');
my $match_id = $cgi->param('match_id');
my $winning_team_id = $cgi->param('winning_team_id');
my $winningTeamAbbr = $cgi->param('winning_team_abbr');
my $approved = $cgi->param('approved');
my $mvd = $cgi->param('filename');
my $teamOneAbbr = $cgi->param('team1');
my $teamTwoAbbr = $cgi->param('team2');
my $teamOnePlayers = $cgi->param('team1players');
my $teamTwoPlayers = $cgi->param('team2players');

print "Content-type: text/html\n\n";
my $referer = $ENV{"HTTP_REFERER"};
if ($referer != /reportMatch/) { exit; }

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
my @strings = `strings -2 "$tempMvd"`;
my $fraggee = undef;
my $fragger = undef;
foreach $string (@strings)
{
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
    chomp($oldString);
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
# doesnt effect score?
#    $fragger = findPlayer($1);
#    $fragger->teamKills($fragger->teamKills() + 1);
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
    chomp($oldString);
    $fraggee = findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }    
  elsif ($string =~ /^ burst into flames/)
  {
    chomp($oldString);
    $fraggee = findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }
  elsif ($string =~ /^ turned into hot slag/)
  {
    chomp($oldString);
    $fraggee = findPlayer($oldString);
    $fraggee->lavaBores($fraggee->lavaBores() + 1);
  }
  elsif ($string =~ /^(.*) cratered/)
  {
    print $string;
    $fraggee = findPlayer($1);
    $fraggee->fallBores($fraggee->fallBores() + 1);
  }
  elsif ($string =~ /^ fell to his death/)
  {
    chomp($oldString);
    $fraggee = findPlayer($oldString);
    $fraggee->fallBores($fraggee->fallBores() + 1);
  }
  elsif ($string =~ /^(.*) sleeps with the fishes/)
  {
    $fraggee = findPlayer($1);
    $fraggee->waterBores($fraggee->waterBores() + 1);
  }
  elsif ($string =~ /^(.*) sucks it down/)
  {
    print $string;
    $fraggee = findPlayer($1);
    $fraggee->waterBores($fraggee->waterBores() + 1);
  }
  elsif ($string =~ /^(.*) gulped a load of slime/)
  {
    chomp($oldString);
    $fraggee = findPlayer($oldString);
    $fraggee->slimeBores($fraggee->slimeBores() + 1);
  }
  elsif ($string =~ /^(.*) can't exist on slime alone/)
  {
    print $oldString . $string;
    $fraggee = findPlayer($1);
    $fraggee->slimeBores($fraggee->slimeBores() + 1);
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
    # this seems to have no effect on score in ktpro ??
    #chomp($oldString);
    #$fraggee = findPlayer($oldString);
    #$fraggee->miscBores($fraggee->miscBores() + 1);
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
 #       $team->color($bottomColor);
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
  elsif ($string =~ /^(.*) min left$/)
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
  elsif ($string =~ /^\[(.*)\](.*):\[(.*)\](.*)$/)
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
  $oldString = $string;
}
push(@graphTime, 0);
push(@graphTeamOneScore, $teamOneScore);
push(@graphTeamTwoScore, $teamTwoScore);
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
calculateTeamColors();
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

  #print "$teamOneFound\t$teamTwoFound\n";
  
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

   print "\t<input type='hidden' name='playerFields' value='42'>\n";
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
  my @data = (\@graphTime);
  foreach $player (@players)
  { 
 #   if ($player->team != undef)
    {
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

sub outputTeamScoreGraph
{
  my $x = 400; my $y = 300;
  if (@_) { $x = shift; $y = shift; }
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
    $graph->set(title => "Frags by " . $player->name . " (" . $player->frags 
                         . ")",
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

sub complementColor
{
  my $color = shift;
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
