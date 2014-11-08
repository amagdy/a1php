#!/bin/sh
sitefolder=`cd ..;pwd`
cd ..

chmod 755 -R "$sitefolder"
find $sitefolder -type f | grep -v .svn | while read l; do chmod 644 "$l"; done

chmod 700 $sitefolder/tools/*.sh
chmod 700 $sitefolder/tools/*.sql

chmod 777 $sitefolder/smarty/templates_c
find $sitefolder/uploads/ -type d | grep -v .svn | while read l; do chmod 777 "$l"; done
find $sitefolder/uploads/ -type f | grep -v .svn | while read l; do chmod 666 "$l"; done

find $sitefolder -name "*.*~" | grep -v .svn | while read l; do rm "$l"; done
find $sitefolder/smarty/templates_c -maxdepth 1 -type f -exec rm "{}" \;
