# $Id: Document.pm,v 1.11 2006/03/24 15:15:52 goneri Exp $
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
use Data::Dumper;
use open ':utf8';
use warnings;
use strict;

require Exporter;

use vars qw($VERSION @ISA @EXPORT @EXPORT_OK $PREFERRED_PARSER);

@ISA               = qw(Exporter);
@EXPORT            = qw(XMLin XMLout);
@EXPORT_OK         = qw(new load write getkeydesc setkeycomment getkeycomment setkeyscore getkeyscore write getauthors addauthor delauthor getappname setappname getlanguage setlanguage getrelease setrelease getlicenselist getlicenseid setlicenseid getlicensedesc setlicensedesc geturl seturl getdesc setdesc getdemourl setdemourl getqsosformat setqsosformat getqsosspecificformat setqsosspecificformat getqsosappfamily setqsosappfamily);
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
  $self->{authors} = [];

  bless $self;
  return $self;
}

sub load {
  my ($self, $file) = @_;

  if (! -f $file) {
    carp "file doesn't exist";
    return;
  }
  unless ($self->{twig}->safe_parsefile($file)) {
    print "Can't load file `$file'\n";
    print $@;
    return;
  }
  return unless ($file);
  $self->{file} = $file;

  my $aref;
  my @root = $self->{twig}->root->children;

  shift @root;
  foreach (@root) {
    $self->_flatme ($aref, $_);
  }
  1;
}

sub _pushElem {

  my ($self, $elt, $deep) = @_;

  carp "element undef" unless ($elt);

  $deep = 0 unless $deep;

  my $h = {
    name => $elt->atts->{name},
    comment_ref => $elt->first_child('comment'),
    desc_ref => $elt->first_child('desc'),
    score_ref => $elt->first_child('score'),
    deep => $deep
  };

  
  push @{$self->{tabular}}, $h;
}


sub getkeydesc {
  my ($self, $nbr) = @_;

  if (! defined $nbr) {
    croak ("nbr is not defined");
    return;
  }

  my $comment_ref = $self->{tabular}->[$nbr]->{desc_ref};

  unless ($comment_ref) {
    return;
  }
  $comment_ref->text();
}



