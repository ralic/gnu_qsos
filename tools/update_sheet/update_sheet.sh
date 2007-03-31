#!/bin/sh
#$Id: update_sheet.sh,v 1.13 2007/03/31 01:57:05 goneri Exp $
#  Copyright (C) 2006 2007 Atos Origin 
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
######
#  This script checkout current sheet from the CVS, translate them
#  to xhtml and upload them on a ftp server
set -e

. qsos.cfg || exit 1

createSheet () {
  FULLPATH=$@
  HTML_FILE=`basename $@|sed s/\.qsos$/.html/`
  QSOS_FILE=`basename $@|sed s/\.qsos$/.qsos/`
  DIR=$DESTDIR_SHEETS`dirname $@ | sed s%^.%%`

  mkdir -p $DIR

  echo converting sheet $FILE 
  cp $FULLPATH $DIR/$QSOS_FILE
  xsltproc $XSLT_QSOS $FULLPATH|sed s!%%CSS_SHEET%%!"$CSS_SHEET"! > $DIR/$HTML_FILE
}

createTemplate () {
  FULLPATH=$@
  HTML_FILE=`basename $@|sed s/\.qtpl$/.html/`
  QSOS_FILE=`basename $@|sed s/\.qtpl$/.qsos/`
  DIR=$DESTDIR_TEMPLATES

  mkdir -p $DIR"/"

  
  echo converting template $FILE  to $DIR/$FILE
# Caramba !
cat $FULLPATH | perl -nle "
if (/<include\W+section=\"(\w*)\"\W+>/) {
        \$f = \"$INCLUDE_DIR/\$1.qin\";
        if (-f \$f && (open FILE,\"<\$f\")) {
                foreach (<FILE>) {chomp;print};
        } else { die \"can not open \$f\" }
} else {print}" > $DIR/$QSOS_FILE

xsltproc $XSLT_QTPL $DIR/$QSOS_FILE|sed s!%%CSS_SHEET%%!"$CSS_SHEET"! - > $DIR/$HTML_FILE
}

createIndex () {
  local i
  TARGET=$1
  DIR=$2
  echo "$TYPE, $DIR"
  LIST="\n<ul class=\"downloads\">"
 
  echo $DIR
  rm -f $DIR/index.html
  for i in `ls $DIR|grep -v qsos`;do
    echo $i
    if [ -f "$DIR/$i" ]; then
      TYPE="sheet"
      echo "sheet: $i"
    else
      echo "dossier: $i"
      TYPE="folder"
    fi

    if [ "$TYPE" = "sheet" ]
    then
      LIST=$LIST"<li class=$TYPE>`echo $i|sed s/\.html$//` (<a href=\"$i\">view</a>) (<a href="`echo $i|sed s/\.html$/.qsos/`">sources</a>)</li>\n"
    else
      LIST=$LIST"<li class=$TYPE><a href=\"$i\">`echo $i|sed s/\.html$//`</a></li>\n"
    fi
  done
  LIST=$LIST"</ul>\n"
  
  cat $HTMLTEMPLATES_DIR/index_$TARGET.tpl| \
  sed s!%%CSS_LISTING%%!"$CSS_LISTING"!| \
  sed s!%%LIST%%!"$LIST"!| \
  sed s!%%DIRECTORY%%!"$DIR"! \
  > $DIR/index.html

  echo index $DIR/index.html created
}

# FIXME if mkdir failed, web site is removed...
upload () {
cat <<eof | lftp
open -u $FTP_LOGIN,$FTP_PASSWD $FTP_HOST
mkdir -p $FTP_DIR_SHEETS
cd $FTP_DIR_SHEETS 
mirror -c -e -R $DESTDIR_SHEETS .
mkdir -p $FTP_DIR_TEMPLATES
cd $FTP_DIR_TEMPLATES 
mirror -c -e -R $DESTDIR_TEMPLATES .
exit
eof

}

deploy_local () {
if [ ! -d $LOCAL_DIR_SHEETS ] || [ ! -d $LOCAL_DIR_TEMPLATES ]; then
  echo "LOCAL_DIR_SHEETS and LOCAL_DIR_TEMPLATES must exist"
  exit 1
fi

rm -rf $LOCAL_DIR_SHEETS/*
rm -rf $LOCAL_DIR_TEMPLATES/*
cp -rv $DESTDIR_SHEETS/* $LOCAL_DIR_SHEETS
cp -rv $DESTDIR_TEMPLATES/* $LOCAL_DIR_TEMPLATES
}


LOCALDIR=`pwd`
rm -Rf $CVS_LOCAL_DIR $DESTDIR_SHEETS $DESTDIR_TEMPLATES 
mkdir -p $CVS_LOCAL_DIR
mkdir -p $DESTDIR_SHEETS

cd $CVS_LOCAL_DIR
cvs -z3 -d$CVS_ROOT co -P $CVS_MODULE
cd $CVS_LOCAL_DIR/$CVS_MODULE

for i in `find -name '*.qtpl'`; do
  createTemplate $i
done


for i in `find  -name '*.qsos'`; do
  createSheet $i
done

cd $DESTDIR_SHEETS
for i in `find  -type d`; do
  createIndex "sheet" $i
done

createIndex "template" $DESTDIR_TEMPLATES 
if [ "$FTP_UPLOAD" = "yes" ]
then
  upload
fi

if [ "$LOCAL_DEPLOY" = "yes" ]
then
  deploy_local
fi
