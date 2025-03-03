# Use PHP 8.2 CLI as the base image
FROM php:8.2-cli

# Set the working directory
WORKDIR /app

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install necessary packages and enable PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    && docker-php-ext-enable opcache \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && composer global require phpunit/phpunit

# Configure Xdebug
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=coverage,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Add Composer's global bin directory to PATH
ENV PATH="/root/.composer/vendor/bin:${PATH}"

# Set the default command
CMD ["php", "-a"]
