#!/usr/bin/perl -w
use strict;
use GD::Graph::lines;
use GD::Graph::pie;
use GD::Graph::colour;
package qwGraph;

sub line_graph{
  #if (@_) { $x = shift; $y = shift;} 
  my $qwhash = shift;
  
  my @data = @{$qwhash->{'data'}};
  my @legendPlayers = @{$qwhash->{'legend'}};
  my ($x,$y) = ($qwhash->{'x'},$qwhash->{'y'});
  my $title = $qwhash->{'title'};
  my $x_label = $qwhash->{'xlabel'};
  my $y_label = $qwhash->{'ylabel'};
  my @colorArray = @{$qwhash->{'colors'}};
  my $tempDir = $qwhash->{'tempDir'};
  my $graph = GD::Graph::lines->new($x,$y);
  
  my $showvals;
  if(exists($qwhash->{'showvalues'}))
  {
  $showvals = $qwhash->{'showvalues'};
  }
  $graph->set(title => $title,
              x_label => $x_label,
              x_label_position => .5,
              y_label => $y_label,
              line_width => 2
	      );
 
  $graph->set(dclrs => [@colorArray]);
  $graph->set_legend(@legendPlayers);
   if ($x < 401)
  {
    $graph->set(x_label_skip => 5)
  }
  elsif(defined($showvals) && ($showvals ne ""))
  { 
  $graph->set(show_values => $showvals);
  }
  my $image = $graph->plot(\@data); # or die ("Died creating image");
  
  my $imagePath = $tempDir . $title . "_" . $x . "x" . $y . ".png";
  $imagePath =~ s/\s/\_/g;
  open(OUT, ">$imagePath") or die $!;
  binmode OUT;
  print OUT $image->png();
  close OUT;
  return $imagePath;
}

sub pie_graph{
	my $qwhash = shift;
	my @data = @{$qwhash->{'data'}};
	#my @legendPlayers = @{$qwhash->{'legend'}};
  	my ($x,$y) = ($qwhash->{'x'},$qwhash->{'y'});
  	my $title = $qwhash->{'title'};
  	my $x_label = $qwhash->{'xlabel'};
  	my $y_label = $qwhash->{'ylabel'};
  	my @colorArray = @{$qwhash->{'colors'}};
  	my $tempDir = $qwhash->{'tempDir'};
	if(!defined($tempDir)){die $!;}
	my $graph = GD::Graph::pie->new($x,$y);
    	$graph->set(title => $title,
        suppress_angle => 3) or warn $graph->error;
        $graph->set(dclrs => [@colorArray]);
 
	my $image = $graph->plot(\@data); # or warn $graph->error;
        my $imagePath = $tempDir . $title . ".png";
	$imagePath =~ s/\s/\_/g;
    open(OUT, ">$imagePath");
    binmode OUT;
    print OUT $image->png();
    close OUT;    
  return $imagePath;
}
1;
