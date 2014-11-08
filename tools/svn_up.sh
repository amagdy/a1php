#!/bin/bash

# this script takes a revision number or more space separated then loops on these revision numbers
# for each revision number files to be updated are retrieved 
# loop on revision files 
# for each file if file not in ignore list then svn up to the version specified
STR_IGNORED_FILES="`cat ignore_list.txt | awk -vORS=\"###\" '{print $0;}'`"
function is_file_ignored () {
	# If active check active
	echo $STR_IGNORED_FILES | awk -vRS="###" '{print $0;}' | while read line
	do
		if [ -n "$line" ]; then 
			if [ -n "`echo "$1" | grep "$line"`" ]; then
				return 1
			fi
		fi
	done
	if [ $? -eq 1 ]; then
		return 0
	else 
		return 1
	fi
}

cd ..

SVN_BASE=`svn info | grep -E "^URL: " | awk '{print $2;}' | sed 's/^http:\/\/[^\/]*\/svn\/web//g'`
SVN_BASE=$SVN_BASE"/"
for REVISION in "$@"
do
        svn log -q -v -r$REVISION | sed 's/^ *//g' | grep -E "^[A-Z] \/" | sed 's/^[A-Z] *//g' | while read FILENAME
        do
                FILENAME=`echo $FILENAME | replace "$SVN_BASE" ""`
                is_file_ignored $FILENAME
		if [ $? -ne 0 ]; then
		        CURRENT_FILE_VERSION=`svn info $FILENAME | grep -E "^Revision:" | awk '{print $2;}'`
		        if [ -n "$CURRENT_FILE_VERSION" ]; then
		                if [ $CURRENT_FILE_VERSION -lt $REVISION ]; then
		                        svn up -r$REVISION $FILENAME
		                fi
		        else
		                svn up -r$REVISION $FILENAME
		        fi
		fi
        done
done

echo "Preparing..."
cd tools
chmod 755 ./prepare.sh
./prepare.sh

