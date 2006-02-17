use Qt::attributes qw( aboutform );
use QSOS::Document;
use Data::Dumper;
use Qt::debug;
use QSOS::QtEditor::aboutForm;
use Carp;
use strict;
use warnings;

sub saveCurrentValue
{

  my $num = SUPER->{current_section_nbr};
  SUPER->this->{qsosxml}->setcomment($num, commentBox->text);

}


sub loadSection
{
  my ($num) = @_;

  print "load section n° $num\n";
  # Saving current commentBox :
  if (defined SUPER->{current_section_nbr}) {
    saveCurrentValue();
  }
  my $score = SUPER->this->{qsosxml}->getscore($num);
  if (defined $score) {
    $score = 3 if ($score !~ /[123]/);
    scoreBox->setCurrentItem($score);
    scoreBox->setEnabled($score);
  } else {
    scoreBox->setEnabled(0);
    scoreBox->setCurrentItem(3);
  }

  my $comment = SUPER->this->{qsosxml}->getcomment($num);
  my $desc = SUPER->this->{qsosxml}->getdesc($num);
  $desc = "no description" unless (defined $desc);
  #descriptionBox->setText('<p align="center"><b>'.$desc.'</b></p>');
  descriptionBox->setText($desc);
  if (defined ($comment)) {
    commentBox->setText($comment);
    commentBox->setReadOnly(0);
    commentBox->setEnabled(1);
  } else {
    commentBox->setText('');
    commentBox->setReadOnly(1);
    commentBox->setEnabled(0);
  }
  SUPER->{current_section_nbr} = $num;
}

sub fileNew
{
  print "Qsosform->fileNew(): Not implemented yet.\n";
}
sub fileOpen
{

  my $file = Qt::FileDialog::getOpenFileName(
    undef,
    "QSOS file (*.xml)",
    this,
    "open file dialog",
    "Choose a file" );
  unless (-f $file) {
    carp "file `$file' doesn't exist";
    return;
  }
  SUPER->{file} = $file;

  print "opening file $file\n";

  SUPER->this->{qsosxml} = new QSOS::Document; 
  SUPER->this->{qsosxml}->load($file);
  ### listbox initialisation
  listBox->clear();
#    print Dumper(SUPER->this->{qsosxml}->{struct});
  foreach (@{SUPER->this->{qsosxml}->{tabular}}) {
  print $_->{name};
  my $v;
  $v .= ' ' foreach (0..$_->{deep});
  $v .= $_->{name};
  listBox->insertItem($v);
  loadSection(0);
  listBox->setEnabled(1);
}
}

sub fileSave
{
  saveCurrentValue();
  if (defined SUPER->{file}) {
    print "sauvegarde du fichier :\n";
    SUPER->this->{qsosxml}->write(SUPER->{file});
  }
}

sub fileSaveAs
{
  my $file = Qt::FileDialog::getSaveFileName( undef,"QSOS file (*.xml)", this);

  return unless $file;

  saveCurrentValue();
  SUPER->this->{qsosxml}->write($file);
}

sub filePrint
{
  print "Qsosform->filePrint(): Not implemented yet.\n";
}

sub fileExit
{
  print "Qsosform->fileExit(): Not implemented yet.\n";
}


sub helpAbout
{

  #aboutform = aboutForm(this,"aboutForm");
  aboutform = QSOS::QtEditor::aboutForm(this);
#  aboutform = aboutForm(this);
  aboutform->show(1);
#print Dumper(SUPER->SUPER);
  print "Qsosform->helpAbout(): Not implemented yet.\n";
#$about->show();
  print "Qsosform->helpAbout(): Not implemented yet.\n";
}

sub itemChanged
{
  my $item = shift;
  print "Qsosform->itemChanged(): Not implemented yet.\n";

  loadSection($item);
}

sub scoreChanged
{
  print Dumper(@_);

  my $score = shift;
  my $num = SUPER->{current_section_nbr};
 
  return unless (defined ($score) and ($score));
  return unless (defined ($num) and ($num));
  SUPER->this->{qsosxml}->setscore($num, $score);

}



sub commentChanged
{

#  print "Qsosform->commentChanged(): Not implemented yet.\n";

}

1;
