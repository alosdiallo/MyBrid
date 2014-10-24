use strict;
use English;
# Created by Alos Diallo UMASS Medical School
my %hash=();
my $location_m = $ARGV[0];
my $Width = $ARGV[1];
my $Height = $ARGV[2];
my $X_Off_Set = $ARGV[3];
my $Y_Off_Set = $ARGV[4];
my $answer = $ARGV[5];

chdir $location_m;

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
		system("/opt/csw/bin/convert ".$orig." -crop ".$Width."x".$Height."+".$X_Off_Set."+".$Y_Off_Set."+repage ".$cropped);
		
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
