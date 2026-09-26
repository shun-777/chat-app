# PHP 8.4 + FPM
FROM php:8.4-fpm

# 必要なパッケージ
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    sqlite3 \
    nodejs \
    npm \
    nginx \
    && docker-php-ext-install \
    pdo_sqlite \
    mbstring \
    bcmath \
    exif \
    pcntl \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Laravelプロジェクト
WORKDIR /var/www/html
COPY . .

# PHP依存関係
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# JavaScript依存関係・Viteビルド
RUN npm install
RUN npm run build

# SQLiteファイルを作成
RUN touch database/database.sqlite

# Laravelのキャッシュ・権限
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && chown -R www-data:www-data storage bootstrap/cache database

# Nginx設定
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# 起動スクリプト
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]