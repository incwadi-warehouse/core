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
csfixer:
	vendor/bin/php-cs-fixer fix
psalm:
	vendor/bin/psalm
psalter:
	vendor/bin/psalter --issues=all
