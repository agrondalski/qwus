#!/usr/bin/perl

$date = `date +%F`;
chomp($date);

$filename = "svnBackup-" . $date;
$shellOut = `svnadmin dump /usr/local/svn --incremental > $filename`;

$shellOut = `gzip -9 $filename`;
$shellOut = `rm -f $filename`;
$filename = $filename . ".gz";

#this could become to large for email at some point
#also once a week backup would likely be acceptable, 
#but we'll start with nightly

$shellOut = `mutt -s "QuakeWorld.US Subversion Backup" -a $filename ult1m0\@yahoo.com < /dev/null`;
#$shellOut = `mutt -s "QuakeWorld.US Subversion Backup" -a $filename skelman\@skelman.com < /dev/null`;
