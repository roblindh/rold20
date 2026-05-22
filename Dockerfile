FROM php:8.1-apache

# Enable Apache modules
RUN a2enmod rewrite \
    && a2enmod headers

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable mysqli pdo_mysql

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Install composer (optional, for future use)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Expose port
EXPOSE 80

CMD ["apache2-foreground"]
