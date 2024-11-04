FROM php:8.2-fpm

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

COPY . .

# Install Nginx
RUN apt-get update && apt-get install -y nginx

# Configure Nginx and PHP-FPM
COPY conf/nginx/nginx-site.conf /etc/nginx/sites-available/default

RUN echo "listen = 0.0.0.0:9000" >> /usr/local/etc/php-fpm.d/www.conf

# Start Nginx and PHP-FPM
CMD service nginx start && php-fpm