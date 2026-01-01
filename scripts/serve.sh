#!/bin/sh

# For the servers only
echo "Serve : 0.0.0.0:8000 in background"
sudo -u www-data php artisan serve --host=0.0.0.0 --port=8000 > storage/laravel.log 2>&1 &
