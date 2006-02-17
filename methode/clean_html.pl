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
# $Id: clean_html.pl,v 1.1 2006/02/17 10:25:29 goneri Exp $

use strict;
use warnings;

my $imagesdir = "methode";
my $tmpfile = "/tmp/tmp_f$$";
my $file = shift @ARGV;

unless ($file) {
  die "J'attend le nom du fichier en param\n";
}
open FILE,"<$file" or die "Can't open $file $!";

open TMP,">".$tmpfile or die "Can't open /tmp/f_tmp $!";

# je vire les retours charots qui me gene
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
  s/<HR>//;
  #lien vers les tags correctes
  s/qsos.html/methode.php/;
  s/align="\w*"//;
  s/border="\w*"//;

  #image dans le bon dossier
  s!\./!!;
  #$_ =~ s!src="\.!prout="!;
  s/\ src="/src="$imagesdir\//;
  print if $inbody;

}
close FILE;
