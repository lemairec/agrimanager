symfony_agri
============

A Symfony project created on February 4, 2017, 12:17 pm.

````
mysql.server start

php composer.phar install

php bin/console doctrine:schema:create --dump-sql
php bin/console doctrine:schema:update --force
php bin/console doctrine:generate:entity
php bin/console generate:bundle

php bin/console server:run
````


OVH

````
php composer.phar install
php bin/console doctrine:schema:update --force
````

modify web/app.dev => true

Back_up
-------

````
bin/console doctrine:database:drop --force && bin/console doctrine:database:create && mysql --host localhost --user root --password maplaine < ~/Downloads/maplainemkagri.sql
````

````
cd ~/workspace/agrimanager/ && yarn encore production && rsync -az ~/workspace/agrimanager/public/build/ maplainemk@ssh.cluster023.hosting.ovh.net:temp && ssh maplainemk@ssh.cluster023.hosting.ovh.net
````

DROP TABLE ephy_substance_produit;
DROP TABLE ephy_substance;
DROP TABLE ephy_phrase_risques;
DROP TABLE ephy_phrase_risque;
DROP TABLE ephy_commercial_name;
DROP TABLE ephy_usage;
DROP TABLE ephy_produit;




ssh maplainemk@ssh.cluster023.hosting.ovh.net
cd maplaine; php bin/console export_bdd
exit

rm -rf ~/workspace/dump; mkdir ~/workspace/dump; rsync maplainemk@ssh.cluster023.hosting.ovh.net:maplaine/temp/dump/dump_maplaine.zip ~/workspace/dump
sh ~/workspace/agrimanager/import.sh