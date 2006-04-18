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

my $inheader;
open FILE,"<".$file or die "can't open $file: $!";
foreach(<FILE>) {

  $inheader = 1 if (/<header>/);
  $inheader = undef if (/<\/header>/);
  s!(<creation>).+(<\/creation>)!$1$2!g;
  s!(<appname>).+(<\/appname>)!$1$2!g;
  s!(<licensedesc>).+(<\/licensedesc>)!$1$2!g;
  s!(<url>).+(<\/url>)!$1$2!g;
  s!(<email>).+(<\/email>)!$1$2!g;
  s!(<name>).+(<\/name>)!$1$2!g;
  s!(<qsosappfamily>).+(<\/qsosappfamily>)!$1$2!g;
  s!(<demourl>).+(<\/demourl>)!$1$2!g;
  if ($inheader) {
    s!(<desc>).+(<\/desc>)!$1$2!g;
  }

  s!(<comment>).+(<\/comment>)!$1$2!g;
  s!(<score>).+(</score>)!$1$2!g;
  s!<\!--.+-->!!g;
  s!>[\ \t]+$!>!g;
  chomp if (s!^[\t\ ]{0,}$!!);
  print;
  if ($global && /<\/section>/) {
    print "<document>\n";
    last;
  }
}


close FILE or die;

