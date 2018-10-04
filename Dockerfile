FROM php:7.1.2-apache 
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli
RUN pecl install xdebug