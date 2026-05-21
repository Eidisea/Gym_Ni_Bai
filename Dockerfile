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

# Copy Laravel app
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install frontend dependencies and build assets
RUN npm install && npm run build

# Fix permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views bootstrap/cache public/uploads \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads \
    && chmod -R 775 storage bootstrap/cache public/uploads


# Manually wipe any local cache files that snuck in
RUN rm -rf bootstrap/cache/*.php

# # Clear and cache config (with error handling)
# RUN php artisan config:clear || echo "Config clear failed" \
#     && php artisan route:clear || echo "Route clear failed" \
#     && php artisan view:clear || echo "View clear failed" \
#     && php artisan optimize:clear || echo "Optimize clear failed" \
#     && php artisan config:cache || echo "Config cache failed" \
#     && php artisan route:cache || echo "Route cache failed" \
#     && php artisan view:cache || echo "View cache failed"

# Create storage symlink
RUN php artisan storage:link || true

# # (Optional) Run migrations
# RUN php artisan migrate --force || true

# Expose port
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]