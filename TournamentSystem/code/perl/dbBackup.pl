#!/usr/bin/perl

# todo:
# fix password support
# automated offsite transfer

@databases = ("dew", "qwus", "phpbb");
$user = "root";
$pw = "fred";

$date = `date +%F`;
chomp($date);

foreach $database (@databases)
{
  $filename = $database . "-" . $date . ".sql";
  $shellOut = 
    `mysqldump $database -u $user -p --single_transaction --force > $filename`;
}

$filename = "backup-" . $date . ".tar";
$shellOut = `tar -cf $filename  *.sql`;
$shellOut = `gzip -9 $filename`;

$shellOut = `rm -f *.sql`;
