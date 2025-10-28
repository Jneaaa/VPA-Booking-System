FROM php:8.2-apache 
 
WORKDIR /var/www/html 
 
    build-essential libpng-dev libjpeg-dev libfreetype6-dev  
    libonig-dev libxml2-dev zip unzip git curl libzip-dev  
 
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer 
RUN a2enmod rewrite 
 
COPY . . 
RUN composer install --no-dev --optimize-autoloader 
 
RUN chown -R www-data:www-data /var/www/html  
 
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf 
EXPOSE 80 
CMD ["apache2-foreground"] 
