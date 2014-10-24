#!/opt/csw/bin/perl
use strict;
#use warnings;
########################################################################
# Script: thumb3plate.pl
# Description: Will go into the database and pick out unique Idbaits and
#              create a heatmapped plate for them then save it
# By: Justin Nelson
########################################################################



use DBI;
use GD;

my $output_folder = "/heap/UMassProject/thumbs/";

my $databasename = "umass";
my $host = "localhost";
my $username = "umass_build";
my $pw = "3&2aswe!7";

print "checkpoint 1 reached: INITIALIZED\n";

my $dbh = DBI->connect('DBI:mysql:'.$databasename.';host='.$host, $username, $pw, { RaiseError => 1 }) or die "Could not connect to database: $DBI::errstr";

my $tablename = "Promoter";
my $searchterm = "bait_id, user_id, project_id";

print "checkpoint 2 reached: Searching...\n";
my $sth = $dbh->prepare('SELECT DISTINCT '.$searchterm.' FROM '.$tablename); # sth = statement handle; common
my $rv = $sth->execute();
print "checkpoint 3 reached: Done\n";
print "checkpoint 4 reached: Filling IDBaits\n";
##############################################################
#Make Distinct IDbaits
##############
my @distinctID;
my @userID;
my @projectID;                                   
##########################################    
## error handling     
if(!$rv){                       
	handleError($dbh->errstr)
##########################################        
} else {                                         
	while(my @results = $sth->fetchrow_array() ){ 
		push(@distinctID, $results[0]);
		push(@userID, $results[1]);
		push(@projectID, $results[2]);          
	}                                             
}                                                 
## END make distinct Idbaits
##############################################################
$sth->finish();

print "List of distinct IDBaits:\n";
print "@distinctID\n";
print "checkpoint 5 reached: Complete\n";
#return;
my @baitMod = ("0", "1", "2");
my @matrix;


