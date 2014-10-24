#!/opt/csw/bin/perl
use lib "/opt/csw/lib/perl/csw";
use strict;
use warnings;
use File::Copy;

my $location_m = 0;
my $align = 0;

$location_m = $ARGV[0];
$align = $ARGV[1];
chdir $location_m;
my $file = 0;
my %hash=();
my @initialImages = <*>;


foreach $file (@initialImages) {
   
    
    if(($file =~ /.png/) and ($file !~ /_DEBUG/g) and ($file !~ /.outer.final.png/g) and ($file !~ /permissive/g)) {
  
	print "Processing files $file.....\n";
	my @tmp=split(/\./,$file);
	my $name="";
	for(my $i=0;$i<(@tmp-1);$i++) {
	    if($name eq "") { $name = $tmp[$i]; } else { $name=$name.".".$tmp[$i]; }
	}
	my $exten=$tmp[(@tmp-1)];
	my $orig=$name.".".$exten;
	my $xcoord = $orig;
	my $ycoord = $orig;
	$xcoord =~ s/.png/._x_coords.txt/;
	$ycoord =~ s/.png/._y_coords.txt/;

	 
	system("/heap/UMassProject/scripts/Manual_magicPlate.pl -i ".$orig." -xgo ".$xcoord." -ygo ".$ycoord." -d 0");
    }
}


system ("chmod -R 755 *DEBUG"); 
system ("chown alos *DEBUG");
print "\tRemoving JPG files we don't need...\n";
#system ("rm -rf *.cropped.JPG");
#system ("rm -rf *.resized.JPG");

system ("mv *.final.png ../quality_control");
# print "\tCopying the Debug data...\n";
#system ("mv *DEBUG ../final_debug");

my $count = 0;


foreach $file (@initialImages) {


	if(($file =~ /_DATA/) and ($file !~ /_DEBUG/g) and ($file !~ /.outer.final.png/g)){
	
		$count++;
		print "Processing files $file.....\n";
		my @tmp=split(/\./,$file);
		my $name="";
		chdir $file;

		my $intensity = $file;
		my $size = $file;
		my $med_all = $file;
		$intensity =~ s/_DATA/.png.red.median.colony.txt/;
		$med_all =~ s/_DATA/.png.red.median.all.txt/;
		$size =~ s/_DATA/.png.size.txt/;

		my @temp=();
		my $line = 0;
		 
		@temp = split(/\//,$intensity);
		my $filename=$temp[@temp-1];
		@temp=();
		@temp = split(/\_/,$filename);
		$filename = $temp[0]."_".$temp[1];

		my $n=$temp[2];
		my ($plateType);
		if($n eq "N") { 
			$plateType=$temp[3];
		} else {
			$plateType=$temp[2];
		}
 
		print "\tPalte type is $plateType\n";
		 if ($plateType eq "1-4") {	
			print "\tCopying the data...\n";
			copy("$location_m/$file/$intensity","../../1to4/") or die "Copy failed: $!";		
			copy("$location_m/$file/$size","../../1to4/") or die "Copy failed: $!";
			copy("$location_m/$file/$intensity","../../dataFiles/") or die "Copy failed: $!";			
			copy("$location_m/$file/$size","../../dataFiles/") or die "Copy failed: $!";
			
		 }
		 elsif ($plateType eq "5-8") {
			print "\tCopying the data...\n";
			copy("$location_m/$file/$intensity","../../5to8/") or die "Copy failed: $!";
			copy("$location_m/$file/$size","../../5to8/") or die "Copy failed: $!";
			copy("$location_m/$file/$intensity","../../dataFiles/") or die "Copy failed: $!";			
			copy("$location_m/$file/$size","../../dataFiles/") or die "Copy failed: $!";
		 }
		 elsif ($plateType eq "9-12") {
			print "\tCopying the data...\n";
			copy("$location_m/$file/$intensity","../../9to12/") or die "Copy failed: $!";
			copy("$location_m/$file/$size","../../9to12/") or die "Copy failed: $!";
			copy("$location_m/$file/$intensity","../../dataFiles/") or die "Copy failed: $!";			
			copy("$location_m/$file/$size","../../dataFiles/") or die "Copy failed: $!";
		 }

	}
}


#print "\tCopying the data...\n";
#system ("mv *DATA ../final_data");
#system ("rm -rf *.cropped.JPG");
