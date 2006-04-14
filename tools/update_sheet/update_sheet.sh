#$Id: update_sheet.sh,v 1.5 2006/04/14 11:15:25 goneri Exp $
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
######
#  This script checkout current sheet from the CVS, translate them
#  to xhtml and upload them on a ftp server

. qsos.cfg

createSheet () {
  FULLPATH=$@
  FILE=`basename $@|sed s/\.qsos$/.html/`
  DIR=$DESTDIR`dirname $@ | sed s%^.%%`

  mkdir -p $DIR

  echo converting $FILE 
  xsltproc $XSLT $FULLPATH|sed s!%%CSS_SHEET%%!"$CSS_SHEET"! > $DIR/$FILE
}

createIndex () {
  local i
  DIR=$@
  LIST="\n<ul class=\"downloads\">"
 
  echo $DIR
  rm -f $DIR/index.html
  for i in `ls $DIR`;do
    if [ -d $i ]; then
      TYPE="folder"
    else
      TYPE="sheet"
    fi

    LIST=$LIST"<li class=$TYPE><a href=\"$i\">`echo $i|sed s/\.html$//`</a></li>\n"
  done
  LIST=$LIST"</ul>\n"
  
  cat $TEMPLATES_DIR/index.tpl| \
  sed s!%%CSS_LISTING%%!"$CSS_LISTING"!| \
  sed s!%%LIST%%!"$LIST"!| \
  sed s!%%DIRECTORY%%!"$DIR"! \
  > $DIR/index.html



  echo index $DIR/index.html created

}

upload () {
cat <<eof | lftp
open -u $FTP_LOGIN,$FTP_PASSWD $FTP_HOST
cd $FTP_DIR 
mirror -c -e -R $DESTDIR .
exit
eof

}

LOCALDIR=`pwd`
mkdir -p $CVS_LOCAL_DIR
mkdir -p $DESTDIR

cd $CVS_LOCAL_DIR
cvs -z3 -d$CVS_ROOT co -P $CVS_MODULE
cd $CVS_LOCAL_DIR/$CVS_MODULE
for i in `find  -type f |grep -v template|grep qsos$`; do
  createSheet $i
done

cd $DESTDIR
for i in `find  -type d`; do
  createIndex $i
done

if [ "$UPLOAD" = yes ]
then
  upload
fi
