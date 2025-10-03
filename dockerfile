# Utiliser l'image officielle PHP avec Apache
FROM php:8.3-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl vim \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Activer les modules Apache nécessaires
RUN a2enmod rewrite ssl headers

# Générer un certificat SSL auto-signé pour le développement
RUN mkdir -p /etc/apache2/ssl && \
    openssl req -x509 -nodes -days 365 \
    -subj "/C=FR/ST=Paris/L=Paris/O=Dev/OU=Dev/CN=localhost" \
    -newkey rsa:2048 \
    -keyout /etc/apache2/ssl/apache.key \
    -out /etc/apache2/ssl/apache.crt

# Adapter Apache pour pointer vers Symfony "public/"
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Définir le répertoire de travail
WORKDIR /var/www/html


###> recipes ###
###< recipes ###