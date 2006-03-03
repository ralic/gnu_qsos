# $Id: Makefile.PL,v 1.4 2006/03/03 09:45:39 goneri Exp $

use ExtUtils::MakeMaker;

BEGIN {
  if($] < 5.006) {
    warn
      qq(This version of XML::QSOS requires Perl version 5.6 or better.\n) .
      qq(You might prefer to install an older version of XML::QSOS\n\n);
  }
}

require 5.006;

my $make_params = {
    'NAME'          => 'QSOS::Document',
    'VERSION_FROM'  => 'lib/QSOS/Document.pm',
    'DISTNAME'      => 'XML-QSOS',
    'PREREQ_PM'     => {
                         Test::Simple          => 0.41,
                         XML::Twig             => 3,
                       },
    'dist'          => { COMPRESS => 'gzip --best', SUFFIX => 'gz' },
    'AUTHOR'        => 'Goneri Le Bouder <goneri@rulezlan.org>',
    'ABSTRACT_FROM' => 'lib/QSOS/Document.pm',
};

print "Checking installed modules ...\n";

if ( eval { require XML::Twig } && ! $@ ) {
    print "XML::Twig is installed, it will be used by the test suite\n";
}
else {
    die "You don't have XML::Twig installed!\n";
}

eval { require Storable };
if($@) {
  print "Storable is not installed ... caching functions will not be available\n";
}


WriteMakefile(%$make_params);
