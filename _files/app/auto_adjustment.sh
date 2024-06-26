#!/bin/sh

mkdir -p /home/test/storage
mkdir -p /home/trial/storage
mkdir -p /home/laravel/storage

chmod -R 777 /home/test/storage
chown -R www-data:www-data /home/test/storage
chmod -R 777 /home/trial/storage
chown -R www-data:www-data /home/trial/storage
chmod -R 777 /home/laravel/storage
chown -R www-data:www-data /home/laravel/storage