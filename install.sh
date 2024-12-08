docker-compose up -d
docker-compose exec php composer install
docker-compose exec php bin/console doc:mig:mig --no-interaction
docker-compose exec php php -d "memory_limit=-1" bin/console assets:install
docker-compose run node npm install
docker-compose run node npm run build