#!/opt/csw/bin/perl
#!/opt/csw/bin/gs
use Env qw(PATH);
use strict;
use warnings;
use POSIX;
use Data::Dumper;
use File::Path;
use File::Copy;
my $images = "/heap/UMassProject/raw_images/";
my $dest = "/heap/UMassProject/trash/";
my $dir = $ARGV[0];
my @row_arrayN = split(/\//, $dir);
my $size = scalar(@row_arrayN);
print "$row_arrayN[$size -1]\n";
my $dir_holder = 0;
$dir_holder = $row_arrayN[$size -1];
print "\n$dir\there\n";
chdir $images;
my $trash = $row_arrayN[$size -1];
#system("ls -la");
#system("cp -R $dir $dest");


#my $file_to_delete = "/heap/UMassProject/raw_images/1072missedC12/dataFiles/output.list.txt.safe.txt";

if (-e $dir) {
	print "Directory '$dir' still exists";
	rmdir $dir;
	#system("mv $dir_holder $dest");
	system("ls -la");
}
else 
{
	print "File deleted.";
}