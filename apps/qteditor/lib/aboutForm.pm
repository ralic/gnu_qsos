# Form implementation generated from reading ui file 'aboutform.ui'
#
# Created: Fri Feb 17 16:51:16 2006
#      by: The PerlQt User Interface Compiler (puic)
#
# WARNING! All changes made in this file will be lost!


use strict;
use utf8;


package QSOS::QtEditor::aboutForm;
use Qt;
use Qt::isa qw(Qt::Dialog);
use Qt::attributes qw(
    pushButton1
    textLabel1
);



sub NEW
{
    shift->SUPER::NEW(@_[0..3]);

    if ( name() eq "unnamed" )
    {
        setName("aboutForm" );
    }
    setSizePolicy(Qt::SizePolicy(2, 2, 0, 0, this->sizePolicy()->hasHeightForWidth()) );
    setMinimumSize(Qt::Size(400, 220) );
    setMaximumSize(Qt::Size(400, 220) );
    setBaseSize(Qt::Size(400, 200) );
    setSizeGripEnabled(0 );


    pushButton1 = Qt::PushButton(this, "pushButton1");
    pushButton1->setGeometry( Qt::Rect(200, 180, 98, 25) );

    textLabel1 = Qt::Label(this, "textLabel1");
    textLabel1->setGeometry( Qt::Rect(50, 10, 290, 170) );
    languageChange();
    my $resize = Qt::Size(400, 220);
    $resize = $resize->expandedTo(minimumSizeHint());
    resize( $resize );
    clearWState( &Qt::WState_Polished );

    Qt::Object::connect(pushButton1, SIGNAL "clicked()", this, SLOT "close()");
}


#  Sets the strings of the subwidgets using the current
#  language.

sub languageChange
{
    setCaption(trUtf8("About QSOS Qt Editor") );
    pushButton1->setText( trUtf8("Ok") );
    textLabel1->setText( trUtf8("<h1>About QSOS Editor</h1>\n" .
    "<p>Authors :\n" .
    "<ul>\n" .
    "<li>Gonéri Le Bouder (Atos Origin)</li>\n" .
    "</ul>\n" .
    "</p>\n" .
    "<p>Web site : <b>http://www.qsos.org</b>\n" .
    "</p>") );
}


1;
