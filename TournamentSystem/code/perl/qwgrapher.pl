#!/usr/bin/perl -w
use strict;
use qwGraph;
use CGI qw/:standard/;
#
#    $tourney_id = $cgi->param('tourney_id');
#    $division_id = $cgi->param('division_id');
#    $match_id = $cgi->param('match_id');
#    $approved = $cgi->param('approved');
#    $mvd = $cgi->param('filename');
#    $teamOneAbbr = $cgi->param('team1');
#    $teamTwoAbbr = $cgi->param('team2');
#    $teamOnePlayers = $cgi->param('team1players');
#    $teamTwoPlayers = $cgi->param('team2players');

#playerscoregraph
my $cgi = new CGI;
my $graphlabels = $cgi->param('graphlabels');#"1,2,3";
my $datapoints = $cgi->param('datapoints');#"10,20,50;5,5,40;8,27,39";
my $datalabels = $cgi->param('datalabels');#"Larry,Moe,Curly";
my($title) = $cgi->param('test');#"test";
my($x_label) = $cgi->param('xlabel');#"time";
my($y_label) = $cgi->param('ylabel');#"score";
my($x) = $cgi->param('x');#"400";
my($y) = $cgi->param('y');#"300";
my $graphtype = "pie"; #"pie" or "line"
my $tempdir = "/tmp/";
#player piechart

my $imagePath;
if($graphtype eq "line"){
my @data;
push(@data,[split(/\,/,$graphlabels)]);
my @legend = split(/\,/,$datalabels);
  foreach my $dataset (split(/\;/,$datapoints))
  {
  my @scoreArray = split(/\,/,$dataset);
  push(@data, \@scoreArray); 
  }
my @colorArray = qw(red orange blue dgreen dyellow cyan marine purple);
my $datahash = { 'data'    => \@data,
              'x'       => $x,
	      'y'       => $y,
	      'x_label' => $x_label,
	      'y_label' => $y_label,
	      'title'   => $title,
	      'colors'  => \@colorArray,
	      'legend'  => \@legend,
	      'tempDir' => $tempdir
	      };
 		
$imagePath = outputPlayerScoreGraph($datahash);
print "$imagePath\n";
}
elsif($graphtype eq "pie"){  
my @players = split(/\,/,$datalabels);
my @dataset = split(/\;/,$datapoints);
my @colorArray = qw(lred orange purple dgreen dyellow cyan marine);
if(!($#players == $#dataset)){return 0;}
  for(my $i = 0; $i <= $#players; $i++)
  { 
  my @temp = split(/\,/,$dataset[$i]);
  my @weaponList = (  "SG " . $temp[0],
                      "SSG " . $temp[1],
                      "NG " . $temp[2],
                      "SNG " . $temp[3],
                      "GL " . $temp[4],
                      "RL " . $temp[5],
                      "LG " . $temp[6]); 
  my @data = (\@weaponList, \@temp);
  my $datahash = { 'data'    => \@data,
                'x'       => $x,
	        'y'       => $y,
	        'title'   => $title,
	        'colors'  => \@colorArray,
	        'tempDir' => $tempdir
	      };
 $imagePath .= outputPlayerPieCharts($datahash);
 }
print "$imagePath\n";
}  

exit;


sub outputPlayerScoreGraph
{
  my $datahash = shift;
  my $x = 400; my $y = 300;
  if (@_) { $x = shift; $y = shift; } 
  
  
 
  my %qwhash = (	'data'     => $datahash->{data},
    			'x'	   => $datahash->{x},
			'y'	   => $datahash->{y},
			'x_label'  => $datahash->{xlabel},
			'y_label'  => $datahash->{ylabel},
			'legend'   => $datahash->{legend},
			'title'	   => $datahash->{title},
    			'colors'   => $datahash->{colors},
			'imagePath'=> $datahash->{tempDir} . $datahash->{title} . "_" . $x . "x" . $y . ".png"
			);
    
   
    my $imagePath = qwGraph::line_graph(\%qwhash);
return $imagePath;
}



sub outputPlayerPieCharts
{
my $datahash = shift;
my %qwhash = (	'data'=> $datahash->{data},
        	'x'	=> '250',
		'y'	=> '175',
		'title'	=> $datahash->{tempDir} . $datahash->{title} . "_" . $x . "x" . $y . ".png",
    		'colors'=> $datahash->{colors},
		'tempDir'=> $datahash->{'tempDir'}
		);
 
my $imagePath = qwGraph::pie_graph(\%qwhash);
return $imagePath;
}   

