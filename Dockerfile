FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Install Apache and precompiled PHP 8.1 with MySQL, OPcache, XML, and Mbstring
RUN apt-get update && apt-get install -y --no-install-recommends \
    apache2 \
    php8.1 \
    php8.1-mysql \
    php8.1-opcache \
    php8.1-mbstring \
    php8.1-xml \
    libapache2-mod-php8.1 \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Enable required Apache modules
RUN a2enmod rewrite headers speling remoteip

# Configure Apache for Reverse Proxy, HTTPS detection, and Security
RUN echo '<Directory /var/www/html>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    CheckSpelling On\n\
    CheckCaseOnly On\n\
</Directory>\n\
RemoteIPHeader X-Forwarded-For\n\
SetEnvIf X-Forwarded-Proto "^https$" HTTPS=on\n\
Header always set Content-Security-Policy "upgrade-insecure-requests"\n' > /etc/apache2/conf-available/rold20-security.conf \
    && a2enconf rold20-security

# Support custom php.ini from docker/php.ini volume mount
RUN mkdir -p /usr/local/etc/php/conf.d \
    && ln -sf /usr/local/etc/php/conf.d/custom.ini /etc/php/8.1/apache2/conf.d/99-custom.ini

WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Set proper ownership and permissions for the web user
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]
