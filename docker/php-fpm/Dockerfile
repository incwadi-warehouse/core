FROM php:8.1-fpm-alpine3.14

# cron
COPY docker/php-fpm/crontab.txt /crontab.txt
COPY docker/php-fpm/script.sh /script.sh
COPY docker/php-fpm/cron.sh /cron.sh
RUN chmod 755 /cron.sh /script.sh
RUN /usr/bin/crontab /crontab.txt

# install packages
RUN apk --update --no-cache add git

# configure php
COPY docker/php-fpm/override.ini $PHP_INI_DIR/conf.d/override.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql opcache zip intl bcmath tidy gmp apcu imagick

COPY --from=composer /usr/bin/composer /usr/bin/composer

# entry
COPY docker/php-fpm/entry.sh /entry.sh
RUN chmod 755 /entry.sh

#  workdir
WORKDIR /usr/local/apache2/htdocs

# copy app
COPY . .

# composer
RUN composer install --no-interaction
RUN composer dump-env prod

# run
CMD /entry.sh ${DATABASE_SERVER} ${DATABASE_PORT}; php-fpm

EXPOSE 9000
