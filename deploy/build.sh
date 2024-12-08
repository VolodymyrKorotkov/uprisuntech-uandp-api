#!/bin/sh

cd /var/www/uandp-api &&
git pull &&
docker-compose exec php composer install &&
docker-compose exec php bin/console --no-interaction doc:mig:mig &&
docker-compose exec php bin/console doctrine:cache:clear-metadata &&
docker-compose exec php bin/console assets:install