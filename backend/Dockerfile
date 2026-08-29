FROM php:8.4-fpm-alpine

# Install system dependencies & Nginx & Supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-client \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    freetype-dev \
    jpeg-dev \
    nodejs \
    npm

# Install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql mbstring gd zip bcmath

# Configure working directory
WORKDIR /var/www/html

# Copy application files and Nginx config
COPY . /var/www/html
COPY nginx.conf /etc/nginx/http.d/default.conf

# Install Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js websocket dependencies
RUN cd websocket && npm install --production

# Create storage directory structure and set permissions
RUN mkdir -p storage/logs storage/framework/views storage/framework/sessions storage/framework/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
# Copy entrypoint script and set executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80 3000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/var/www/html/supervisord.conf"]
