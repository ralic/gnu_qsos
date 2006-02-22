use Qt::attributes qw( aboutform propertyform );
use QSOS::Document;
use Data::Dumper;
use Qt::debug;
use QSOS::QtEditor::Aboutform;
use QSOS::QtEditor::Propertyform;
use Carp;
use strict;
use warnings;

sub saveCurrentValue
{

  my $num = SUPER->{current_section_nbr};
  SUPER->this->{qsosxml}->setkeycomment($num, commentBox->text);

}


sub loadSection
{
  my ($num) = @_;

  print "load section n° $num\n";
  # Saving current commentBox :
  if (defined SUPER->{current_section_nbr}) {
    saveCurrentValue();
  }
  my $score = SUPER->this->{qsosxml}->getkeyscore($num);
  if (defined $score) {
    $score = 3 if ($score !~ /[123]/);
    scoreBox->setCurrentItem($score);
    scoreBox->setEnabled($score);
  } else {
    scoreBox->setEnabled(0);
    scoreBox->setCurrentItem(3);
  }

  my $comment = SUPER->this->{qsosxml}->getkeycomment($num);
  my $desc = SUPER->this->{qsosxml}->getkeydesc($num);
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
    "QSOS file (*.qsos *.xml)",
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
  if (!SUPER->this->{qsosxml}->load($file)) {
    Qt::MessageBox::warning(undef, "Can't open $file", "Sorry this file is not a valide QSOS file");
    SUPER->{file} = undef;
    return; 
  }

  ### listbox initialisation
  listBox->clear();
  foreach (@{SUPER->this->{qsosxml}->{tabular}}) {
  print $_->{name};
  my $v;
  $v .= ' ' foreach (0..$_->{deep});
  $v .= $_->{name};
  listBox->insertItem($v);
  loadSection(0);
  listBox->setEnabled(1);
  showProperty->setEnabled(1);
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
  my $file = Qt::FileDialog::getSaveFileName( undef,"QSOS file (*.qsos *.xml)", this);

  return unless $file;

  saveCurrentValue();
  SUPER->this->{qsosxml}->write($file);
}


sub fileExit
{
  this->close();
}


sub helpAbout
{
  aboutform = QSOS::QtEditor::Aboutform(this);
  aboutform->show(1);
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
  SUPER->this->{qsosxml}->setkeyscore($num, $score);

}



sub commentChanged
{

#  print "Qsosform->commentChanged(): Not implemented yet.\n";

}

sub propertyBox
{
  propertyform = QSOS::QtEditor::Propertyform(this);
  propertyform->init(SUPER->this->{qsosxml});
  propertyform->show(1);
}


1;
