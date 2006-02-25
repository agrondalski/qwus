#!/usr/bin/perl -w
use strict;
use CGI qw/:standard/;
use mvdReport;

my $DEBUG = 0;
my $mvd = shift(@ARGV);
my($pass_thru, $teamOneAbbr, $teamTwoAbbr,$teamOnePlayers,$teamTwoPlayers);
my $cgi;

if (!$mvd eq "")
{
  $DEBUG = 0;
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
  }
  elsif ($referer =~ /reportMatch/ || $referer =~ /recomputeGame/
                                   || $referer =~ /statCreation/)
  {
    $mvd = $cgi->param('filename');
    $teamOneAbbr = $cgi->param('team1');
    $teamTwoAbbr = $cgi->param('team2');
    $teamOnePlayers = $cgi->param('team1players');
    $teamTwoPlayers = $cgi->param('team2players');
    $pass_thru = $cgi->param('pass_thru');
  }
  else 
  {
    print "Invalid referer: $referer<br>\n"; 
    exit; 
  }
}

if ($DEBUG)
{
  ($pass_thru, $teamOneAbbr, $teamTwoAbbr) = ("", "", "");
  $teamOnePlayers = [];
  $teamTwoPlayers = [];
}

my $mvdRep = mvdReport->new();

my $seconds = `date +%s`;
chomp($seconds);
my $tempDir = "/tmp/" . $seconds;
my $shell = `mkdir $tempDir`;
$tempDir .= "/";

$mvdRep->{tempDir}        = $tempDir;
$mvdRep->{teamOneAbbr}    = $teamOneAbbr; 
$mvdRep->{teamTwoAbbr}    = $teamTwoAbbr;
$mvdRep->{teamOnePlayers} = $teamOnePlayers;
$mvdRep->{teamTwoPlayers} = $teamTwoPlayers;
$mvdRep->{pass_thru}      = $pass_thru;	

$mvdRep->mvdtoStrings($mvd);
$mvdRep->parseStrings();
$mvdRep->calculateTeamColors();
$mvdRep->outputForm();
exit;
