#!/opt/csw/bin/perl
use lib "/opt/csw/lib/perl/csw";
use strict;
use warnings;
use File::Copy;

my $location_m = 0;
$location_m = $ARGV[0];
print "$location_m\n";
chdir $location_m;
my $file = 0;
my @initialImages = <*>;
my $MAX_PROCESSES = 0;
my $pm = 0;
my $nClusters = 0;
my $Smooth_Radius = 0;
my $Smooth_Mode = 0;
my $Min_Colony_Size = 0;
my $Max_Colony_Size = 0;
my $Colony_Neighbors = 0;
$nClusters = $ARGV[1];
$Smooth_Radius = $ARGV[2];
$Smooth_Mode = $ARGV[3];
$Min_Colony_Size = $ARGV[4];
$Max_Colony_Size = $ARGV[5];
$Colony_Neighbors = $ARGV[6];

foreach $file (@initialImages) {

    if(($file =~ /.png/) and ($file !~ /_DEBUG/g) and ($file !~ /.outer.final.png/g) and ($file !~ /permissive/g)){
        print "processing $file...\n";
        my @tmp=split(/\./,$file);
        my $name="";
        for(my $i=0;$i<(@tmp-1);$i++) {
            if($name eq "") { $name = $tmp[$i]; } else { $name=$name.".".$tmp[$i];}
        }

        my $exten=$tmp[(@tmp-1)];
        my $orig=$name.".".$exten;

        system("/heap/UMassProject/scripts/Auto_magicPlate.pl -i ".$orig." -c $nClusters -r $Smooth_Radius -m $Smooth_Mode -min $Min_Colony_Size -max $Max_Colony_Size -nhood $Colony_Neighbors -d 1");


     }
} 

my $count = 0;
system ("mv *.final.png ../quality_control");
print "Finished generating intensity values...\n";



foreach $file (@initialImages) {
	print "Copying data...\n\n";

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
		print "Copying $intensity\n";
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
 

		 if ($plateType eq "1-4") {	
			system("chmod 755 *.txt");
			print "\n\n$file\n\n";
			copy("$location_m/$file/$intensity","../../1to4/") or die "Copy failed: $!";		
			copy("$location_m/$file/$size","../../1to4/") or die "Copy failed: $!";
			copy("$location_m/$file/$intensity","../../dataFiles/") or die "Copy failed: $!";			
			copy("$location_m/$file/$size","../../dataFiles/") or die "Copy failed: $!";
			
		 }
		 elsif ($plateType eq "5-8") {
		 	system("chmod 755 *.txt");
			print "\n\n$file\n\n";
			copy("$location_m/$file/$intensity","../../5to8/") or die "Copy failed: $!";
			copy("$location_m/$file/$size","../../5to8/") or die "Copy failed: $!";
			copy("$location_m/$file/$intensity","../../dataFiles/") or die "Copy failed: $!";			
			copy("$location_m/$file/$size","../../dataFiles/") or die "Copy failed: $!";
		 }
		 elsif ($plateType eq "9-12") {
			system("chmod 755 *.txt");
			print "\n\n$file\n\n";			
			copy("$location_m/$file/$intensity","../../9to12/") or die "Copy failed: $!";
			copy("$location_m/$file/$size","../../9to12/") or die "Copy failed: $!";
			copy("$location_m/$file/$intensity","../../dataFiles/") or die "Copy failed: $!";			
			copy("$location_m/$file/$size","../../dataFiles/") or die "Copy failed: $!";
		 }

	}
}

print "Done...\n";

