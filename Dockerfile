FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Install Apache, PHP, and all extensions directly from official Ubuntu repositories
RUN apt-get update && apt-get install -y --no-install-recommends \
    apache2 \
    php \
    php-cli \
    php-mysql \
    php-mbstring \
    php-xml \
    php-bcmath \
    php-intl \
    php-opcache \
    php-zip \
    php-curl \
    libapache2-mod-php \
    ca-certificates \
    curl \
    git \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Enable required Apache modules
RUN a2enmod rewrite headers remoteip speling deflate expires

# Copy virtual host configuration
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Support custom php.ini from docker/php.ini
RUN mkdir -p /usr/local/etc/php/conf.d \
    && for d in /etc/php/*/apache2/conf.d /etc/php/*/cli/conf.d; do \
           [ -d "$d" ] && ln -sf /usr/local/etc/php/conf.d/99-custom.ini "$d/99-custom.ini"; \
       done

WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Copy and setup entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i -e 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Set ownership and permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
