#!/usr/bin/env bash

# Enables job control.
set -m

# Start php-fpm in the background (-R flag allows the pool to run as root). 
php-fpm &

# Install composer dependencies.
composer install

# Wait for postgresql to become available then setup the database.
if [[ "$APP_ENV" == "dev" ]]; then
  wait-for-it postgres:5432 -- \
    php bin/console doctrine:schema:drop --force && \
    php bin/console doctrine:schema:update --force && \
    php bin/console hautelook:fixtures:load --no-interaction
fi 

# Move the php-fpm process back into the foreground.
fg %1

