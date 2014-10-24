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
my $align = 0;
$align = $ARGV[7];


my %hash=();

my $count = 0;
open(OUT,">"."run_data.txt");
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
	if (-e $ycoord) {
		$count++;
		print "$count";
	} 
	
	 
    }
}
print OUT "$location_m\t$nClusters\t$Smooth_Radius\t$Smooth_Mode\t$Min_Colony_Size\t$Max_Colony_Size\t$Colony_Neighbors\n";


if($count > 0){
	system("/heap/UMassProject/scripts/Mwork.pl $location_m $align");
	system("/heap/UMassProject/scripts/copy_helper.pl $location_m");
}
else{
	system("/heap/UMassProject/scripts/Awork.pl $location_m $nClusters $Smooth_Radius $Smooth_Mode $Min_Colony_Size $Max_Colony_Size $Colony_Neighbors");
	system("/heap/UMassProject/scripts/copy_helper.pl $location_m");
}

print "Done...\n";

