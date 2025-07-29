FROM php:8.4-apache

ARG DEBIAN_FRONTEND=noninteractive
ARG WWWGROUP=sail

WORKDIR /var/www/html

# Instala dependencias de sistema, libs para extensiones necesarias, composer y configurar grupo de usuario
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    build-essential curl dialog git htop libbz2-dev libcurl4-openssl-dev \
    libfreetype6-dev libicu-dev libjpeg-dev libonig-dev libpng-dev \
    libpq-dev libxml2-dev libzip-dev locales nano unzip vim wget zip && \
    docker-php-ext-configure zip && \
    pecl install xdebug && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j"$(nproc)" \
    pdo_mysql zip pdo_pgsql pgsql \
    gd bcmath exif pcntl xml soap mbstring bz2 intl ftp fileinfo && \
    a2enmod rewrite && \
    rm -rf /var/lib/apt/lists/* && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    groupadd --force -g "${WWWGROUP}" sail && \
    useradd -ms /bin/bash --no-user-group -g "${WWWGROUP}" -u 1337 sail

# Crear las carpetas necesarias con los permisos correctos ANTES de cambiar de usuario
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views vendor && \
    chown -R sail:sail /var/www/html

# Cambiar al usuario sail ANTES de copiar archivos
USER sail

# Copia solo los archivos de dependencias primero para aprovechar cache de Docker
COPY --chown=sail:sail composer.json composer.lock ./

# Instalación de dependencias de la aplicación (sin scripts para evitar errores)
RUN composer update --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# Copia el resto de archivos de la aplicación
COPY --chown=sail:sail . .

# Cambiar temporalmente a root para configurar permisos y Apache
USER root

# Ejecutar los scripts de Composer y configurar Apache
RUN composer run-script post-autoload-dump && \
    chown -R sail:sail /var/www/html && \
    chmod -R 775 /var/www/html && \
    chmod -R 775 storage bootstrap/cache vendor && \
    sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
