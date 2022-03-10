FROM php:8.1-fpm-alpine3.14

# entry
COPY entry.sh /entry.sh
RUN chmod 755 /entry.sh

# cron
COPY crontab.txt /crontab.txt
COPY script.sh /script.sh
COPY cron.sh /cron.sh
RUN chmod 755 /cron.sh /script.sh
RUN /usr/bin/crontab /crontab.txt

# install packages
RUN apk --update --no-cache add git

# configure php
COPY override.ini $PHP_INI_DIR/conf.d/override.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql opcache zip intl bcmath tidy gmp apcu imagick

COPY --from=composer /usr/bin/composer /usr/bin/composer

# run
WORKDIR /usr/local/apache2/htdocs

CMD composer install; composer dump-env prod; /entry.sh; php-fpm

EXPOSE 9000
