# Use PHP 8.2 with Apache
FROM php:8.2-apache

# 1. Install the mysqli extension for database connection
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# 2. Enable Apache rewrite module (important for clean URLs)
RUN a2enmod rewrite

# 3. Copy your project files into the container
COPY . /var/www/html/

# 4. Set correct permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# 5. Expose port 80 for the web traffic
EXPOSE 80
