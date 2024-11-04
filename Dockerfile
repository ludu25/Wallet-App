FROM richarvey/nginx-php-fpm:1.7.2

# Instale extensões PHP necessárias para Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Configure o diretório de trabalho
WORKDIR /var/www/html

COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Instale o Composer e as dependências do Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --optimize-autoloader --no-dev

# Defina as permissões
RUN chmod -R 775 storage bootstrap/cache

# Exponha a porta
EXPOSE 9000

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1
CMD ["/start.sh"]