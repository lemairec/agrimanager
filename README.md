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
mysql --host localhost --user root --password maplaine < ~/Downloads/maplainemkagri_2.sql
````
