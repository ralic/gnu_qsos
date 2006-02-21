#!/usr/bin/perl -w
# $Id: 0_check.pl,v 1.1 2006/02/21 11:10:10 goneri Exp $
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
BEGIN { plan tests => 2 }

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
