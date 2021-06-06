FROM php:8.0-fpm-alpine

COPY db.sh /usr/bin/db-setup.sh
RUN chmod +x /usr/bin/db-setup.sh

COPY override.ini $PHP_INI_DIR/conf.d/override.ini

RUN apk --update --no-cache add git

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql opcache zip intl bcmath tidy gmp apcu imagick

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /usr/local/apache2/htdocs

CMD composer install ; composer dump-env prod ; /usr/bin/db-setup.sh ; php-fpm

EXPOSE 9000