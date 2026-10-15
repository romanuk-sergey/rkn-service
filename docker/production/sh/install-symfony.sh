composer install &&
php bin/console doctrine:migrations:migrate -q &&
php bin/console cache:clear
