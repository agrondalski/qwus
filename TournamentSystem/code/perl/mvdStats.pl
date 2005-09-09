#!/usr/bin/perl

# todo:
# fix remaining fun names errors
# nice output for easy database entry
# attempt to read and/or calculate final score
# misc bs
# optimize
# fix package lameness
# finish team support
# either ctf is messed up or blood and forrests names are lame

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
  #  $self->{SATAN_FRAGS} = 0;
  #  $self->{BORES} = 0;
  #  $self->{SELF_GRENADE_DEATHS} = 0;
    $self->{DISCHARGES} = 0;
    $self->{DISCHARGE_DEATHS} = 0;
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
    if ($self->deaths + $self->frags < 1) { return 0; }
    return ($self->frags / ($self->deaths + $self->frags)) * 100;
}

$start = new Benchmark;

foreach $mvd (@ARGV)
{
  $tempMvd = $mvd . ".tmp";
  $shell = `sed -f convertAscii.sed "$mvd" > "$tempMvd"`;
  @strings = `strings -3 "$tempMvd"`;

  foreach $string (@strings)
  {
    if (length($string) < 8)
    { 
     1;
    }
    elsif ($string =~ /(.*) rides (.*)'s rocket/)
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
    elsif ($string =~ /(.*) eats (.*)'s pineapple/)
    {
	$fraggee = findPlayer($1);
	$fraggee->grenadeDeaths($fraggee->grenadeDeaths() + 1);
	$fragger = findPlayer($2);
	$fragger->grenadeFrags($fragger->grenadeFrags() + 1);
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
    elsif ($string =~ /(.*) loses another friend/) 
    {
      $fragger = findPlayer($1);
      $fragger->teamKills($fragger->teamKills() + 1);
    }
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
    elsif ($string =~ m/\\name\\/)
    {
        $name = $';
        if ($string =~ m/\\team\\/)
        {
# todo: skip if spectator > 0
	    $team = $';
            while ($team =~ /(.*)\\/) { $team = $1; }
            while ($name =~ /(.*)\\/) { $name = $1; }
            $name =~ s/\s+$//;
            $team =~ s/\s+$//;
            $player = findPlayer($name);
            $player->team($team);
        }    
    }
# once we reach this point the match is over and no good data remains
# breaking out of the loop not only provides a speed boost, but
# eliminates the disconnected player list from being added again
    elsif ($string =~ /. - disconnected players/) { last; }
  }
  $shell = `rm "$tempMvd"`;
}

outputHTML();

$end = new Benchmark;
$diff = Benchmark::timediff($end, $start);
print "\n\nBenchmark: " . Benchmark::timestr($diff, 'all') . "\n"; 

sub removeTrailingWhiteSpace
{
  $string = shift;
  $string =~ s/\s+$//;
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
