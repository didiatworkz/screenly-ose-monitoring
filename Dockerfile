FROM php:7.4.26-apache-buster
LABEL maintainer="github@atworkz.de"

# Install dependencies
RUN apt update \
  && apt install curl cron wget locales git-core zip libzip-dev zlib1g-dev libpng-dev libssh2-1-dev libssh2-1 -y --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* \
  && rm -r /var/cache/apt \
  && pecl install ssh2-1.3.1

# Setup server
RUN docker-php-ext-install pdo zip gd \
  && docker-php-ext-enable ssh2 \
  && apt remove zip libzip-dev zlib1g-dev libpng-dev -y

# Generate php config
RUN echo "en_US.UTF-8 UTF-8\nde_DE.UTF-8 UTF-8\n" >> /etc/locale.gen \
  && locale-gen

RUN echo "memory_limit=128M\n\
  max_execution_time=600\n\
  post_max_size=10M\n\
  upload_max_filesize=4M\n\
  date.timezone=Europe/Berlin" > /usr/local/etc/php/conf.d/somo.ini

RUN echo "error_log=/var/www/php_error.log\n\
  error_reporting=E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT\n\
  display_errors=Off\n\
  log_errors=On" > /usr/local/etc/php/conf.d/error.ini

WORKDIR /var/www/html

COPY . /var/www/html
COPY assets/tools/crontab /etc/cron.d/somo
COPY assets/tools/somo /usr/bin/somo
RUN chmod 0644 /etc/cron.d/somo 
RUN chmod 0755 /usr/bin/somo
RUN chmod 0755 /var/www/html/entrypoint.sh

ENTRYPOINT /var/www/html/entrypoint.sh