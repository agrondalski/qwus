#!/usr/bin/perl -w
use strict;

1;

package Team;
sub new
{
  my $class = shift;
  my $self = {};
  $self->{NAME} = undef;
  $self->{APPROVED} = 0;
  $self->{PLAYERS} = [];
  $self->{COLOR} = 0;
  $self->{MINUTES_PLAYED} = 0;
  $self->{MINUTES_WITH_LEAD} = 0;
  $self->{SCORE_GRAPH} = [];
  $self->{QUADS} = 0;
  $self->{PENTS} = 0;
  $self->{RINGS} = 0;
  $self->{GREEN_ARMORS} = 0;
  $self->{YELLOW_ARMORS} = 0;
  $self->{RED_ARMORS} = 0;
  $self->{DAMAGE_TAKEN} = 0;
  $self->{DAMAGE_GIVEN} = 0;
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

sub quads
{
  my $self = shift;
  if (@_) { $self->{QUADS} = shift }
  return $self->{QUADS};
}

sub pents
{
  my $self = shift;
  if (@_) { $self->{PENTS} = shift }
  return $self->{PENTS};
}

sub rings
{
  my $self = shift;
  if (@_) { $self->{RINGS} = shift }
  return $self->{RINGS};
}

sub greenArmors
{
  my $self = shift;
  if (@_) { $self->{GREEN_ARMORS} = shift }
  return $self->{GREEN_ARMORS};
}

sub yellowArmors
{
  my $self = shift;
  if (@_) { $self->{YELLOW_ARMORS} = shift }
  return $self->{YELLOW_ARMORS};
}

sub redArmors
{
  my $self = shift;
  if (@_) { $self->{RED_ARMORS} = shift }
  return $self->{RED_ARMORS};
}

sub damageTaken
{
  my $self = shift;
  if (@_) { $self->{DAMAGE_TAKEN} = shift }
  return $self->{DAMAGE_TAKEN};
}

sub damageGiven
{
  my $self = shift;
  if (@_) { $self->{DAMAGE_GIVEN} = shift }
  return $self->{DAMAGE_GIVEN};
}

sub pushScore
{
  my $self = shift;
  if (@_)
  {
    push(@{ $self->{SCORE_GRAPH} }, shift);
  }
}

sub popScore
{
  my $self = shift;
  return pop(@{ $self->{SCORE_GRAPH} });
}

sub getScoreArray
{
  my $self = shift;
  return @{$self->{SCORE_GRAPH}};
}

# might need some error checking here
sub removePlayer
{
  my $self = shift;
  if (@_)
  {
    my $playerToRemove = shift;
    my $id = 0;
    my $playerId = undef;
    foreach my $player ($self->players)
    {
      if ($player eq $playerToRemove) { $playerId = $id }
      $id++;
    }
    splice(@{$self->{PLAYERS}}, $playerId, 1); 
  }
}

sub playerList
{
  my $self = shift;
  foreach my $player ($self->players)
  {
    print "\t" . $player . "\n";
  }
}

sub addPlayer
{
  my $self = shift;
  if (@_) 
  {
    my $playerToAdd = shift; 
    foreach my $player ($self->players)
    {
      if ($playerToAdd eq $player) { return; }
    }
    push(@{ $self->{PLAYERS} }, $playerToAdd); 
  }
}

sub points
{
  my $self = shift;
  my $players = shift;
  #int "$players\n";die;
  my $points = 0;
  foreach my $player ($self->players)
  {
   
    $player = $players->findPlayer($player);
    $points += $player->points($players);
  }
  return $points;
}

sub captures
{
  my $self = shift;
  my $players = shift;
  
  my $caps = 0;
  foreach my $player ($self->players)
  {
#    print "$player\n";die;
    $player = $players->findPlayer($player);
    $caps += $player->captures;
  }
  return $caps;
}

sub outputStatsHeader
{
  print "\t<input type='hidden' name='teamStats' value='";
  print "Name\\\\";
  print "Matched\\\\";
  print "Score\\\\";
  print "MinutesPlayed\\\\";
  print "MinutesWithLead\\\\";
  print "Quads\\\\";
  print "Pents\\\\";
  print "Rings\\\\";
  print "Green Armors\\\\";
  print "Yellow Armors\\\\";
  print "Red Armors\\\\";
  print "Damage Taken\\\\";
  print "Damage Given";
  print "'>\n";
}

sub outputStats
{
  my $self = shift;
  my $self2 = shift;
  print $self->name . "\\\\";
  print $self->approved . "\\\\";
  print $self->points($self2) . "\\\\";
  print $self->minutesPlayed . "\\\\";
  print $self->minutesWithLead . "\\\\";
  print $self->quads . "\\\\";
  print $self->pents . "\\\\";
  print $self->rings . "\\\\";
  print $self->greenArmors . "\\\\";
  print $self->yellowArmors . "\\\\";
  print $self->redArmors . "\\\\";
  print $self->damageTaken . "\\\\";
  print $self->damageGiven;
  print "'>\n";
}
