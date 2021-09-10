FROM php:8.0-fpm-alpine

# Define the working directory.
WORKDIR /var/www

# Specify a port to listen on. 
EXPOSE 9000

# Copy custom php-fpm config into image.
COPY .docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copy init script into image and make it executable.
COPY .docker/php/init.sh /usr/local/bin/init 
RUN chmod +x /usr/local/bin/init

# Copy wait-for-it script into image and make it executable.
COPY .docker/php/wait-for-it.sh /usr/local/bin/wait-for-it 
RUN chmod +x /usr/local/bin/wait-for-it

# Copy `install-php-extensions` helper script into image and make it executable.
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync

# Install php extensions and composer into image.
RUN install-php-extensions pgsql pdo_pgsql opcache @composer

# Install additional software packages into image.
RUN apk add --no-cache bash

# Run init command on startup.
CMD ["init"]