#!/opt/csw/bin/perl
use lib "/opt/csw/lib/perl/csw";
use warnings;
use strict;
my $location_m = 0;
$location_m = $ARGV[0];
$location_m = $location_m."1to4/";

my %tfIntensity=();
my %tfSize=();
my %arrayFileHash=();
my $data = 0;
my $size = 0;
my $intensity_file_name = 0;
my $size_file_name = 0;
my $arrayFile = 0;
chdir $location_m;
my @files = <*>;
foreach my $file (@files) {
	
	#if(($file =~ /cropped.resized.grey.png.red.median.colony.txt$/) or ($file =~ /cropped.resized.grey.png.red.median.all.txt$/)) {
	if($file =~ /cropped.resized.grey.png.red.median.colony.txt$/) {	
		$data = $file;
		my @tmp=split(/_/,$data);
		my @temp = split(/\_/,$file);
		$intensity_file_name = $temp[0]."_".$temp[1]."_".$temp[2];
		#fix for retarted naming
		my $n=$tmp[2];
		my ($plateType);
		if($n eq "N") {
			$plateType=$tmp[3];
		} else {
			$plateType=$tmp[2];
		}
	    $arrayFile=$plateType.".txt";
		$intensity_file_name = $temp[0]."_".$temp[1]."_".$plateType;
		$tfIntensity{$intensity_file_name}{'data'}=$data;

	}


}

foreach $intensity_file_name ( keys %tfIntensity ) {
		my $intensityFile = $tfIntensity{$intensity_file_name}{'data'};
		print "\nprocessing $intensityFile...\n";
		system ("/heap/UMassProject/scripts/1to4/program3.pl $intensityFile mediansN.txt");
		print "\t/heap/UMassProject/scripts/1to4/program3.pl $intensityFile mediansN.txt\n";
	
}
	
