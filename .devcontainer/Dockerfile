FROM mcr.microsoft.com/devcontainers/php:1-8.2

RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y mariadb-client libpng-dev libjpeg-dev \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install mysqli pdo pdo_mysql gd
