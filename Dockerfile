FROM php:7.2-apache
LABEL MAINTAINER="Francesco Bianco <info@javanile.org>"

RUN docker-php-ext-install mysqli pdo pdo_mysql gettext \
 && a2enmod rewrite
