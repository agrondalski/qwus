#!/usr/bin/perl -w
use strict;
use Benchmark;
use CGI qw/:standard/;
use mvdReport;

my $DEBUG = 0;

my $mvd = shift(@ARGV);
my($pass_thu, $teamOneAbbr, $teamTwoAbbr,$teamOnePlayers,$teamTwoPlayers);
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
    $mvd = $cgi->param('filename');
    $teamOneAbbr = $cgi->param('team1');
    $teamTwoAbbr = $cgi->param('team2');
    $teamOnePlayers = $cgi->param('team1players');
    $teamTwoPlayers = $cgi->param('team2players');
    $pass_thru = $cgi->param('pass_thru');
  }
  else { exit; }
}
if($DEBUG){
($teamOneAbbr,$teamTwoAbbr) = ("","");
$teamOnePlayers = [];
$teamTwoPlayers = [];
}
my $mvdRep = mvdReport->new();

$mvdRep->{tempDir} = '/tmp/';
$mvdRep->{teamOneAbbr} = $teamOneAbbr; 
$mvdRep->{teamTwoAbbr} = $teamTwoAbbr;
$mvdRep->{teamOnePlayers} = $teamOnePlayers;
$mvdRep->{teamTwoPlayers} = $teamTwoPlayers;
$mvdRep->{pass_thru} = $pass_thru;	
$mvdRep->mvdtoStrings($mvd);
$mvdRep->parseStrings();
$mvdRep->calculateTeamColors();
$mvdRep->outputForm();
exit;
