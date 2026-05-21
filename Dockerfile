FROM php:8.4-apache

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Set ServerName to suppress warnings
RUN echo "ServerName gym-ni-bai.onrender.com" >> /etc/apache2/apache2.conf

# Make Apache use port 10000 (Render default)
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf

# Set Laravel public as document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Allow .htaccess for Laravel
RUN printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (without running post-install scripts)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy the rest of the application
COPY . .

# Create basic .env file for build (will be overridden by Render environment variables)
RUN if [ -f .env.example ]; then \
        cp .env.example .env; \
    else \
        echo "APP_NAME=Laravel" > .env && \
        echo "APP_ENV=production" >> .env && \
        echo "APP_KEY=" >> .env && \
        echo "APP_DEBUG=false" >> .env && \
        echo "APP_URL=http://localhost" >> .env && \
        echo "DB_CONNECTION=mysql" >> .env && \
        echo "DB_HOST=127.0.0.1" >> .env && \
        echo "DB_PORT=3306" >> .env && \
        echo "DB_DATABASE=laravel" >> .env && \
        echo "DB_USERNAME=root" >> .env && \
        echo "DB_PASSWORD=" >> .env && \
        echo "SESSION_DRIVER=database" >> .env; \
    fi

# Generate application key (required for Laravel to work)
RUN php artisan key:generate --no-interaction

# Install frontend dependencies and build assets
RUN npm install && npm run build

# Fix permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views bootstrap/cache public/uploads \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads \
    && chmod -R 775 storage bootstrap/cache public/uploads

# Clear any cached files from local development
RUN rm -rf bootstrap/cache/*.php

# Expose port
EXPOSE 10000

# Create startup script
RUN echo '#!/bin/bash\n\
# Clear caches on startup\n\
php artisan config:clear || true\n\
php artisan route:clear || true\n\
php artisan view:clear || true\n\
php artisan optimize:clear || true\n\
\n\
# Create storage symlink if it doesnt exist\n\
php artisan storage:link || true\n\
\n\
# Start Apache\n\
apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Start with our custom script
CMD ["/usr/local/bin/start.sh"]