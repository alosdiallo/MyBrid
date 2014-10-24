#!/opt/csw/bin/perl
use lib "/opt/csw/lib/perl/csw";
use strict;
use warnings;
use File::Copy;

my $location_m = 0;
my $align = 0;

$location_m = $ARGV[0];

chdir $location_m;
my $file = 0;
my %hash=();
my @initialImages = <*>;
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
