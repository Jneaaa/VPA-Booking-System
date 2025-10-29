FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    ca-certificates

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd pdo_mysql mbstring zip xml exif

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Create ssl directory and copy Aiven certificate
RUN mkdir -p /etc/ssl/certs/aiven
COPY ssl/ca.pem /etc/ssl/certs/aiven/aiven-ca.pem
RUN chmod 644 /etc/ssl/certs/aiven/aiven-ca.pem

# Create .docker directory and Apache config
RUN mkdir -p .docker
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copy application files
COPY . .

# Copy production environment file
COPY .env.production .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache

# Generate key and cache config
RUN php artisan config:cache && php artisan route:cache

EXPOSE 80
CMD ["apache2-foreground"]