#!/usr/bin/perl

# todo:
# handle fun names (could be fairly tricky in some cases)
# nice output for easy database entry
# attempt to read and/or calculate final score
# misc bs

package Player;

sub new
{
    my $class = shift;
    my $self = {};
    $self->{NAME} = undef;
    $self->{ROCKETFRAGS} = 0;
    $self->{ROCKETDEATHS} = 0;
    $self->{SHOTGUNFRAGS} = 0;
    $self->{SHOTGUNDEATHS} = 0;
    $self->{SSGFRAGS} = 0;
    $self->{SSGDEATHS} = 0;
    $self->{NAILGUNFRAGS} = 0;
    $self->{NAILGUNDEATHS} = 0;
    $self->{SNGFRAGS} = 0;
    $self->{SNGDEATHS} = 0;
    $self->{GRENADEFRAGS} = 0;
    $self->{GRENADEDEATHS} = 0;
    $self->{LIGHTNINGFRAGS} = 0;
    $self->{LIGHTNINGDEATHS} = 0;
    $self->{TEAMKILLS} = 0;
    bless ($self, $class);
    return $self;
}

sub name
{
    my $self = shift;
    if (@_) { $self->{NAME} = shift }
    return $self->{NAME};
}

sub rocketDeaths
{
    my $self = shift;
    if (@_) { $self->{ROCKETDEATHS} = shift }
    return $self->{ROCKETDEATHS};
}

sub rocketFrags
{
    my $self = shift;
    if (@_) { $self->{ROCKETFRAGS} = shift }
    return $self->{ROCKETFRAGS};
}

sub teamKills
{
    my $self = shift;
    if (@_) { $self->{TEAMKILLS} = shift }
    return $self->{TEAMKILLS};
}


foreach $mvd (@ARGV)
{
  @strings = `strings $mvd`;

  foreach $string (@strings)
  {
    if ($string =~ /(.*) rides (.*)'s rocket/)
    {
      $fraggee = findPlayer($1);
      $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->rocketFrags($fragger->rocketFrags() + 1);
    }
    elsif ($string =~ /(.*) accepts (.*)'s shaft/)
    {

    }
    elsif ($string =~ /(.*) chewed on (.*)'s boomstick/) {}
    elsif ($string =~ /(.*) was punctured by (.*)/) {}
    elsif ($string =~ /(.*) was nailed by (.*)/) {}
    elsif ($string =~ /(.*) ate 2 loads of (.*)'s buckshot/) {}
    elsif ($string =~ /(.*) was brutalized by (.*)'s quad rocket/) {}
    elsif ($string =~ /(.*) was gibbed by (.*)'s grenade/) {}
    elsif ($string =~ /(.*) was gibbed by (.*)'s rocket/) {}
    elsif ($string =~ /(.*) was telefragged by his teammate/) {}
    elsif ($string =~ /(.*) was telefragged by (.*)/) {}
    elsif ($string =~ /(.*) mows down a teammate/) 
    {
      $fragger = findPlayer($1);
      $fragger->teamKills($fragger->teamKills() + 1);
    }
    elsif ($string =~ /(.*) checks his glasses/) 
    {
      $fragger = findPlayer($1);
      $fragger->teamKills($fragger->teamKills() + 1);
    }
  }
}

outputPlayerList();

sub outputPlayerList
{
  foreach $player (@players)
  {
     print 
      $player->name() . ":\t" . 
      $player->rocketFrags() . "\t" .
      $player->rocketDeaths() . "\t" .
      $player->teamKills() .
      "\n";
  }
}


// Searches player array for the name passed in
// Returns player object if found or new player object if not
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
