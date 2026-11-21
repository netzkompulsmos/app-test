FROM php:8.4-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    mariadb-client \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    dos2unix \
    && docker-php-ext-install pdo pdo_mysql

# Configure PHP upload limits (50MB)
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source code
COPY . .

# Convert line endings, set permissions, and install composer dependencies if vendor is missing
RUN dos2unix docker-entrypoint.sh \
    && chmod +x docker-entrypoint.sh \
    && chmod -R 777 storage bootstrap/cache \
    && if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then composer install --optimize-autoloader --no-interaction; fi

EXPOSE 8000

ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
