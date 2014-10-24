use strict;
use English;

my %hash=();

my $location_m = $ARGV[0];
my $crop = $ARGV[1];
chdir $location_m;
my @crop_array = split(/\s/, $crop);
my $crop_to_use = $crop_array[1];
my @initialImages = <*>;
foreach my $file (@initialImages) {
	if(($file =~ /.JPG/) and ($file !~ /.cropped./g)) {
		print "processing $file...\n";
		my @tmp=split(/\./,$file);
		my $name="";
		for(my $i=0;$i<(@tmp-1);$i++) {
			if($name eq "") { $name = $tmp[$i]; } else { $name=$name.".".$tmp[$i]; }
		}
		my $exten=$tmp[(@tmp-1)];
		
		my $orig=$name.".".$exten;
		my $cropped=$name.".cropped.".$exten;
		my $resized=$name.".cropped.resized.".$exten;
		my $greyscale=$name.".cropped.resized.grey.".$exten;
		my $png=$name.".cropped.resized.grey.png";
			
		#crop out the border
		print "\tcropping image...\n";
		
		#system("/opt/csw/bin/convert ".$orig." -crop 4100x2750+520+065 +repage ".$cropped);#for Alex remove when done
		system("/opt/csw/bin/convert ".$orig." -crop $crop_to_use +repage ".$cropped);#normal keep
		print "\n/opt/csw/bin/convert $orig -crop $crop_to_use +repage $cropped\n";
		
		#resize it to 25%
		print "\tresizing to 25%...\n";
		system("/opt/csw/bin/convert ".$cropped." -resize 25% ".$resized);
		
		#jpg -> png
		print "\tconverting to PNG...\n";
		system("/opt/csw/bin/convert ".$resized." ".$png);
		
		my @sections=split(/_/,$file);
		my $id="";
		for(my $i=0;$i<3;$i++) {
			if($id eq "") { $id = $sections[$i]; } else { $id = $id ."_". $sections[$i]; }
		}
	

	}
}