sub setkeycomment {
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

sub getkeycomment {
  my ($self, $nbr) = @_;

  if (! defined $nbr) {
    croak ("nbr is not defined");
    return;
  }

  my $comment_ref = $self->{tabular}->[$nbr]->{comment_ref};

  unless ($comment_ref) {
    return;
  }
  $comment_ref->text();
}

sub setkeyscore {
  my ($self, $nbr, $score) = @_;


  if (! defined $nbr) {
    croak ("nbr is not defined");
    return;
  }
  if (! defined $self->{tabular}->[$nbr]) {
    croak ("Can't setscore in an undef value");
    return;
  }

  $score = '' if ($score !~ /[012]/);

  my $score_ref = $self->{tabular}->[$nbr]->{score_ref};

  if ($score_ref) {
    $score_ref->set_text($score);
  }
}

sub getkeyscore {
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


sub getauthors {
  my $self = shift;

  return $self->{authors} if (@{$self->{authors}});
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  my $authors = $header->first_child('authors');
  my @author = $authors->children('author');

  foreach (@author) {
    my $name = $_->first_child('name');
    my $email = $_->first_child('email');
    push @{$self->{authors}}, {
      author_ref => $_,
      name => defined ($name)?$name->text():"",
      email => defined ($email)?$email->text():""
    };
  }
  
  $self->{authors};
}

sub addauthor {
  my ($self, $name, $email) = @_;
 
  return unless (defined ($name));
  $email = "" unless defined ($email);

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  my $authors = $header->first_child('authors');

  $authors->insert_new_elt ('last_child', 'author');
  $authors->last_child()->insert_new_elt ('name' , $name);
  $authors->last_child()->insert_new_elt ('email' , $email);
  
  $self->{authors} = [];
}

sub delauthor {
  my ($self, $id) = @_;

  return unless (defined $id);
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  if ($header->first_child('authors')->children_count()<$id) {
    carp "id $id doesn't exist";
    return;
  }

  my @authors = $header->first_child('authors')->children();
  $authors[$id]->delete;
  $self->{authors} = [];

}

sub getappname {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('appname')->text();

}

sub setappname {
  my ($self, $appname) = @_;

  $appname = "" unless defined $appname;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('appname')->set_text($appname);

}

sub getlanguage {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('language')->text();

}

sub setlanguage {
  my ($self, $language) = @_;

  $language = "" unless defined $language;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('language')->set_text($language);

}

sub getrelease {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('release')->text();

}

sub setrelease {
  my ($self, $release) = @_;

  $release = "" unless defined $release;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('release')->set_text($release);

}

sub getlicenselist {
  # TODO read the list from the license.xml file  
  # cf: http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/license.xml?root=qsos&view=markup 
[ "Affero GPL", "AFPL (Aladdin)", "APSL (Apple)", "Copyback License", "DFSG approved", "Eclipse Public License", "EFL (Eiffel)", "Free for Eductional Use", "Free for Hum Use", "Free for non-commercial use", "Free but Restricted", "Freely Distribuable", "Freeware", "NPL (Netscape)", "NOKOS (Nokia)", "OSI Approved", "Proprietary", "Proprietary with trial", "Proprietary with source", "Public Domain", "Shareware", "SUN Binary Code License", "The Apache License", "The Apache License 2.0", "CeCILL License (INRIA)", "Artistic License", "LPPL (Latex)", "Open Content License", "Voxel Public License", "WTFPL", "Zope Public License", "GNU GPL", "GNU LGPL", "BSD", "GNU approved License", "GNU FDL" ];
}

sub setlicenseid {
  my ($self, $licenseid) = @_;

  $licenseid = "" unless defined $licenseid;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('licenseid')->set_text($licenseid);

}

sub getlicenseid {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('licenseid')->text();

}

sub setlicensedesc {
  my ($self, $licensedesc) = @_;

  $licensedesc = "" unless defined $licensedesc;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('licensedesc')->set_text($licensedesc);

}

sub getlicensedesc {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('licensedesc')->text();

}



sub seturl {
  my ($self, $url) = @_;

  $url = "" unless defined $url;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('url')->set_text($url);

}

sub geturl {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('url')->text();

}

sub setdesc {
  my ($self, $desc) = @_;

  $desc = "" unless defined $desc;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('desc')->set_text($desc);

}

sub getdesc {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('desc')->text();

}

sub setdemourl {
  my ($self, $demo) = @_;

  $demo = "" unless defined $demo;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('demourl')->set_text($demo);

}

sub getdemourl {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('demourl')->text();

}


sub setqsosformat {
  my ($self, $qsosformat) = @_;

  $qsosformat = "" unless defined $qsosformat;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('qsosformat')->set_text($qsosformat);

}

sub getqsosformat {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('qsosformat')->text();

}


sub setqsosspecificformat {
  my ($self, $qsosspecificformat) = @_;

  $qsosspecificformat = "" unless defined $qsosspecificformat;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('qsosspecificformat')->set_text($qsosspecificformat);

}

sub getqsosspecificformat {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('qsosspecificformat')->text();

}


sub setqsosappfamily {
  my ($self, $qsosappfamily) = @_;

  $qsosappfamily = "" unless defined $qsosappfamily;
  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('qsosappfamily')->set_text($qsosappfamily);

}

sub getqsosappfamily {
  my $self = shift;

  my @root = $self->{twig}->root->children;
  my $header = shift @root;
  $header->first_child('qsosappfamily')->text();

}
=head1 NAME

QSOS::Document - QSOS file access

=head1 SYNOPSIS

TODO

=head1 DESCRIPTION

This librairie give an easy way to edit QSOS file.

=head1 SEE ALSO

http://www.qsos.org

=head1 AUTHORS

Gonéri Le Bouder <goneri.lebouder@atosorigin.org>

=cut
