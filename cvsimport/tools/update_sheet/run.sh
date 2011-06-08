#!/bin/sh
#$Id: run.sh,v 1.1 2007/03/31 02:04:04 goneri Exp $
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
#
# A wrapper for the crontab
set -e

UPDATESHETDIR="/home/qsos/cvs/tools/update_sheet"
JABBERID="$JABBERID"
###############

cd $UPDATESHETDIR > last.log 2>&1 
./update_sheet.sh >> last.log 2>&1
if [ "$?" -ne "0" ]; then
  touch /tmp/update_sheet_failed
else
  if [ -f /tmp/update_sheet_failed_sent ]; then
  echo "QSOS sheets refreshed successfully. No error anymore"| sendxmpp -s "update_sheet.sh is ok" $JABBERID
  fi

  rm -f /tmp/update_sheet_failed
  rm -f /tmp/update_sheet_failed_sent
fi

if [ -f /tmp/update_sheet_failed ] && [ ! -f /tmp/update_sheet_failed_sent ]; then

  CREATETIME=$(stat -c %Y /etc/crontab)
  CURRENTTIME=$(date +%s)
  AGE=$(($CURRENTTIME - $CREATETIME))

  if [ $AGE -gt $((60*60*2)) ]; then # Send a Jabber message after 2 hours of error
    sendxmpp -s "update_sheet.sh failed" $JABBERID << EOF
[WARNING] QSOS sheets failed to refresh during the last 2 hours

10 last lines of last.log:
$(head -n 10 last.log)
EOF 
    touch /tmp/update_sheet_failed_sent
  fi
fi
