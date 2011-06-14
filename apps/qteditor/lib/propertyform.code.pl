use QSOS::Document;

sub init
{
  this->{qsosxml} = shift;

  nameEdit->setText(this->{qsosxml}->getappname());
  releaseEdit->setText(this->{qsosxml}->getrelease());

  my $licenses = this->{qsosxml}->getlicenselist();
  licensecomboBox->insertItem("not in the list");
  licensecomboBox->insertItem($_) foreach (@$licenses);

  my $licenseid = this->{qsosxml}->getlicenseid();
  if ($licenseid) {
    licensecomboBox->setCurrentItem($licenseid+1);
  } else {
    licensecomboBox->setCurrentItem(0);
    newlicenseDesc->setText(this->{qsosxml}->getlicensedesc());
    newlicenseDesc->setEnabled( 1 );
  }

  commentEdit->setText(this->{qsosxml}->getdesc());
  url->setText(this->{qsosxml}->geturl());
  demourl->setText(this->{qsosxml}->getdemourl());

  authorsList->clear();
  my $authors = this->{qsosxml}->getauthors();
  foreach (@$authors) {
    my $string = $_->{name};
    $string .= " <".$_->{email}.">" if ($_->{email});
    authorsList->insertItem($string);
  }


}

sub accept
{
  this->{qsosxml}->setappname(nameEdit->text);
  this->{qsosxml}->setrelease(releaseEdit->text);
  if (licensecomboBox->currentItem == 0) {
  this->{qsosxml}->setlicenseid("");
  this->{qsosxml}->setlicensedesc(newlicenseDesc->text);
  } else {
  this->{qsosxml}->setlicenseid(licensecomboBox->currentItem - 1);
  this->{qsosxml}->setlicensedesc(licensecomboBox->currentText);
  }
  this->{qsosxml}->setdesc(commentEdit->text);
  this->{qsosxml}->seturl(url->text);
  this->{qsosxml}->setdemourl(demourl->text);
  this->close();
}

sub newauthor
{
  this->{qsosxml}->addauthor(authorEdit->text, emailEdit->text);

  my $string = authorEdit->text;
  $string .= " <".emailEdit->text.">" if (emailEdit->text);

  authorsList->insertItem($string);

}

sub delauthor
{
  my $item = authorsList->currentItem;
  this->{qsosxml}->delauthor($item);
  authorsList->removeItem($item);
}

sub licensecombolistchanged
{
  if (licensecomboBox->currentItem == 0) {
    newlicenseDesc->setEnabled( 1 );
  } else {
    newlicenseDesc->setEnabled( 0 );
  }
}

1;
