symfony_agri
============

A Symfony project created on February 4, 2017, 12:17 pm.

mysql.server start

php bin/console doctrine:schema:create --dump-sql
php bin/console doctrine:schema:drop --force; php bin/console doctrine:schema:update --force
php bin/console doctrine:generate:entity
php bin/console generate:bundle

php bin/console server:run

