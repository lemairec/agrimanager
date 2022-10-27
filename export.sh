#!/bin/bash

agrimanager=~/workspace/maplaine;
echo "begin"

echo "remove /home/maplainemk/maplaine/temp/dump"
rm -rf /home/maplainemk/maplaine/temp/dump
echo "remove ok"
echo "mkdir /home/maplainemk/maplaine/temp/dump"
mkdir /home/maplainemk/maplaine/temp/dump
echo "mkdir ok"
echo "sql"
cd /home/maplainemk/maplaine; php bin/console export_bdd
echo "sql ok"
echo "copie"
cp -r ~/maplaine/public/uploads/* /home/maplainemk/maplaine/temp/dump
echo "copie ok"
