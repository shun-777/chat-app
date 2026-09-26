#!/bin/sh

set -e

# SQLiteデータベースを作成
touch /var/www/html/database/database.sqlite

# データベースのマイグレーション
php artisan migrate --force

# Laravelのキャッシュ
php artisan config:cache
php artisan route:cache
php artisan view:cache

# PHP-FPMをバックグラウンドで起動
php-fpm -D

# Nginxをフォアグラウンドで起動
nginx -g "daemon off;"