FROM php:8.2-apache

# Instalar extensiones necesarias para CodeIgniter 4
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install intl mbstring mysqli pdo pdo_mysql zip

# Habilitar mod_rewrite en Apache (CodeIgniter lo necesita)
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuración de Apache para CodeIgniter
WORKDIR /var/www/html
COPY . /var/www/html

# Permitir .htaccess
RUN chown -R www-data:www-data /var/www/html
