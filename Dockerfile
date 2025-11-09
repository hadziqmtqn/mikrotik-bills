FROM php:8.3-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install required packages
RUN apk add --no-cache \
    bash \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor \
    nginx

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install pdo_mysql zip gd exif bcmath intl

# Install composer binary from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /code

# Copy application code
COPY . .

# Install PHP deps and build assets (fail if deps missing)
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci && npm run build

# Ensure supervisor conf dir and log dir exist and proper permissions
RUN mkdir -p /etc/supervisor/conf.d /var/log/supervisor \
 && chown -R www-data:www-data /var/log/supervisor \
 && chown -R www-data:www-data /code/storage /code/bootstrap/cache \
 && chmod -R 775 /code/storage /code/bootstrap/cache

# Copy nginx and supervisord configuration from repo -> image
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/queue-worker.conf /etc/supervisor/conf.d/queue-worker.conf

# add entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start supervisord in foreground using absolute config path
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
