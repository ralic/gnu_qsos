use QSOS::Document;

sub init
{
  this->{qsosxml} = shift;

  nameEdit->setText(this->{qsosxml}->getappname());
  releaseEdit->setText(this->{qsosxml}->getrelease());

  my $licenses = this->{qsosxml}->getlicenselist();
  licensecomboBox->insertItem($_) foreach (@$licenses);

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
  this->{qsosxml}->setlicense(licensecomboBox->currentItem);
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

1;
