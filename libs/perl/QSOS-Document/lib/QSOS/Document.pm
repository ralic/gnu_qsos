# $Id: Document.pm,v 1.1 2006/02/15 17:41:13 goneri Exp $
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

package QSOS::Document;
use XML::Twig;
use Carp;
use open ':utf8';
use warnings;
use strict;

require Exporter;

use vars qw($VERSION @ISA @EXPORT @EXPORT_OK $PREFERRED_PARSER);

@ISA               = qw(Exporter);
@EXPORT            = qw(XMLin XMLout);
@EXPORT_OK         = qw(new load write setcomment getcomment setscore getscore write);
$VERSION           = '0.01';


sub _flatme {
  my ($self, $aref, $root, $deep) = @_;
  $self->_pushElem($root,$deep);
  $deep = 0 unless $deep;
  my @children = $root->children('element');
  foreach (@children) {
    $self->_flatme ($aref, $_, $deep+1);
  }
}


sub new {
  my $self;
  $self->{twig} = XML::Twig->new(
    pretty_print => 'indented',
    keep_atts_order => 1
  );    # create the twig

  $self->{tabular} = [];

  bless $self;
  return $self;
}

sub load {
  my ($self, $file) = @_;

  if (! -f $file) {
    carp "file doesn't exist";
    return;
  }
  $self->{twig}->parsefile($file);
  $self->{file} = $file;

  my $aref;
  my @root = $self->{twig}->root->children;

  shift @root;
  foreach (@root) {
    $self->_flatme ($aref, $_);
  }
}

sub _pushElem {

  my ($self, $elt, $deep) = @_;

  carp "element undef" unless ($elt);

  $deep = 0 unless $deep;

  my $h = {
    name => $elt->atts->{name},
    comment_ref => $elt->first_child('comment'),
    score_ref => $elt->first_child('score'),
    deep => $deep
  };

  if ($h->{comment_ref}) {
    $h->{comment_text} = $elt->first_child('comment')->text;
  }

  push @{$self->{tabular}}, $h;
}

sub setcomment {
  my ($self, $nbr, $comment) = @_;


  if (! defined $nbr) {
    croak ("nbr is not defined");
    return;
  }
  if (! defined $self->{tabular}->[$nbr]) {
    croak ("Can't setcomment in an undef value");
    return;
  } 

  my $comment_ref = $self->{tabular}->[$nbr]->{comment_ref};

  if ($comment_ref) {
    $comment_ref->set_text($comment);
  }
}

sub getcomment {
  my ($self, $nbr, $comment) = @_;

  if (! defined $nbr) {
    croak ("nbr is not defined");
    return;
  }

  my $comment_ref = $self->{tabular}->[$nbr]->{comment_ref};

  unless ($comment_ref) {
    return;
  }
#  print $comment_ref->text()."\n";
  $comment_ref->text();
}

sub setscore {
  my ($self, $nbr, $score) = @_;


  if (! defined $nbr) {
    croak ("nbr is not defined");
    return;
  }
  if (! defined $self->{tabular}->[$nbr]) {
    croak ("Can't setscore in an undef value");
    return;
  }

  my $score_ref = $self->{tabular}->[$nbr]->{score_ref};

  if ($score_ref) {
    $score_ref->set_text($score);
  }
}

sub getscore {
  my ($self, $nbr) = @_;

  if (! defined $nbr) {
    croak ("nbr is not defined");
    return;
  }

  my $score_ref = $self->{tabular}->[$nbr]->{score_ref};

  unless ($score_ref) {
    return;
  }
  $score_ref->text();
}




sub write {
  my ($self, $file) = @_;

  $file = $self->{file} unless ($file);

  my $aout =  $self->{twig}->sprint;

  carp "file is empty !" unless ($aout);

  open XMLOUT,">".$file or carp "can't pen $file $!";
  print XMLOUT $aout;
  close XMLOUT;
  
}
=debug func
sub dumpage {

  my $self = shift;
  open DUMPER, ">/tmp/dump_xml";
  print DUMPER Dumper($self->{twig});
  close DUMPER;
  open DUMPER, ">/tmp/dump_tabular";
  print DUMPER Dumper($self->{twig});
  close DUMPER;

}
=cut
