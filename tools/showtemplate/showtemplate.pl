#!/usr/bin/perl -w


use strict;
use Getopt::Long;

sub _help {
  print "usage createtemplate.pl -f sheet.qsos > template.qsos\n";
}
my ($help, $file, $global);

GetOptions (
  'file=s' => \$file,
  'global' => \$global,
  'help' => \$help,
);

if (!($file && (-f $file))) {
  _help();
  exit 1;
}

open FILE,"<".$file or die "can't open $file: $!";
foreach(<FILE>) {
  s!(<comment>).+(<\/comment>)!$1$2!g;
  s!(<score>).+(</score>)!$1$2!g;
  chomp if (s!^[\ ]{0,}$!!);
  print;
  if ($global && /<\/section>/) {
    print "<document>\n";
    last;
  }
}


close FILE or die;

