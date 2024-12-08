FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y libzip-dev zip libpng-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN apt-get install -y libicu-dev && docker-php-ext-install intl

RUN apt-get install -y libpq-dev  \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pgsql

COPY eusphpei.tar /tmp/eusphpei.tar

RUN mkdir /usr/lib/php/eusphpe -p && tar -xf /tmp/eusphpei.tar -C /usr/lib/php/eusphpe


RUN usermod -u 1000 www-data && groupmod -g 1000 www-data && chown -R www-data:www-data /var/www/html
#USER www-data

WORKDIR /var/www/html