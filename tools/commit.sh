#!/bin/bash
cd ..
new_files_count=`svn stat | grep ? | wc -l`
if [ $new_files_count -gt 0 ]; then
	echo "These files are not added to the SVN server:"
	svn stat | grep ? | sed 's/^\? *//g' | while read l
	do 
		echo "- $l"
	done
	echo -n "Please paste the files you want to add and press [Enter]:    "
	read added_files
	if [ -n "$added_files" ]; then
		svn add $added_files
	fi
fi



comment=""
while [ -z "$comment" ]
do
	echo -n "- Please write a comment: "
	read comment
done
if [ -z "$comment" ]; then
	exit 1
fi
OUTPUT=`svn ci -m "$comment"`
REVISION_NUM=`echo $OUTPUT | grep "Committed revision " | awk '{print $NF;}' | tr -d '.'`
if [ -n "$REVISION_NUM" ]; then
	cd tools
	echo $REVISION_NUM >> versions
	echo -e '\E[77;32m'"\033[1mCommited Successfully Version: $REVISION_NUM\033[0m"
else
	echo -e '\E[77;31m'"\033[1mERROR: $OUTPUT\033[0m"
fi
exit 0
