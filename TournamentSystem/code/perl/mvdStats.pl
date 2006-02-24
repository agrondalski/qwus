#!/usr/bin/perl -w
use strict;
use Benchmark;
use CGI qw/:standard/;
use mvdReport;

my $DEBUG = 0;

my $mvd = shift(@ARGV);
my($tourney_id, $division_id, $match_id, $approved, $teamOneAbbr, $teamTwoAbbr,$teamOnePlayers,$teamTwoPlayers,$screenshot_url);
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
  elsif ($referer =~ /reportMatch/ || $referer =~ /recomputeGame/)
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
    $screenshot_url = $cgi->param('screenshot_url');
  }
  else { exit; }
}
if($DEBUG){
($tourney_id, $division_id, $match_id,$approved,$teamOneAbbr,$teamTwoAbbr) = ("CTF2006","A","320","1","","");
$teamOnePlayers = [];
$teamTwoPlayers = [];
}
my $mvdRep = mvdReport->new();

$mvdRep->{tempDir} = '/tmp/';
$mvdRep->{tourney_id} = $tourney_id; 
$mvdRep->{division_id} = $division_id; 
$mvdRep->{match_id} = $match_id; 
$mvdRep->{approved} = $approved; 
$mvdRep->{teamOneAbbr} = $teamOneAbbr; 
$mvdRep->{teamTwoAbbr} = $teamTwoAbbr;
$mvdRep->{teamOnePlayers} = $teamOnePlayers;
$mvdRep->{teamTwoPlayers} = $teamTwoPlayers;
$mvdRep->{screenshot_url} = $screenshot_url;	
$mvdRep->mvdtoStrings($mvd);
$mvdRep->parseStrings();
$mvdRep->calculateTeamColors();
$mvdRep->outputForm();
exit;
