#!/bin/bash
set -e

echo "Deployment started ...."

cd ~/htdocs/staging.businessjoy.in/

# Turn ON Maintenance Mode or return true
# if already is in maintenance mode
(php artisan down) || true

# Pull the latest version of the app
git pull origin main


# Turn OFF Maintenance mode
php artisan up

echo "Deployment finished!"