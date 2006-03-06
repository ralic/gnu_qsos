#!/usr/bin/perl -w
# $Id: 0_check.t,v 1.1 2006/03/06 09:39:30 goneri Exp $
#
#  Copyright (C) 2006 Atos Origin 
#
#  Author: Gonéri Le Bouder <goneri.lebouder@atosorigin.com>
#
#  This program is free software; you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation; either version 2 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program; if not, write to the Free Software
#  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
#
#
use Test;
BEGIN { plan tests => 28 }

use QSOS::Document;
use Data::Dumper;

$qsos = new QSOS::Document;

print "load() ";
$qsos->load("qsos.xml");
ok(defined $qsos);

print "gestauthors() ";
my $authors = $qsos->getauthors();
ok(@$authors);

print "addauthor() ";
$qsos->addauthor("toto","foo\@bar.org");
ok(@$authors);

print "write() ";
$qsos->write ("qsos2.xml");
ok(-f "qsos2.xml");

undef $qsos;
$qsos = new QSOS::Document;

print "load() new file ";
$qsos->load("qsos2.xml");
ok(defined ($qsos) and ($qsos));

print "check author insertion ";
$authors = $qsos->getauthors();
my $last = pop @$authors;
ok($last->{email} eq 'foo@bar.org');

print "delauthor() ";
$qsos->delauthor(2);
$authors = $qsos->getauthors();
$last = pop @$authors;
ok($last->{email} ne 'foo@bar.org');

print "getappname() ";
$appname = $qsos->getappname();
ok($appname eq 'demo');

print "setappname('test') ";
$qsos->setappname('test');
$appname = $qsos->getappname();
ok($appname eq 'test');

print "getlanguage() ";
$language = $qsos->getlanguage();
ok($language eq 'en');

print "setlanguage('fr') ";
$qsos->setlanguage('fr');
$language = $qsos->getlanguage();
ok($language eq 'fr');

print "getrelease() ";
$release = $qsos->getrelease();
ok($release eq '1');

print "setrelease('2') ";
$qsos->setrelease('2');
$release = $qsos->getrelease();
ok($release eq '2');

print "getlicenselist() ";
$licenselist = $qsos->getlicenselist();
ok(@$licenselist);

print "getlicense() ";
$license = $qsos->getlicense();
ok($license eq '1');

print "setlicense(2) ";
$qsos->setlicense(2);
$license = $qsos->getlicense();
ok($license eq '2');

print "geturl() ";
$url = $qsos->geturl();
ok($url eq 'http://www.qsos.org');

print "seturl(http://qsos.org) ";
$qsos->seturl('http://qsos.org');
$url = $qsos->geturl();
ok($url eq 'http://qsos.org');

print "getdesc() ";
$desc = $qsos->getdesc();
ok($desc eq 'a description');

print "setdesc(something) ";
$qsos->setdesc('something');
$desc = $qsos->getdesc();
ok($desc eq 'something');

print "getdemourl() ";
$demourl = $qsos->getdemourl();
ok($demourl eq 'http://demo.site.org');

print "setdemourl(http://demo.qsos.org) ";
$qsos->setdemourl('http://demo.qsos.org');
$demo = $qsos->getdemourl();
ok($demo eq 'http://demo.qsos.org');

print "getqsosformat() ";
$qsosformat = $qsos->getqsosformat();
ok($qsosformat eq '1');

print "setqsosformat(1.1) ";
$qsos->setqsosformat('1.1');
$qsosformat = $qsos->getqsosformat();
ok($qsosformat eq '1.1');

print "getqsosspecificformat() ";
$qsosspecificformat = $qsos->getqsosspecificformat();
ok($qsosspecificformat eq '1');

print "setqsosspecificformat(1.1) ";
$qsos->setqsosspecificformat('1.1');
$qsosspecificformat = $qsos->getqsosspecificformat();
ok($qsosspecificformat eq '1.1');

print "getqsosappfamily() ";
$qsosappfamily = $qsos->getqsosappfamily();
ok($qsosappfamily eq 'groupware');

print "setqsosappfamily(word processor) ";
$qsos->setqsosappfamily('word processor');
$qsosappfamily = $qsos->getqsosappfamily();
ok($qsosappfamily eq 'word processor');


