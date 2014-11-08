#!/bin/sh
sitefolder=`cd ..;pwd`

cd $sitefolder/model
astyle *.php
mkdir $sitefolder/tools/model
mv $sitefolder/model/*.php.orig $sitefolder/tools/model

cd $sitefolder/controller
astyle *.php
mkdir $sitefolder/tools/controller
mv $sitefolder/controller/*.php.orig $sitefolder/tools/controller
