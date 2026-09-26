#!/bin/sh

set -e

mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views

# SQLiteデータベースを作成
touch /var/www/html/database/database.sqlite

# データベースのマイグレーション
php artisan migrate --force

# Laravelのキャッシュ
php artisan config:cache
php artisan route:cache

# PHP-FPMをバックグラウンドで起動
php-fpm -D

# Nginxをフォアグラウンドで起動
nginx -g "daemon off;"
