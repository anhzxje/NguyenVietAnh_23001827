FROM php:8.3-cli
RUN docker-php-ext-install pdo_mysql \
    && printf 'upload_max_filesize=4M\npost_max_size=5M\ndisplay_errors=Off\n' > /usr/local/etc/php/conf.d/buoi9.ini
WORKDIR /app
COPY . /app
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app"]
