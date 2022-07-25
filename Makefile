setup:
	composer install
	composer dump-env prod
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:migrations:migrate -n
test:
	bin/phpunit
rector:
	vendor/bin/rector process src/
fixtures:
	bin/console doctrine:fixtures:load -n