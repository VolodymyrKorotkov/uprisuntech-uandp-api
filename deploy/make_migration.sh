docker-compose exec php bin/console doc:mig:diff --namespace "App\ApplicationFlow\Migrations"
docker-compose exec php bin/console doc:mig:diff --namespace "App\UserService\Migrations"
docker-compose exec php bin/console doc:mig:diff --namespace "App\DescBook\Migrations"
docker-compose exec php bin/console doc:mig:diff --namespace "App\LMS\Migrations"
docker-compose exec php bin/console doc:mig:diff --namespace "App\FileStorage\Migrations"

sudo chown denys:denys -R src