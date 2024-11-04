FROM richarvey/nginx-php-fpm:latest

# Configure o diretório de trabalho
WORKDIR /var/www/html

COPY . .

# Copia o arquivo do supervisor para o contêiner
COPY conf/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

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

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]