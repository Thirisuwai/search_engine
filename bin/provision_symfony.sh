#!/bin/sh
cd /vagrant

echo Install composer packages
composer install -vvv --profile

echo Run php web server in the background
php app/console --no-ansi -vvv server:run 0.0.0.0:8000 > app/logs/server.log &h