FROM php:8.2-fpm

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

COPY . .

# Install Nginx
RUN apt-get update && apt-get install -y nginx

# Configure Nginx and PHP-FPM
COPY nginx.conf /etc/nginx/sites-available/default

# Start Nginx and PHP-FPM
CMD service nginx start && php-fpm