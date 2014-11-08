#!/bin/bash
function get_file_version () {
	local FILE=$1
	svn info $FILE | grep "Revision:" | awk '{print $2;}'
}

BK_FILE="BK.txt"
echo -n "" > $BK_FILE
find ./ -type f | grep -v .svn  | while read file_path
do
	VERSION=`get_file_version $file_path`;
	if [ -n "$VERSION" ]; then
		echo "$VERSION###$file_path" >> $BK_FILE
	fi
done

