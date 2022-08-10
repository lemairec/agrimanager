#!/bin/bash

agrimanager=~/workspace/maplaine;

rm -rf ~/workspace/dump/;

rsync maplainemk@ssh.cluster023.hosting.ovh.net:maplaine/temp/dump ~/workspace/ --progress -ah

rm -rf $agrimanager/public/uploads/*


echo "copie"
cp -r ~/workspace/dump/* $agrimanager/public/uploads/
echo "copie end"

echo "sql"
cd $agrimanager/public/uploads/
$agrimanager/bin/console doctrine:database:drop --force;
$agrimanager/bin/console doctrine:database:create;
docker exec -i mariadb mysql -uroot -pzeouane maplaine < backup.sql
echo "sql end"
