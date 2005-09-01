#!/usr/bin/perl

# todo:
# handle fun names (could be fairly tricky in some cases)
# nice output for easy database entry
# attempt to read and/or calculate final score
# ax, telefrags, slime, lava, drown
# attempt to create 2 teams and place players in them
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
    $self->{AXDEATHS} = 0;
    $self->{AXFRAGS} = 0;
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

sub shotgunDeaths
{
    my $self = shift;
    if (@_) { $self->{SHOTGUNDEATHS} = shift }
    return $self->{SHOTGUNDEATHS};
}

sub shotgunFrags
{
    my $self = shift;
    if (@_) { $self->{SHOTGUNFRAGS} = shift }
    return $self->{SHOTGUNFRAGS};
}

sub ssgDeaths
{
    my $self = shift;
    if (@_) { $self->{SSGDEATHS} = shift }
    return $self->{SSGDEATHS};
}

sub ssgFrags
{
    my $self = shift;
    if (@_) { $self->{SSGFRAGS} = shift }
    return $self->{SSGFRAGS};
}

sub nailgunDeaths
{
    my $self = shift;
    if (@_) { $self->{NAILGUNDEATHS} = shift }
    return $self->{NAILGUNDEATHS};
}

sub nailgunFrags
{
    my $self = shift;
    if (@_) { $self->{NAILGUNFRAGS} = shift }
    return $self->{NAILGUNFRAGS};
}

sub sngDeaths
{
    my $self = shift;
    if (@_) { $self->{SNGDEATHS} = shift }
    return $self->{SNGDEATHS};
}

sub sngFrags
{
    my $self = shift;
    if (@_) { $self->{SNGFRAGS} = shift }
    return $self->{SNGFRAGS};
}

sub grenadeDeaths
{
    my $self = shift;
    if (@_) { $self->{GRENADeDEATHS} = shift }
    return $self->{GRENADEDEATHS};
}

sub grenadeFrags
{
    my $self = shift;
    if (@_) { $self->{GRENADEFRAGS} = shift }
    return $self->{GRENADEFRAGS};
}

sub lightningDeaths
{
    my $self = shift;
    if (@_) { $self->{LIGHTNINGDEATHS} = shift }
    return $self->{LIGHTNINGDEATHS};
}

sub lightningFrags
{
    my $self = shift;
    if (@_) { $self->{LIGHTNINGFRAGS} = shift }
    return $self->{LIGHTNINGFRAGS};
}

sub axDeaths
{
    my $self = shift;
    if (@_) { $self->{AXDEATHS} = shift }
    return $self->{AXDEATHS};
}

sub axFrags
{
    my $self = shift;
    if (@_) { $self->{AXFRAGS} = shift }
    return $self->{AXFRAGS};
}

sub teamKills
{
    my $self = shift;
    if (@_) { $self->{TEAMKILLS} = shift }
    return $self->{TEAMKILLS};
}

sub frags
{
    my $self = shift;
    return 
    (
      $self->axFrags() + $self->shotgunFrags() +
      $self->ssgFrags() + $self->nailgunFrags() +
      $self->sngFrags() + $self->grenadeFrags() +
      $self->rocketFrags() + $self->lightningFrags()
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
      $self->rocketDeaths() + $self->lightningDeaths()
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
    return ($self->frags /  ($self->deaths + $self->frags)) * 100;
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
      $fraggee = findPlayer($1);
      $fraggee->lightningDeaths($fraggee->lightningDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->lightningFrags($fragger->lightningFrags() + 1);
    }
    elsif ($string =~ /(.*) chewed on (.*)'s boomstick/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->shotgunDeaths($fraggee->shotgunDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->shotgunFrags($fragger->shotgunFrags() + 1);
    }
    elsif ($string =~ /(.*) was punctured by (.*)/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->sngDeaths($fraggee->sngDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->sngFrags($fragger->sngFrags() + 1);
    }
    elsif ($string =~ /(.*) was nailed by (.*)/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->nailgunDeaths($fraggee->nailgunDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->nailgunFrags($fragger->nailgunFrags() + 1);
    }
    elsif ($string =~ /(.*) ate 2 loads of (.*)'s buckshot/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->ssgDeaths($fraggee->ssgDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->ssgFrags($fragger->ssgFrags() + 1);
    }
    elsif ($string =~ /(.*) was brutalized by (.*)'s quad rocket/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->rocketFrags($fragger->rocketFrags() + 1);
    }
    elsif ($string =~ /(.*) was gibbed by (.*)'s grenade/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->grenadeFrags($fragger->grenadeFrags() + 1);
    }
    elsif ($string =~ /(.*) was gibbed by (.*)'s rocket/) 
    {
      $fraggee = findPlayer($1);
      $fraggee->rocketDeaths($fraggee->rocketDeaths() + 1);
      $fragger = findPlayer($2);
      $fragger->rocketFrags($fragger->rocketFrags() + 1);
    }
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
      $player->frags() . "\t" . 
      $player->rocketFrags() . "\t" .
      $player->rocketDeaths() . "\t" .
      $player->teamKills() . "\t" .
      $player->eff() .
      "\n";
  }
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
