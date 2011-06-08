#!/bin/sh
XML_VALIDATION_TOOL=xmlstarlet
if [ `which ${XML_VALIDATION_TOOL}` = ''  ]; then
	echo "This script requires ${XML_VALIDATION_TOOL} to do something !!!"
	exit
fi 

# Checking out all qsos's sheet available in the cvs
mkdir ./target
cd ./target
cvs -z3 -d:pserver:anonymous@cvs.savannah.nongnu.org:/sources/qsos co qsos/sheet
cd ..
# validating all of xml files
xmlstarlet val --list-bad --err -s src/xsd/qsos.xsd xml/table.x target/*/*/*.qsos
