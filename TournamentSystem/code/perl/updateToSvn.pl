#!/usr/bin/perl

#not close to finished

my $date = `date +%F`; chomp($date);
my $filename = "SiteBackup-$date.tar";
my $htmlHome = "/var/www/html/";
@backupDirectories = qw(php perl css);

$shell = `tar -cf "$filename" /var/www/html/php /var/www/html/perl /var/www/html/css /var/www/html/*.php`;

$shell = `gzip -9 "$filename"`;

# copy the new stuff over.. (careful not to copy hidden svn files)

$shell = `mv /var/www/html/php/dbConnect.php /var/www/html/php/oldDbConnect.php`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/php/classes/*.php /var/www/html/php/classes`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/php/*.php /var/www/html/php`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/perl/qwGraph.pm /var/www/html/perl`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/perl/mvdPlayer.pm /var/www/html/perl`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/perl/mvdReport.pm /var/www/html/perl`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/perl/mvdStats.pl /var/www/html/perl`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/perl/mvdTeam.pm /var/www/html/perl`;

$shell = `cp -f /usr/www/svn/TournamentSystem/code/perl/convertAscii.sed /var/www/html/perl`;

$shell = `cp -f /usr/www/svn/website/index.php /var/www/html`;

$shell = `cp -f /usr/www/svn/website/css/default.css /var/www/html/css`;

$shell = `cp -f /usr/www/svn/website/img/*.jpg /var/www/html/img`;

$shell = `cp -f /usr/www/svn/website/png/*.png /var/www/html/img`;

$shell = `mv /var/www/html/php/oldDbConnect.php /var/www/html/php/dbConnect.php`;
