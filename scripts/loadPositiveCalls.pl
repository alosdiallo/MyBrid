#!/opt/csw/bin/perl
use strict;
#use warnings;
########################################################################
# Script: loadPositiveCalls.pl
# Description: Takes a list of bait_ids and array_coords and changes all of the calls in the interaction table to positive. Quick and dirty.
# By: Justin Nelson
########################################################################



use DBI;
use GD;




my $databasename = "umass";
my $host = "localhost";
my $username = "umass_build";
my $pw = "3&2aswe!7";

print "checkpoint 1 reached: INITIALIZED\n";

my $dbh = DBI->connect('DBI:mysql:'.$databasename.';host='.$host, $username, $pw, { RaiseError => 1 }) or die "Could not connect to database: $DBI::errstr";

#Check Args
if ($#ARGV != 2 ) {
	print "usage: loadPositiveCalls <file_name> <user_name> <project_name>\n";
	exit;
}

#Grab Args
my $file_name = $ARGV[0];
my $user      = $ARGV[1];
my $project   = $ARGV[2];

#SET UP DATA
my $sth = $dbh->prepare('UPDATE Interactions SET modified_call = "Negative" WHERE user_id = "'.$user.'" AND project_id = "'.$project.'"'); # sth = statement handle; common
my $rv = $sth->execute();
$sth->finish();
#Grab the plate number and row column and store it using query3
open(INPUTFILE, "<", $file_name) or die ("Cannot open file $file_name");


#READ IN
while(my $line = <INPUTFILE>){
	chomp($line);
	$line =~ s/\r//g;
	
	# split the line into an array with the relavant lines
	my @values = split('\t', $line);
	my $bait_id = $values[0];
	my $array_coord = $values[1];
	
	my $sth = $dbh->prepare('UPDATE Interactions SET modified_call = "Positive" WHERE user_id = "'.$user.'" AND project_id = "'.$project.'" AND plate_name = "'.$bait_id.'" AND array_coord = "'.$array_coord.'"'); # sth = statement handle; common
	my $rv = $sth->execute();
	$sth->finish();
}
# (insert query examples here...)
$dbh->disconnect();
