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
my $image_orig = $image;
unlink("$image");	
$image =~ s/.cropped.resized.grey.png/.JPG/g;
my $image_two = $image;
$image_two =~ s/.JPG/""/g;

my $name = $image;



my $orig=$name;

my $png=$image_orig;
my $total_path = $dir.$orig;

	 
#crop out the border
print "\tcropping $orig image...\n";
my $path = "";
#chdir $path;


my $myPval = `/opt/csw/bin/convert $image -crop 3490x2350+370+235 +repage -resize 25% $png`;
chomp $myPval;
#system("/opt/csw/bin/convert ".$image." -crop 3490x2350+370+235 +repage -resize 25% ".$png);
print "$png\n"; 



