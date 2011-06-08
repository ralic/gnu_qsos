#!/usr/bin/perl -W
#Copyright (c) 2004 2005 2006 Atos Origin
#Permission is granted to copy, distribute and/or modify this
#document
#under the terms of the GNU Free Documentation License,
#      Version 1.2
#      or any later version published by the Free Software
#      Foundation;
#      with no Invariant Sections, no Front-Cover
#      Texts, and no Back-Cover
#      Texts.  A copy of the license is
#      included in the section entitled "GNU
#      Free Documentation License".
#
# Perl script used to clean the html code from latex2html
# Usage : ./clean_html.pl qsos.html > /tmp/new.html
# Gonéri Le Bouder (Atos Origin)
# $Id: clean_html.pl,v 1.3 2006/03/28 20:10:26 goneri Exp $

use strict;
use warnings;

my $file = shift @ARGV;
my $lang = shift @ARGV;

unless ($file) {
  die "first param is a file name\n";
}
unless ($lang) {
  die "the second param is the language (en,fr,...)";
}

my $imagesdir = "methode/$lang";
my $tmpfile = "/tmp/tmp_f$$";


open FILE,"<$file" or die "Can't open $file $!";

open TMP,">".$tmpfile or die "Can't open /tmp/f_tmp $!";

# i remove some stupid \r 
foreach(<FILE>) {
  s/src=\n.*$/src=/;
  print TMP;
}
close FILE;
close TMP;


open FILE,"<".$tmpfile or die "Can't open $file $!";
my $inbody;
foreach (<FILE>) {
  if (/<body>/) {
    $inbody = 1;
    next;
  }

  if ($inbody) {
    $inbody = undef if (/<address>/);
  }
  #lien vers les tags correctes
  s/qsos.html/methode.php/;
  s/align="\w*"//;
  s/border="\w*"//;

  #image dans le bon dossier
  s!\./!!;
  #$_ =~ s!src="\.!prout="!;
  s/\ src="/src="$imagesdir\//;
  s!<hr\ />!!g;
  s!<br\ />!!g;
  print if $inbody;


}
close FILE;
