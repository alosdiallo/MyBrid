#!/usr/bin/perl

use Env qw(PATH);
use strict;
use warnings;
use POSIX;
use Data::Dumper;
# Created by Alos Diallo UMASS Medical School
my $location = "/heap/UMassProject/goldFile/crop_file.txt";
open CROPNEW,">>"."$location" or die $!;	
 
my $crop_name = 0;
my $width = 0;
my $hight = 0;
my $X_Off_Set = 0;
my $Y_Off_Set = 0;
$crop_name = $ARGV[0];
$width = $ARGV[1];
$hight = $ARGV[2];
$X_Off_Set = $ARGV[3];
$Y_Off_Set = $ARGV[4];

my $crop_settings = $width."x".$hight."+".$X_Off_Set."+".$Y_Off_Set;

print CROPNEW "$crop_name\t$crop_settings\n";

my $location_m = "/heap/UMassProject/goldFile/";
close(CROPNEW);
chdir $location_m;
system("more crop_file.txt");

print "done\n";