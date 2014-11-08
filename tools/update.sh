#!/bin/bash
cd ..

cat tools/ignore_list.txt | while read ignored_file
do
    FOLDERNAME=`dirname $ignored_file`
    mkdir -p tools/backup/$FOLDERNAME
    cp $ignored_file tools/backup/$ignored_file
    svn revert $ignored_file
done

svn up

cat tools/ignore_list.txt | while read ignored_file
do
    mv tools/backup/$ignored_file $ignored_file
    chown longarm:longarm $ignored_file
done
rm -rdf tools/backup/*


cd tools
chmod 755 ./prepare.sh
./prepare.sh
