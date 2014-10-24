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
my $crop_name = $ARGV[3];
my $Width = $ARGV[4];
my $Height = $ARGV[5];
my $X_Off_Set = $ARGV[6];
my $Y_Off_Set = $ARGV[7];


chdir $dir;
unlink("$image");	
my $image_orig = $image;

$image =~ s/.cropped.resized.grey.png/.JPG/g;
my $image_two = $image;
$image_two =~ s/.JPG/""/g;

my $name = $image;



my $orig=$name;

my $png=$image_orig;
my $total_path = $dir.$orig;

	 
#crop out the border
my $crop_settings = $Width."x".$Height."+".$X_Off_Set."+".$Y_Off_Set;
print "\tcropping image...\n";
system("/opt/csw/bin/convert ".$image." -crop ".$Width."x".$Height."+".$X_Off_Set."+".$Y_Off_Set." +repage -resize 25% ".$png);




print "$png\n"; 

		


