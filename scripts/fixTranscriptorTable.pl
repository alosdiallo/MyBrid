#!/opt/csw/bin/perl
use strict;
#use warnings;
########################################################################
# Script: fixTranscriptorTable
# Description: Quick and Dirty; Convert Array coords to X,Y
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



my $sth = $dbh->prepare("SELECT DISTINCT array_coord, plate_number, MIN(x_coord) as x, MIN(y_coord) as y FROM Interactions GROUP BY array_coord, plate_number"); # sth = statement handle; common
my $rv = $sth->execute();

my @arrayCoords;
my @plate_numbers;
my @x_coords;
my @y_coords;


print "checkpoint 2 reached: Querying array coords\n";
if(!$rv){

} else {
	while(my @results = $sth->fetchrow_array() ){
		print "Array Coord " . $results[0] . "\n";
		print "Plate Numbers " . $results[0] . "\n";
		print "x " . $results[1] . "\n";
		print "y " . $results[2] . "\n";
		push(@arrayCoords, $results[0]);
		push(@plate_numbers, $results[1]);
		push(@x_coords, $results[2]);
		push(@y_coords, $results[3]);
	}
}
$sth->finish();
print "checkpoint 3 reached: DONE Querying array coords\n";

my $i = 0;
foreach my $array (@arrayCoords){
	my $sth = $dbh->prepare("UPDATE TranscriptorFactor SET x_coord = '".$x_coords[$i]."', y_coord = '".$y_coords[$i]."', plate_number = '".$plate_numbers[$i]."' WHERE coordinate = '".$array."'");
	my $rv = $sth->execute();
	$sth->finish();
	$i++;
}






$dbh->disconnect();
