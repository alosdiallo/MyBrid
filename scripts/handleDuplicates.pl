#!/opt/csw/bin/perl
use strict;
#use warnings;
########################################################################
# Script: handleDuplicates.pl
# Description: This script will go into the database, mark the duplicates on lists then go into projects and set all duplicates to modified_call = negative aside from the one with the highest z_prime score.
# By: Justin Nelson
########################################################################

use DBI;
use GD;

#Check Args
if ($#ARGV != 0 ) {
	print "Argument are required to make sure you want to run this script.\n";
	print "usage: handleDuplicates.pl 1\n";
	exit;
}


my $databasename = "umass";
my $host = "localhost";
my $username = "umass_build";
my $pw = "3&2aswe!7";

my $dbh = DBI->connect('DBI:mysql:'.$databasename.';host='.$host, $username, $pw, { RaiseError => 1 }) or die "Could not connect to database: $DBI::errstr";

##########################################################################
## Grab the list names
my $sth = $dbh->prepare("SELECT DISTINCT list FROM TranscriptorFactor"); # sth = statement handle; common
my $rv = $sth->execute();

my @tf_list_names;

while(my @results = $sth->fetchrow_array() ){
	push(@tf_list_names, $results[0]);
}
print "@tf_list_names"."\n";
$sth->finish();

######################################################################
## Set all duplicates equal to 0
my $sth_update = $dbh->prepare("UPDATE TranscriptorFactor SET duplicates = 0"); # sth = statement handle; common
my $rv = $sth->execute();
$sth_update->finish();

#######################################################################
## Grab the orf names for each list
foreach my $list (@tf_list_names){
	print "LIST NAME: ".$list."\n";
	my $sth = $dbh->prepare("SELECT orf_name FROM TranscriptorFactor WHERE list = '".$list."'"); # sth = statement handle; common
	my $rv = $sth->execute();
	
	my @orf_list;
	while(my @results = $sth->fetchrow_array() ){
		push(@orf_list, $results[0]);
	}
	$sth->finish();
	
	print "@orf_list"."\n";
	#######################################################################
	## Check duplicates status
	print "########################LIST OF DUPLICATES FOUND##################";
	for(my $i = 0; $i <= $#orf_list; $i++){
		if(uc($orf_list[$i]) ne "BLANK"){
			for(my $j = $i + 1; $j <= $#orf_list; $j++){
				if($orf_list[$i] eq $orf_list[$j]){
					print $orf_list[$i]."\t";
					#################################################################
					## Duplicate found, set duplicates = 1 in mySql
					my $sth_update = $dbh->prepare("UPDATE TranscriptorFactor SET duplicates = 1 WHERE list = '".$list."' AND orf_name = '".$orf_list[$i]."'");
					my $rv = $sth->execute();
					$sth_update->finish();
				}
			}
		}
	} 
}

##########################################################################
## Grab project and user names now
my @users;
my @projects;
my @lists;
my $sth = $dbh->prepare("SELECT user_id, project_id, tf_list FROM Projects WHERE metausers = '0' AND tf_list IS NOT NULL"); # sth = statement handle; common
my $rv = $sth->execute();

while(my @results = $sth->fetchrow_array() ){
	push(@users, $results[0]);
	push(@projects, $results[1]);
	push(@lists, $results[2]);
}

print "@users"."\n";
print "@projects"."\n";
print "@lists"."\n";
$sth->finish();
#################################################################################################
## For each project grab a joined query and then loop through appropriately
##########################################################################
## Grab everything hash it out and compare
my $user_loop = 0;
my %plate_name = ();
my %array_coord = ();
my %orf_name = ();
my %value = ();
my %num_positives = ();

my $value_curr = 0;
#######################
## Flush duplicate calls, set to negative. NOTE: TAKES 20 MINUTES
print "###START SET DUPLICATE_CALL = MODIFIED_CALL\n";
$sth_update = $dbh->prepare("UPDATE Interactions SET duplicate_call = modified_call");
my $rv = $sth_update->execute();
$sth_update->finish();
print "###END SET DUPLICATE_CALL = MODIFIED_CALL\n";

############NOTENOTENOTENOTENOTENOTENOTENOTENOTENOTE#########
# Number of positive colonies needs to be properly added as the first check for which one to check.
#######NOTENOTENOTENOTENOTENOTENOTE##########


