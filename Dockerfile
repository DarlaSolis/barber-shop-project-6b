# Build stage for PHP / Laravel dependencies & Node assets
FROM php:8.2-fpm-alpine as base

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    zip \
    unzip \
    libzip-dev \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    git \
    nodejs \
    npm

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring gd xml bcmath intl zip

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies ignoring platform reqs for zip extension compatibility
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

# Install NPM dependencies & build frontend assets
RUN npm ci && npm run build

# Fix permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
