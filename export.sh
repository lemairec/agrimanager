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
mysqldump -u maplainemkagri --password=4GT8H7fedm --host=maplainemkagri.mysql.db --port=3306 --opt maplainemkagri --max_allowed_packet=512M --no-tablespaces > /home/maplainemk/maplaine/temp/dump/backup.sql
echo "sql ok"
echo "copie"
cp -r ~/maplaine/public/uploads/* /home/maplainemk/maplaine/temp/dump
echo "copie ok"
echo "copie ok" > /home/maplainemk/maplaine/temp/dump/fin.txt
