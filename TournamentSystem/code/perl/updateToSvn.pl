#!/usr/bin/perl

#not close to finished

my $date = `date +%F`; chomp($date);
my $filename = "SiteBackup-$date.tar";
my $htmlHome = "/var/www/html/";
@backupDirectories = qw(php perl css);

foreach $dir (@backupDirectories)
{
    print "$dir\n";
}

$shell = `tar -cf "$filename" /var/www/html/php /var/www/html/perl /var/www/html/css /var/www/html/*.php`;

#$shell = `gzip -9 "$filename"`;

# copy the new stuff over.. (careful not to copy hidden svn files)

$shell = `cp -f /usr/www/svn/TournamentSystem/code/php/classes/*.php /var/www/html/php/classes`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/php/*.php /var/www/html/php`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/perl/*.pl /var/www/html/perl`;

