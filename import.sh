#!/bin/bash

dump=~/Downloads/dump_1546164665;
agrimanager=~/workspace/agrimanager;

rm -f $agrimanager/public/uploads/documents/*.pdf

cp $dump/*.pdf $agrimanager/public/uploads/documents/;

bin/console doctrine:database:drop --force;
bin/console doctrine:database:create;
mysql --host localhost --user root --password maplaine < $dump/database.sql
