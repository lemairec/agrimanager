#!/bin/bash

dumps=~/Downloads/dump_*

arrays=($(ls -d $dumps))
arraylength=${#arrays[@]}

# use for loop to read all values and indexes
for (( i=0; i<${arraylength}; i++ ));
do
  echo $i " / " ${arraylength} " : " ${arrays[$i]}
done

read input
dump=${arrays[$input]}

echo "$dump, is ok?"
read input


agrimanager=~/workspace/agrimanager;

rm -f $agrimanager/public/uploads/documents/*
cd $agrimanager/public/uploads/documents/

unzip $dump


#cp $dump/*.pdf $agrimanager/public/uploads/documents/;

$agrimanager/bin/console doctrine:database:drop --force;
$agrimanager/bin/console doctrine:database:create;
mysql --host localhost --user root --password maplaine < database.sql
