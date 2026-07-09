FROM php:8.3-fpm-alpine

# Install system and build dependencies required for Drupal PHP extensions
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    mariadb-client \
    shadow \
    patch \
    $PHPIZE_DEPS

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        opcache \
        pdo_mysql \
        zip \
        intl \
        bcmath

# Install APCu extension (highly recommended for Drupal caching)
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# Clean up build dependencies to keep the image slim
RUN apk del $PHPIZE_DEPS

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Use the development PHP configuration as default
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Set working directory inside the container
WORKDIR /var/www/html

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
