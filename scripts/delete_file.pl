#!/opt/csw/bin/perl
#!/opt/csw/bin/gs
use Env qw(PATH);
use strict;
use warnings;
use POSIX;
use Data::Dumper;
use File::Copy;

my $image = $ARGV[0];
my $dir = $ARGV[1];
my $fullPath = $ARGV[2];
chdir $dir;
my $total_path = $dir.$image;
my $total_path_moved = $dir."old.".$image.".old";
print "in the script\n";
print "$total_path\n";

if (-e $total_path) {
	unlink("$total_path");
}
