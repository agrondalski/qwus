#!/usr/bin/perl

1;

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

sub playerList
{
  my $self = shift;
  foreach $player ($self->players)
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

sub captures
{
  my $self = shift;
  my $caps = 0;
  foreach $player ($self->players)
  {
    $player = main::findPlayer($player);
    $caps += $player->captures;
  }
  return $caps;
}

