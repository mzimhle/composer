# Image we are using as well as the version
FROM php:7.4-apache
# Set up our environmental variables
ENV MYSQL_ROOT_USER=root
ENV MYSQL_ROOT_PASSWORD=""
# Copy the src library files for the site to the container.
COPY site/src /var/www/html/src
COPY site/public /var/www/html/public
# Bring composer binary into the PHP container. Basically installing composer to your image. This is version 2.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY site/composer.json /var/www/html/composer.json
COPY site/composer.lock /var/www/html/composer.lock
# Define our apache configuration file, copying it to the relevant path
COPY 000-default.conf /etc/apache2/sites-enabled/000-default.conf
# Run commands for this Dockerfile
# Update apt-get then install unzip and zip
# Install composer packages from the composer.json file
RUN apt-get update && apt-get install -y \
    unzip \
    zip && composer install
RUN docker-php-ext-install mysqli pdo pdo_mysql
# Expose the port 80 for this image
EXPOSE 80