my $tablename_Q2 = "Interactions";
my $searchterm_Q2 = "plate_name";
my $searchterm2_Q2 = "plate_number";
my $searchterm3_Q2 = "user_id";
my $color;
my $xc;
my $yc;
my $val;
#my $sth;
#my $rv;
print "checkpoint 6 reached: Generating images\n";
my $j = 0;
foreach my $idDistinct (@distinctID) {
	my $i = 0;
	foreach my $mod (@baitMod){
		$sth = $dbh->prepare('SELECT * FROM Interactions WHERE plate_name = "'.$idDistinct.'" AND plate_number = "'.$mod.'" AND user_id = "'.$userID[$j].'" AND project_id = "'.$projectID[$j].'"');
		$rv = $sth->execute();
		if(!$rv){ 
			handleError($dbh->errstr) 
		} else {
			while(my @results2 = $sth->fetchrow_array() ){
				$xc  = $results2[8];
				$yc  = $results2[7];
				$val = $results2[12];
				
				#print $i."\t".$xc."\t".$yc."\t".$val."\n";
				$matrix[$i][$xc][$yc] = $val;
				#print $i."\t".$xc."\t".$yc."\t".$matrix[$i][$xc][$yc]."\n";
			}
			#print "\n\n\n";
		}
		$i++;
	}
	####################################################################
	# the matrix is complete, convert it to a picture and smash it together, output it and then mvoe on
	#####################################################################
	
	####################################
	# Convert to picture
	########
	my $row_num = 32;
	my $col_num = 48;
	my $size_mult = 4;
	my $plate_num = 3;
	##############################
	# Make picture
	my $im = new GD::Image($col_num * $size_mult * $plate_num,$row_num * $size_mult);

    #######################################
	# Allocate some colors
	my $black = $im->colorAllocate(0,0,0);
	my $white = $im->colorAllocate(255,255,255);
	
	####################################################
	# Heatmap the image
	##############################
	
	my $red10   = $im->colorAllocate(250,  0,  0);
	my $red9   = $im->colorAllocate(225,  0,  0);
	my $red8   = $im->colorAllocate(200,  0,  0);
	my $red7   = $im->colorAllocate(175,  0,  0);
	my $red6   = $im->colorAllocate(150,  0,  0);
	my $red5   = $im->colorAllocate(125,  0,  0);
	my $red4   = $im->colorAllocate(100,  0,  0);
	my $red3   = $im->colorAllocate(75,  0,  0);
	my $red2   = $im->colorAllocate(50,  0,  0);
	my $red1   = $im->colorAllocate( 25,  0,  0);
	my $black0 = $im->colorAllocate(  0,  0,  0);
	my $green1 = $im->colorAllocate(  0, 25,  0);
	my $green2 = $im->colorAllocate(  0,50,  0);
	my $green3 = $im->colorAllocate(  0,75,  0);
	my $green4 = $im->colorAllocate(  0,100,  0);
	my $green5 = $im->colorAllocate(  0,125,  0);
	my $green6 = $im->colorAllocate(  0,150,  0);
	my $green7 = $im->colorAllocate(  0,175,  0);
	my $green8 = $im->colorAllocate(  0,200,  0);
	my $green9 = $im->colorAllocate(  0,225,  0);
	my $green10 = $im->colorAllocate(  0,250,  0);
	
	my $neg10    = -3.0;
	my $neg9    = -2.7;
	my $neg8    = -2.4;
	my $neg7    = -2.1;
	my $neg6    = -1.8;
	my $neg5    = -1.5;
	my $neg4    = -1.2;
	my $neg3    = -0.9;
	my $neg2    = -0.6;
	my $neg1    = -0.3;
	my $black_l = -0.0;
	my $black_h = 0.0;
	my $pos1  = 0.3;
	my $pos2  = 0.6;
	my $pos3  = 0.9;
	my $pos4  = 1.2; 
	my $pos5  = 1.5;
	my $pos6  = 1.8;
	my $pos7  = 2.1;
	my $pos8  = 2.4;
	my $pos9  = 2.7; 
	my $pos10  = 3.0;
	
	
	
	
	

	#############################################
	# Put together the picture
	
	for(my $plate = 0; $plate < $plate_num; $plate++) {
		for(my $col = 0;  $col < $col_num;  $col++) {
			for(my $row = 0; $row < $row_num; $row++) {
				$xc = ($plate * $col_num + $col) * $size_mult;
				$yc = $row * $size_mult;
				$val = $matrix[$plate][$col][$row];
				
				#print $plate."\t".$row."\t".$col."\t".$matrix[$plate][$col][$row]."\n";
				
				if($val < $neg10  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green10);
				}
				if($val >= $neg10   and $val < $neg9  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green9);
				}
				if($val >= $neg9   and $val < $neg8  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green9);
				}
				if($val >= $neg8   and $val < $neg7  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green8);
				}
				if($val >= $neg7   and $val < $neg6  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green7);
				}
				if($val >= $neg6   and $val < $neg5  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green6);
				}
				if($val >= $neg5   and $val < $neg4  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green5);
				}
				if($val >= $neg4   and $val < $neg3  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green4);
				}
				if($val >= $neg3   and $val < $neg2  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green3);
				}
	 			if($val >= $neg2   and $val < $neg1  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green2);
				}
				if($val >= $neg1   and $val < $black_l  ) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $green1);
				}
				if($val >= $black_l and $val < $black_h) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $black0);
				}
				if($val >= $black_h and $val < $pos1) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red1);
				}
				if($val >= $pos1 and $val < $pos2) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red2);
				}
				if($val >= $pos2 and $val < $pos3) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red3);
				}
				if($val >= $pos3 and $val < $pos4) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red4);
				}
				if($val >= $pos4 and $val < $pos5) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red5);
				}
				if($val >= $pos5 and $val < $pos6) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red6);
				}
				if($val >= $pos6 and $val < $pos7) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red7);
				}
				if($val >= $pos7 and $val < $pos8) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red8);
				}
				if($val >= $pos8 and $val < $pos9) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red9);
				}
				if($val >= $pos9 and $val < $pos10) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red9);
				}
				if($val >= $pos10) {
					$im->filledRectangle($xc, $yc, $xc+$size_mult, $yc+$size_mult, $red10);
				}
				########################################
				# Smash the pictures together
				#######
				# set pixel data
				#############

			}
		}
	}
	####################################
	# Output it
	###############
	# Open a file for writing 
	open(PICTURE, ">".$output_folder.$idDistinct."_".$userID[$j]."_".$projectID[$j]."_plateview.png") or die("Cannot open file for writing");
	
	# Make sure we are writing to a binary stream
	binmode PICTURE;
	
	# Convert the image to PNG and print it to the file PICTURE
	print PICTURE $im->png;
	close PICTURE;
	
	print "Picture Complete for $idDistinct\n";
	$j++;
}
print "checkpoint 7 reached: Complete\n";



# (insert query examples here...)
$dbh->disconnect();
