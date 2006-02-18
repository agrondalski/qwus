#!/usr/bin/perl -w
use strict;

# todo:
# add real team name in addition to red\blue to graphs for ctf
# insane demo support for non tournament games--Started--refer from demoStats
# can dew support undef values?
# check ctf ax frags
# improve regexs
# bring ktpro stats up to par with pure ctf
# team stats (end of mvd)
# player minutes played ( * left the game)

use Benchmark;
use CGI qw/:standard/;
use mvdReport;

my $DEBUG = 0;

my $mvd = shift(@ARGV);
my($tourney_id, $division_id, $match_id, $approved, $teamOneAbbr, $teamTwoAbbr,$teamOnePlayers,$teamTwoPlayers);
my $cgi;
if (!$mvd eq "")
{
  $DEBUG = 1;
  print "-- Debug Enabled --\n";
}
else
{
  $cgi = new CGI;
  print "Content-type: text/html\n\n";
  my $referer = $ENV{"HTTP_REFERER"};
  if ($referer =~ /demoStats/)
  {
    $mvd = $cgi->param('filename');
    $tourney_id = -1;
  }
  elsif ($referer =~ /reportMatch/)
  {
    $tourney_id = $cgi->param('tourney_id');
    $division_id = $cgi->param('division_id');
    $match_id = $cgi->param('match_id');
    $approved = $cgi->param('approved');
    $mvd = $cgi->param('filename');
    $teamOneAbbr = $cgi->param('team1');
    $teamTwoAbbr = $cgi->param('team2');
    $teamOnePlayers = $cgi->param('team1players');
    $teamTwoPlayers = $cgi->param('team2players');
  }
  else { exit; }
}
if($DEBUG){
($tourney_id, $division_id, $match_id,$approved,$teamOneAbbr,$teamTwoAbbr) = ("CTF2006","A","320","1","","");
$teamOnePlayers = [];
$teamTwoPlayers = [];
}
my $mvdRep = mvdReport->new();

$mvdRep->{tempDir} = 'tmp/';
$mvdRep->{tourney_id} = $tourney_id; 
$mvdRep->{division_id} = $division_id; 
$mvdRep->{match_id} = $match_id; 
$mvdRep->{approved} = $approved; 
$mvdRep->{teamOneAbbr} = $teamOneAbbr; 
$mvdRep->{teamTwoAbbr} = $teamTwoAbbr;
$mvdRep->{teamOnePlayers} = $teamOnePlayers;
$mvdRep->{teamTwoPlayers} = $teamTwoPlayers;
	
$mvdRep->mvdtoStrings($mvd);
$mvdRep->parseStrings();

#
#if ($DEBUG)
#{
#  foreach $team (@teams)
#  {
#    print $team->name . "\n";
#    $team->playerList();
#  }
#}
$mvdRep->calculateTeamColors();
$mvdRep->outputForm();
exit;
