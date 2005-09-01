#!/usr/bin/perl

# todo:
# handle fun names (could be fairly tricky in some cases)
# nice output for easy database entry
# attempt to read and/or calculate final score
# misc bs

foreach $mvd (@ARGV)
{
  @strings = `strings $mvd`;

  foreach $string (@strings)
  {
    if ($string =~ /(.*) rides (.*)'s rocket/)
    {
      print "rl: " . $1 . "\t" . $2 . "\n";
    }
    elsif ($string =~ /(.*) accepts (.*)'s shaft/)
    {
      print "lg: " . $1 . "\t" . $2 . "\n"; 
    }
    elsif ($string =~ /(.*) chewed on (.*)'s boomstick/) {}
    elsif ($string =~ /(.*) was punctured by (.*)/ {}
    elsif ($string =~ /(.*) was nailed by (.*)/) {}
    elsif ($string =~ /(.*) ate 2 loads of (.*)'s buckshot/) {}
    elsif ($string =~ /(.*) was brutalized by (.*)'s quad rocket/) {}
    elsif ($string =~ /(.*) was gibbed by (.*)'s grenade/) {}
    elsif ($string =~ /(.*) was gibbed by (.*)'s rocket/) {}
    elsif ($string =~ /(.*) was telefragged by his teammate/) {}
    elsif ($string =~ /(.*) was telefragged by (.*)/) {}
    elsif ($string =~ /(.*) mows down a teammate/) {}
    elsif ($string =~ /(.*) checks his glasses/) {}
  }
}
