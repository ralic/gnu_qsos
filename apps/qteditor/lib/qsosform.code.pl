use Qt::attributes qw( aboutform propertyform );
use QSOS::Document;
use Qt::debug;
use QSOS::QtEditor::Aboutform;
use QSOS::QtEditor::Propertyform;
use Carp;
use strict;
use warnings;
# all occurances of scoreBox removed


sub saveCurrentValue
{

  my $num = SUPER->{current_section_nbr};
  SUPER->this->{qsosxml}->setkeycomment($num, commentBox->text);

  my $score;
  if (radioScore0->isOn()) {
    $score = 0;
  } elsif (radioScore1->isOn()) {
    $score = 1;
  } elsif (radioScore2->isOn()) {
    $score = 2;
  }
  
  SUPER->this->{qsosxml}->setkeyscore($num, $score);

}


sub loadSection
{
  my ($num) = @_;

  # Saving current commentBox :
  if (defined SUPER->{current_section_nbr}) {
    saveCurrentValue();
  }

  SUPER->{current_section_nbr} = $num;
  my $score = SUPER->this->{qsosxml}->getkeyscore($num);

  if (defined $score) {
      radioScore0->setEnabled(1);
      radioScore1->setEnabled(1);
      radioScore2->setEnabled(1);

      my $desc0 = SUPER->this->{qsosxml}->getkeydesc($num,0);
      $desc0 = "feature not supported" unless (defined $desc0);
      textScore0->setText($desc0);

      my $desc1 = SUPER->this->{qsosxml}->getkeydesc($num,1);
      $desc1 = "feature partialy supported" unless (defined $desc1);
      textScore1->setText($desc1);

      my $desc2 = SUPER->this->{qsosxml}->getkeydesc($num,2);
      $desc2 = "feature fully supported" unless (defined $desc2);
      textScore2->setText($desc2);

  }
 else {
    # disabling all radio widgets
      radioScore0->setEnabled(0);
      radioScore1->setEnabled(0);
      radioScore2->setEnabled(0);
      
      textScore0->setText('');
      textScore1->setText('');
      textScore2->setText('');
  }
      
  radioScore0->setChecked(0);
  radioScore1->setChecked(0);
  radioScore2->setChecked(0);
  if (defined $score && $score =~ /^[012]$/) {
    if ($score == 0) {
      radioScore0->setChecked(1)
    }
    if ($score == 1) {
      radioScore1->setChecked(1);
    }
    if ($score == 2) {
      radioScore1->setChecked(1);
    }
  }
  my $comment = SUPER->this->{qsosxml}->getkeycomment($num);
  my $desc = SUPER->this->{qsosxml}->getkeydesc($num);
  $desc = "no description" unless (defined $desc);
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
  #listView->clear();
  listView->clear();
  listView->setSortColumn(1);
  listView->hideColumn(1); #doesn't work
#listView->setResizeEnabled(0, 1);
  listView->setColumnWidth(1, 0 );

  my @pile;
  my $last;
  my $i=0;
  foreach (@{SUPER->this->{qsosxml}->{tabular}}) {
  my $item;
  while ($_->{deep} < @pile) {
    pop @pile; # on baisse
  }

  if ($_->{deep} == 0) {
    $item = Qt::ListViewItem(listView, undef, $i);
    push @pile, $item;
    $last = $item;
  } elsif ($_->{deep} == @pile) {
    $item = Qt::ListViewItem($pile[$#pile], undef,$i);
    $last = $item;
  } elsif ($_->{deep} > @pile) {
    $item = Qt::ListViewItem($last , undef, $i);
    push @pile, $item;
    $last = $item;
  } else {
    die;
  }
  $item->setOpen(1);
  $item->setText(0,$_->{title});
  $i++;
}
# Activate the property in the menu entry
showProperty->setEnabled(1);

}

sub fileSave
{
  saveCurrentValue();
  if (defined SUPER->{file}) {
    print "saving :\n";
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
  my $item = listView->currentItem()->text(1);
  loadSection($item) if ($item);
}

sub setscore0
{
  my $num = SUPER->{current_section_nbr};
  return unless (defined ($num) and ($num));
  radioScore1->setChecked(0);
  radioScore2->setChecked(0);
}

sub setscore1
{
  my $num = SUPER->{current_section_nbr};
  return unless (defined ($num) and ($num));
  radioScore0->setChecked(0);
  radioScore2->setChecked(0);
}
sub setscore2
{
  my $num = SUPER->{current_section_nbr};
  return unless (defined ($num) and ($num));
  radioScore0->setChecked(0);
  radioScore1->setChecked(0);
}


sub propertyBox
{
  propertyform = QSOS::QtEditor::Propertyform(this);
  propertyform->init(SUPER->this->{qsosxml});
  propertyform->show(1);
}


1;
