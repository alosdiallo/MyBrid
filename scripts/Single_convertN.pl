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
unlink("$image");	
my $image_orig = $image;
my $crop = $ARGV[3];
my @crop_array = split(/\s/, $crop);
my $crop_to_use = $crop_array[1];


$image =~ s/.cropped.resized.grey.png/.JPG/g;
my $image_two = $image;
$image_two =~ s/.JPG/""/g;

my $name = $image;



my $orig=$name;

my $png=$image_orig;
my $total_path = $dir.$orig;

	 
#crop out the border

my $myPval = `/opt/csw/bin/convert $image -crop $crop_to_use +repage -resize 25% $png`;
chomp $myPval;

print "$png\n"; 

		


