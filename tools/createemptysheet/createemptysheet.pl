#!/usr/bin/perl -w

use strict;

use Getopt::Long;

my $includedir = "../../sheet/include/";
my $qtpl;

GetOptions (
  'include=s' => \$includedir,
  'qtpl=s' => \$qtpl,
);

sub usage {
  my $msg = shift;
  print STDERR $msg."\n" if $msg;

  print STDERR "usage:\n";
  print STDERR "  createemptysheet --include=/includepath --qtpl qsos-template.qtpl:\n";
  print STDERR " Default include directory is $includedir\n";
  exit 1;
}

usage("Missing argument") if ! (defined $includedir && defined $qtpl);
usage("Can't find include directory") if ! -d $includedir;
usage("Can't find template file") if ! -f $qtpl;

my @buff;
open QTPL,"<$qtpl" or die "Failed to open $qtpl: $?";
@buff = <QTPL>;
close QTPL;

while (my $line = shift @buff) {
  if ($line =~ /<include\W+section="([-\w]+)"\W*(|\/)>/) { # this is an include
#    print STDERR "Including $1\n";
    open INCLUDE, "<$includedir/$1.qin" or die "Failed to open includefile
    $1.qin";
    unshift @buff, <INCLUDE>;
    close INCLUDE;
  } else {
    print $line;
  }
}
