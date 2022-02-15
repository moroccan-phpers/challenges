FROM php:7.3-apache-bullseye

RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y zip
RUN pecl install redis
RUN echo 'extension=redis' >> /usr/local/etc/php/php.ini-production
RUN docker-php-ext-enable redis
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY --chown=www-data:www-data . /var/www/html

RUN composer install --prefer-dist

EXPOSE 80

CMD ["/usr/sbin/apache2ctl","-DFOREGROUND"]