foreach my $user (@users){
	%plate_name = ();
	%array_coord = ();
	%orf_name = ();
	%value = ();
	%num_positives = ();
	my $sth = $dbh->prepare("SELECT Interactions.plate_name, Interactions.array_coord, TranscriptorFactor.orf_name, AVG(Interactions.z_prime) AS AVE_Z_PRIME, COUNT(*) AS NUM_POSITIVES, AVG(Interactions.z_score) AS AVE_Z_SCORE, Promoter.bait_name2 FROM Interactions, TranscriptorFactor, Promoter WHERE TranscriptorFactor.coordinate = Interactions.array_coord AND Interactions.plate_name = Promoter.bait_id AND Interactions.user_id = '".$users[$user_loop]."' AND Interactions.project_id = '".$projects[$user_loop]."' AND TranscriptorFactor.list = '".$lists[$user_loop]."' AND Interactions.modified_call = 'Positive' AND Promoter.user_id = '".$users[$user_loop]."' AND Promoter.project_id = '".$projects[$user_loop]."' GROUP BY Interactions.array_coord, Interactions.plate_name"); # sth = statement handle; common
	# [0] = plate_name [i]
	# [1] = array_coord [i]
	# [2] = orf_name [t]
	# [3] = AVE_Z_PRIME [i]
	# [4] = NUM POSITIVES [i]
	# [5] = AVE_Z_SCORE [i]
	# [6] = bait_name2 [p]
	#
	my $rv = $sth->execute();
	while(my @results = $sth->fetchrow_array() ){
		my $temp_name = $results[6].$results[2];
		##################################
		## Does the key exist, if so compare if not create
		if( exists $value{$temp_name} ){
#			print "#########################\n";
			print "##Collision Found ".$temp_name." ".$users[$user_loop]." ".$projects[$user_loop]." ".$lists[$user_loop]."\n";
#			print "KEY: ".$temp_name."\n";
			#######################################################
			## If the new result has more positives
			if($results[4] > $num_positives{$temp_name}){
#				print "NUM_POS_GREATER: RESULTS: ".$results[4]." OLD: ".$num_positives{$temp_name}."\n";
				my $sth_update = $dbh->prepare("UPDATE Interactions SET duplicate_call = 'Negative' WHERE user_id = '".$users[$user_loop]."' AND project_id = '".$projects[$user_loop]."' AND plate_name = '".$plate_name{$temp_name}."' AND array_coord = '".$array_coord{$temp_name}."'");
				my $rv = $sth_update->execute();
				$sth_update->finish();
				
				$plate_name{$temp_name} = $results[0];
				$array_coord{$temp_name} = $results[1];
				$orf_name{$temp_name} = $results[2];
				$value{$temp_name} = $results[3];
				$num_positives{$temp_name} = $results[4];
			###############################
			## If they are equal
			} elsif ($results[4] == $num_positives{$temp_name}){
#				print "NUM_POS_EQUAL: RESULTS: ".$results[4]." OLD: ".$num_positives{$temp_name}."\n";
				
				#######################################################
				## IF THE z_prime is unavailable
				if($results[3] eq ''){
					#######################################
					## Use z_score
					$value_curr = $results[5]
				} else {
					#######################################
					## Otherwise use z_prime
					$value_curr = $results[3];
				}
				#################
				## Is the newly discovered line greater than an existing line, if it is the existing line is Negative and the newly discovered line is put into the hash
				if($value_curr > $value{$temp_name}){
#					print "VALUE_GREATER: RESULTS: ".$value_curr." OLD: ".$value{$temp_name}."\n";
					my $sth_update = $dbh->prepare("UPDATE Interactions SET duplicate_call = 'Negative' WHERE user_id = '".$users[$user_loop]."' AND project_id = '".$projects[$user_loop]."' AND plate_name = '".$plate_name{$temp_name}."' AND array_coord = '".$array_coord{$temp_name}."'");
					my $rv = $sth_update->execute();
					$sth_update->finish();
					
					$plate_name{$temp_name} = $results[0];
					$array_coord{$temp_name} = $results[1];
					$orf_name{$temp_name} = $results[2];
					$value{$temp_name} = $results[3];
					$num_positives{$temp_name} = $results[4];
					
					
				
				#############################
				## Otherwise the newly discovered line should be negative
				} else {
#					print "VALUE_LESSER: RESULTS: ".$value_curr." OLD: ".$value{$temp_name}."\n";
					my $sth_update = $dbh->prepare("UPDATE Interactions SET duplicate_call = 'Negative' WHERE user_id = '".$users[$user_loop]."' AND project_id = '".$projects[$user_loop]."' AND plate_name = '".$results[0]."' AND array_coord = '".$results[1]."'");
					my $rv = $sth_update->execute();
					$sth_update->finish();
				}
			###################
			## If the number positives is less
			} else {
#				print "NUM_POS_LESSER: RESULTS: ".$results[4]." OLD: ".$num_positives{$temp_name}."\n";
				my $sth_update = $dbh->prepare("UPDATE Interactions SET duplicate_call = 'Negative' WHERE user_id = '".$users[$user_loop]."' AND project_id = '".$projects[$user_loop]."' AND plate_name = '".$results[0]."' AND array_coord = '".$results[1]."'");
				my $rv = $sth_update->execute();
				$sth_update->finish();
			}
			#print "############END#####\n";
		################
		## The key doesn't exist, so create it
		} else {
			#print "###KEY NOT FOUND#####\n";
			$plate_name{$temp_name} = $results[0];
			$array_coord{$temp_name} = $results[1];
			$orf_name{$temp_name} = $results[2];
			$value{$temp_name} = $results[3];
			$num_positives{$temp_name} = $results[4];
		}
		
		#####
		# This is the end for the loop through the mysql
	}
	$user_loop++;
}

# Ok so I have to hash on plate_name + orf name, if an orf is found twice on a plate then it can be compared

print "@users"."\n";
print "@projects"."\n";
print "@lists"."\n";
$sth->finish();