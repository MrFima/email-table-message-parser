FROM php:8.0.14-fpm

RUN apt-get update && apt-get install -y \
curl \
wget \
git \
&& pecl install mailparse \
&& docker-php-ext-enable mailparse

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

WORKDIR /var/www/test

CMD ["php-fpm"]