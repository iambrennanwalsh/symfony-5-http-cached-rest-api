#!/usr/bin/env bash

# Enables job control.
set -m

# Start php-fpm in the background (-R flag allows the pool to run as root). 
php-fpm &

# Install composer dependencies.
composer install

# Wait for postgresql to become available then run migrations.
wait-for-it db:5432 -- php bin/console doctrine:migrations:migrate 

# When `composer install` completes, move the php-fpm process back into the foreground.
fg %1

