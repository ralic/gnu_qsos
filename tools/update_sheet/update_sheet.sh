#$Id: update_sheet.sh,v 1.1 2006/03/28 17:19:33 goneri Exp $
. qsos.cfg

createSheet () {
  FULLPATH=$@
  FILE=`basename $@|sed s/\.qsos$/.html/`
  DIR=$DESTDIR`dirname $@ | sed s%^.%%`

  mkdir -p $DIR

  echo converting $FILE 
  xsltproc $XSLT $FULLPATH > $DIR/$FILE
}

createIndex () {
  local i
  DIR=$@
  LIST="\n<ul>"
  
  rm -f index.html
  for i in `ls $DIR`;do
    if [ -d $i ]; then
      TYPE="folder"
    else
      TYPE="file"
    fi

    LIST=$LIST"<li type=$TYPE><a href=\"$i\">$i</a></li>\n"
  done
  LIST=$LIST"</ul>\n"
  
  cat $TEMPLATES_DIR/index.tpl| sed s!%%LIST%%!"$LIST"! \
  > $DIR/index.html



  echo index $DIR/index.html created

}

LOCALDIR=`pwd`
mkdir -p $CVS_LOCAL_DIR
mkdir -p $DESTDIR

cd $CVS_LOCAL_DIR
cvs -z3 -d$CVS_ROOT co -P $CVS_MODULE
cd $CVS_LOCAL_DIR/$CVS_MODULE
for i in `find  -type f |grep qsos$`; do
  createSheet $i
done

cd $DESTDIR
for i in `find  -type d`; do
  createIndex $i
done
