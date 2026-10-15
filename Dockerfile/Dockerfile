FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        ca-certificates \
        libonig-dev \
        libzip-dev \
        libicu-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        bcmath \
        zip \
        intl \
        opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --optimize-autoloader \
    && mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && sed -i 's/\r$//' deploy/start.sh \
    && chmod +x deploy/start.sh

RUN printf '%s\n' \
    '<VirtualHost *:80>' \
    '    DocumentRoot /var/www/html/public' \
    '    <Directory /var/www/html/public>' \
    '        AllowOverride All' \
    '        Require all granted' \
    '    </Directory>' \
    '    ErrorLog /proc/self/fd/2' \
    '    CustomLog /proc/self/fd/1 combined' \
    '</VirtualHost>' \
    > /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

CMD ["sh", "/var/www/html/deploy/start.sh"]