#!/bin/bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled

php artisan config:cache
php artisan route:cache
php artisan optimize