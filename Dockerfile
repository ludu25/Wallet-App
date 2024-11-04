FROM php:8.2-fpm

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

EXPOSE 10000

COPY . .

RUN apt-get update && apt-get install -y nginx
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Nginx and PHP-FPM
COPY conf/nginx/nginx-site.conf /etc/nginx/sites-available/default
RUN echo "listen = 0.0.0.0:9000" >> /usr/local/etc/php-fpm.d/www.conf

COPY scripts/00-laravel-deploy.sh /scripts/00-laravel-deploy.sh
RUN chmod +x /scripts/00-laravel-deploy.sh
RUN /scripts/00-laravel-deploy.sh

# CMD ["/scripts/00-laravel-deploy.sh", "&&", "/start.sh"]
CMD ["/bin/sh", "-c", "/scripts/00-laravel-deploy.sh && /usr/sbin/nginx && php-fpm"]