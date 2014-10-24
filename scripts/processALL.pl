#!/opt/csw/bin/perl
use lib "/opt/csw/lib/perl/csw";
use warnings;
use strict;
my $location_m = 0;
$location_m = $ARGV[0];
my $ignore_list = $ARGV[1]; 
chdir $location_m;
my %tfIntensity=();
my %tfSize=();
my %tfzscore=();
my %tfnorm=();
my %arrayFileHash=();
my $data = 0;
my $size = 0;
my $zscore = 0;
my $norm = 0;
my $intensity_file_name = 0;
my $size_file_name = 0;
my $zscore_file_name = 0;
my $norm_file_name = 0;
my $arrayFile = 0;
print "here\n";
my @files = <*>;
foreach my $file (@files) {
	#if(($file =~ /cropped.resized.grey.png.red.median.colony.txt$/) or ($file =~ /cropped.resized.grey.png.red.median.all.txt$/)){
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
		$tfIntensity{$intensity_file_name}{'array'}=$arrayFile;
		
		
	}
	elsif($file =~ /cropped.resized.grey.png.size.txt$/) {
		$size = $file;
		my @tmp= split(/_/,$size);
		my @temp = split(/\_/,$file);
			my $n=$tmp[2];
		my ($plateType);
		if($n eq "N") {
			$plateType=$tmp[3];
		} else {
			$plateType=$tmp[2];
		}
		$size_file_name = $temp[0]."_".$temp[1]."_".$plateType;
		$tfSize{$size_file_name}=$size;
	}
	elsif($file =~ /cropped.resized.grey.png.zscore.txt$/) {
		$zscore = $file;
		my @tmp= split(/_/,$zscore);
		my @temp = split(/\_/,$file);
			my $n=$tmp[2];
		my ($plateType);
		if($n eq "N") {
			$plateType=$tmp[3];
		} else {
			$plateType=$tmp[2];
		}
		$zscore_file_name = $temp[0]."_".$temp[1]."_".$plateType;
		$tfzscore{$zscore_file_name}=$zscore;
	}
	elsif($file =~ /.txt.norm_values.txt$/) {
		$norm = $file;
		my @tmp= split(/_/,$norm);
		my @temp = split(/\_/,$file);
			my $n=$tmp[2];
		my ($plateType);
		if($n eq "N") { 
			$plateType=$tmp[3];
		} else {
			$plateType=$tmp[2];
		}
		$norm_file_name = $temp[0]."_".$temp[1]."_".$plateType;
		$tfnorm{$norm_file_name}=$norm;
	}

}  

foreach $intensity_file_name ( keys %tfIntensity ) {
	if(exists($tfSize{$intensity_file_name})){
		if(exists($tfzscore{$intensity_file_name})){
			if(exists($tfnorm{$intensity_file_name})){
				my $zscoreFile = $tfzscore{$intensity_file_name};
				my $sizeFile = $tfSize{$intensity_file_name};
				my $normFile = $tfnorm{$intensity_file_name};
				my $intensityFile = $tfIntensity{$intensity_file_name}{'data'};
				my $arrayHash = $tfIntensity{$intensity_file_name}{'array'};
				print "\nprocessing $intensityFile...\n";
				system ("/heap/UMassProject/scripts/stats_matrix_lite.pl $intensityFile /heap/UMassProject/arrayFiles/$arrayHash /heap/UMassProject/goldFile/johns_calls.txt $sizeFile $zscoreFile $normFile  /heap/UMassProject/goldFile/bin1_list.txt /heap/UMassProject/goldFile/worm_TF.txt /heap/UMassProject/goldFile/bait_list.txt $ignore_list $location_m");
				print "\t/heap/UMassProject/scripts/stats_matrix_lite.pl $intensityFile /heap/UMassProject/arrayFiles/$arrayHash /heap/UMassProject/goldFile/johns_calls.txt $sizeFile $zscoreFile $normFile  /heap/UMassProject/goldFile/bin1_list.txt /heap/UMassProject/goldFile/worm_TF.txt /heap/UMassProject/goldFile/bait_list.txt $ignore_list $location_m\n";
			}	
		}	
	}
	
}
	
