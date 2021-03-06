#!/bin/sh

set -e

echo "Deploying SpaceKom API ..."

# Enter maintenance mode

(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true

    # Update codebase

    git fetch origin deploy

    git reset --hard origin/deploy


    # Install dependencies based on lock file

    composer install --no-interaction --prefer-dist --optimize-autoloader


    # Migrate database

    php artisan migrate --force
    
    chmod -R 777 .


    # Note: If you're using queue workers, this is the place to restart them.

    # ...

    # Clear cache

    php artisan optimize

 
    # Reload PHP to update opcache

    echo "" | sudo -S service php8.1-fpm reload

# Exit maintenance mode

php artisan up

echo "SpaceKom API deployed!"