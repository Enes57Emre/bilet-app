FROM php:8.2-apache

RUN apt-get update && apt-get install -y git unzip && rm -rf /var/lib/apt/lists/*

# Enable PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app
COPY . /var/www/html

# Install PHP deps (dompdf)
RUN composer install --no-interaction --prefer-dist || composer require dompdf/dompdf:^2.0 --no-interaction --prefer-dist

# Permissions
RUN chown -R www-data:www-data /var/www/html
