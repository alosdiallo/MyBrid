#!/opt/csw/bin/perl
use lib "/opt/csw/lib/perl/csw";

use strict;
use warnings;
use POSIX;
use Data::Dumper;
my $location_m = 0;
$location_m = $ARGV[0];
my $one = "1to4";
my $five = "5to8";
my $nine = "9to12";
my $back = "..";
my $dataF = "dataFiles";
my $new_loc = $location_m . "dataFiles";
my $long_loc = $location_m . "long9-12.txt";
my $final_loc = $location_m . "final.output_p_r.txt";
my $one_loc = $location_m . "1to4";
my $two_loc = $location_m . "5to8";
my $three_loc = $location_m . "9to12";
my $ignore_list = $ARGV[1]; 

my $end_file = 0;
$end_file = $new_loc."/output.list.txt";
if (-e $end_file) {
	system("rm -rf $end_file");
}
else{
	print "The file $end_file does not exist\n";
}

print "\tCreating new zscore files...\n";
chdir $location_m;
chdir $one;
system ("pwd");

system ("/heap/UMassProject/scripts/1to4/processALL.pl $location_m");
system ("/heap/UMassProject/scripts/1to4/processALL2.pl $location_m");
system ("/heap/UMassProject/scripts/1to4/program4.pl long9-12.txt $location_m");
system ("/heap/UMassProject/scripts/1to4/program5.pl final.output_p_r.txt $location_m");
system ("/heap/UMassProject/scripts/1to4/processALL_norm.pl $location_m");
system ("cp $one_loc/*.zscore.txt $new_loc/");
system ("cp $one_loc/*.norm_values.txt $new_loc/");
chdir $back; 
chdir $five;
system ("/heap/UMassProject/scripts/5to8/processALL.pl $location_m/");
system ("/heap/UMassProject/scripts/5to8/processALL2.pl $location_m/");
system ("/heap/UMassProject/scripts/5to8/program4.pl long9-12.txt $location_m");
system ("/heap/UMassProject/scripts/5to8/program5.pl final.output_p_r.txt $location_m");
system ("/heap/UMassProject/scripts/5to8/processALL_norm.pl $location_m/");
system ("cp $two_loc/*.zscore.txt $new_loc/");
system ("cp $two_loc/*.norm_values.txt $new_loc/");
chdir $back;
chdir $nine;
system ("/heap/UMassProject/scripts/9to12/processALL.pl $location_m");
system ("/heap/UMassProject/scripts/9to12/processALL2.pl $location_m");
system ("/heap/UMassProject/scripts/9to12/program4.pl long9-12.txt $location_m");
system ("/heap/UMassProject/scripts/9to12/program5.pl final.output_p_r.txt $location_m");
system ("/heap/UMassProject/scripts/9to12/processALL_norm.pl $location_m");
system ("cp $three_loc/*.zscore.txt $new_loc/");
system ("cp $three_loc/*.norm_values.txt $new_loc/");
print "\tDone\n\n";

print "\tGenerating stats\n";
chdir $back;



system ("pwd");
system ("/heap/UMassProject/scripts/processALL.pl $new_loc $ignore_list");
print "/heap/UMassProject/scripts/processALL.pl \t$new_loc \t$ignore_list";
print "\tDone\n\n";

 
