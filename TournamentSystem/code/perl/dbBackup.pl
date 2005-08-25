#!/usr/bin/perl

# todo:
# automated offsite transfer

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

$filename = "backup-" . $date . ".tar";
$shellOut = `tar -cf $filename  *.sql`;
$shellOut = `gzip -9 $filename`;

$shellOut = `rm -f *.sql`;
