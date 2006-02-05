#!/usr/bin/perl

1;

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
  $self->{CTF_CAPTURES} = 0;
  $self->{CTF_CAPTURE_TIMES} = [];
  $self->{CTF_FLAG_DEFENDS} = 0;
  $self->{CTF_FLAG_DROPS} = 0;
  $self->{CTF_FLAG_PICKUPS} = 0;
  $self->{CTF_CARRIER_DEFENDS} = 0;
  $self->{CTF_CARRIER_DEFENDS_AGG} = 0;
  $self->{CTF_CARRIER_FRAGS_WITH_BONUS} = 0;
  $self->{CTF_CARRIER_FRAGS_NO_BONUS} = 0;
  $self->{CTF_FLAG_RETURNS} = 0;
  $self->{CTF_RETURN_ASSISTS} = 0;
  $self->{CTF_FRAG_ASSISTS} = 0;
  $self->{CTF_GRAPPLE_FRAGS} = 0;
  $self->{CTF_GRAPPLE_DEATHS} = 0;
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

sub removeScore
{
  my $self = shift;
  pop(@{$self->{SCORE_GRAPH}});
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

sub grappleDeaths
{
    my $self = shift;
    if (@_) { $self->{CTF_GRAPPLE_DEATHS} = shift; $self->resetFragStreak; }
    return $self->{CTF_GRAPPLE_DEATHS};
}

sub grappleFrags
{
    my $self = shift;
    if (@_) { $self->{CTF_GRAPPLE_FRAGS} = shift; $self->incrementFragStreak; }
    return $self->{CTF_GRAPPLE_FRAGS};
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

sub captures
{
  my $self = shift;
  if (@_) { $self->{CTF_CAPTURES} = shift; }
  return $self->{CTF_CAPTURES};
}

sub captureTimes
{
  my $self = shift;
  if (@_)
  {
    my $capTime = shift;
    push(@{ $self->{CTF_CAPTURE_TIMES} }, $capTime);
  }
  else
  {
    my $capTimes = "";
    foreach $cap (@{$self->{CTF_CAPTURE_TIMES}})
    {
      $cap .= " ";
      $capTimes .= $cap;  
    }
    chop($capTimes);
    if ($capTimes == "") { return -1; }
    return $capTimes;
  }
}

sub flagPickups
{
  my $self = shift;
  if (@_) { $self->{CTF_FLAG_PICKUPS} = shift; }
  return $self->{CTF_FLAG_PICKUPS};
}

sub flagDefends
{
  my $self = shift;
  if (@_) { $self->{CTF_FLAG_DEFENDS} = shift; }
  return $self->{CTF_FLAG_DEFENDS};
}

sub carrierDefends
{
  my $self = shift;
  if (@_) { $self->{CTF_CARRIER_DEFENDS} = shift; }
  return $self->{CTF_CARRIER_DEFENDS};
}

sub carrierDefendsAgg
{
  my $self = shift;
  if (@_) { $self->{CTF_CARRIER_DEFENDS_AGG} = shift; }
  return $self->{CTF_CARRIER_DEFENDS_AGG};
}

sub carrierFragsBonus
{
  my $self = shift;
  if (@_) { $self->{CTF_CARRIER_FRAGS_WITH_BONUS} = shift; }
  return $self->{CTF_CARRIER_FRAGS_WITH_BONUS};
}

sub carrierFragsNoBonus
{
  my $self = shift;
  if (@_) { $self->{CTF_CARRIER_FRAGS_NO_BONUS} = shift; }
  return $self->{CTF_CARRIER_FRAGS_NO_BONUS};
}

sub flagReturns
{
  my $self = shift;
  if (@_) { $self->{CTF_FLAG_RETURNS} = shift; }
  return $self->{CTF_FLAG_RETURNS};
}

sub flagDrops
{
  my $self = shift;
  if (@_) { $self->{CTF_FLAG_DROPS} = shift; }
  return $self->{CTF_FLAG_DROPS};
}

sub fragAssists
{
  my $self = shift;
  if (@_) { $self->{CTF_FRAG_ASSISTS} = shift; }
  return $self->{CTF_FRAG_ASSISTS};
}

sub returnAssists
{
  my $self = shift;
  if (@_) { $self->{CTF_RETURN_ASSISTS} = shift; }
  return $self->{CTF_RETURN_ASSISTS};
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
    $self->teleFrags() + $self->grappleFrags()
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
    $self->sngDeaths() + $self->grenadeDeaths() +
    $self->rocketDeaths() + $self->lightningDeaths() + 
    $self->dischargeDeaths() + $self->squishDeaths() +
    $self->selfKills() + $self->teleDeaths() + $self->grappleDeaths()
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
  my $team = $self->team;
  if ($team == null) { return 0; }
  return ($self->frags - $self->teamKills - $self->selfKills + 
   (2 * $self->fragAssists) + $self->returnAssists + $self->carrierDefends +
   $self->flagReturns + $self->flagDefends + (15 * $self->captures) + 
   (2 * $self->carrierFragsBonus) + (2 * $self->carrierDefendsAgg) +
   (10 * ($team->captures - $self->captures)));
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
  print "Ax Frags\\\\";
  print "Ax Deaths\\\\";
  print "Shotgun Frags\\\\";
  print "Shotgun Deaths\\\\";
  print "SSG Frags\\\\";
  print "SSG Deaths\\\\";
  print "Nailgun Frags\\\\";
  print "Nailgun Deaths\\\\";
  print "SNG Frags\\\\";
  print "SNG Deaths\\\\";
  print "Grenade Frags\\\\";
  print "Grenade Deaths\\\\";
  print "Rocket Frags\\\\";
  print "Rocket Deaths\\\\";
  print "LG Frags\\\\";
  print "LG Deaths\\\\";
  print "Tele Frags\\\\";
  print "Tele Deaths\\\\";
  print "Discharge Frags\\\\";
  print "Discharge Deaths\\\\";
  print "Discharge Bores\\\\";
  print "Squish Frags\\\\";
  print "Squish Deaths\\\\";
  print "Squish Bores\\\\";
  print "Lava Bores\\\\";
  print "Slime Bores\\\\";
  print "Water Bores\\\\";
  print "Fall Bores\\\\"; 
  print "Misc Bores\\\\";
  print "Grenade Bores\\\\";
  print "Rocket Bores\\\\";
  print "Self Kills\\\\";
  print "Team Kills\\\\"; 
  print "Total Frags\\\\";
  print "Total Deaths\\\\";
  print "Rank\\\\";
  print "Efficiency\\\\";
  print "Score\\\\";
  print "Frag Streak\\\\";
  print "Captures\\\\";
  print "Capture Times\\\\";
  print "Flag Pickups\\\\";
  print "Flag Defends\\\\";
  print "Carrier Defends\\\\";
  print "Carrier Frags\\\\";
  print "Flag Returns\\\\";
  print "Flag Drops\\\\";
  print "Frag Assists\\\\";
  print "Return Assists\\\\";
  print "Grapple Frags\\\\";
  print "Grapple Deaths\\\\";
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
  print $self->captures . "\\\\";
  print $self->captureTimes . "\\\\";
  print $self->flagPickups . "\\\\";
  print $self->flagDefends . "\\\\";
  print $self->carrierDefends + $self->carrierDefendsAgg . "\\\\";
  print $self->carrierFragsBonus + $self->carrierFragsNoBonus . "\\\\";
  print $self->flagReturns . "\\\\";
  print $self->flagDrops . "\\\\";
  print $self->fragAssists . "\\\\";
  print $self->returnAssists . "\\\\";
  print $self->grappleFrags . "\\\\";
  print $self->grappleDeaths . "\\\\";
}
