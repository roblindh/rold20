FROM php:8.2-apache

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions for Laravel 11 and MySQL
RUN docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    xml \
    bcmath \
    intl \
    opcache \
    zip

# Enable Apache modules
RUN a2enmod rewrite headers remoteip speling deflate expires

# Copy virtual host configuration
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Allow overrides in apache2.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Support custom php.ini from docker/php.ini
COPY docker/php.ini /usr/local/etc/php/conf.d/99-custom.ini

WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Copy and setup entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i -e 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Set ownership
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
