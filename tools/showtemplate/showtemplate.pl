#!/usr/bin/perl -w


use strict;


sub _usage {
  print "usage createtemplate.pl sheet.qsos > template.qsos\n";
}


my $file = shift;

if (!defined ($file)) {
_usage();
exit 1;
}

open FILE,"<".$file or die "can't open $file: $!";
foreach(<FILE>) {
  s!(<comment>).+(<\/comment>)!$1$2!g;
  s!(<score>).+(</score>)!$1$2!g;
  chomp if (s!^[\ ]{0,}$!!);
  print;
}


close FILE or die;

