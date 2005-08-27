#!/usr/bin/perl

@databases = ("dew", "qwus", "phpbb");
$user = "export_user";
$pw = "export";

$date = `date +%F`;
chomp($date);

foreach $database (@databases)
{
  $filename = $database . "-" . $date . ".sql";
  $shellOut = `mysqldump $database --user=$user --password=$pw --single_transaction --force > $filename`;
}

$filename = "dbBackup-" . $date . ".tar";
$shellOut = `tar -cf $filename  *.sql`;
$shellOut = `gzip -9 $filename`;
$filename = $filename . ".gz";
$shellOut = `rm -f *.sql`;
$shellOut = `mutt -s "QuakeWorld.US Database Backup" -a $filename ult1m0\@yahoo.com < /dev/null`;
$shellOut = `mutt -s "QuakeWorld.US Database Backup" -a $filename skelman\@skelman.com < /dev/null`;
