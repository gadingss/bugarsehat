FROM php:8.1-apache

# Install required composer extensions
RUN apt-get update && apt-get install -qqy libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev cron libaio-dev libmcrypt-dev libzip-dev libpq-dev unzip --no-install-recommends
RUN apt-get install supervisor cron tzdata -qqy 
RUN docker-php-ext-install zip mysqli pdo pdo_mysql
RUN apt-get install git libfontconfig1 libxrender1 -y 
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd opcache zip

# xdebug, if you want to debug
#RUN pecl install xdebug && docker-php-ext-enable xdebug

# PHP composer
RUN curl -sS https://getcomposer.org/installer | php --  --install-dir=/usr/bin --filename=composer
#COPY --from=composer /usr/bin/composer /usr/bin/composer
# apache configurations, mod rewrite
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load


# Copy App Files
COPY . /var/www/html/bugar-sehat

# Install required app files
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux
RUN cd /var/www/html/bugar-sehat && composer install --no-scripts --no-dev
RUN cd /var/www/html/bugar-sehat && cp .env.example .env \
    && chown -R www-data:www-data /var/www/html \
    && composer remove --dev facade/ignition
RUN chmod -R 0777 /var/www/html/bugar-sehat/storage 

RUN cd /var/www/html/bugar-sehat && php generate-image-version.php \
    && php artisan key:generate \
    && php artisan jwt:secret -f \
    && php artisan storage:link 

# detected dubious ownership git
# RUN cd /var/www/html/bugar-sehat \
#     && git config --global --add safe.directory /var/www/html/bugar-sehat \
#     && git config pull.rebase false
#clear Congfiguration Temp
RUN rm -rf /var/www/html/bugar-sehat/docker-compose.yml \
    /var/www/html/bugar-sehat/Dockerfile \
    /var/www/html/bugar-sehat/README.md \
    /var/www/html/bugar-sehat/build.sh \
    /var/www/html/bugar-sehat/repull.sh \
    /var/www/html/bugar-sehat/.env.example \
    /var/www/html/bugar-sehat/dbdump.sh \
    /var/www/html/bugar-sehat/dbfresh.sh \
    /var/www/html/bugar-sehat/services.bat
# Enable mod_rewrite
RUN a2enmod rewrite

COPY --chown=www-data:www-data supervisord.conf /etc/supervisor/conf.d/supervisord.conf
#laravel root add -> public
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

ENV TZ="Asia/Jakarta"


# Copy hello-cron file to the cron.d directory
COPY --chown=www-data:www-data cron-job /etc/cron.d/cron-job

# Give execution rights on the cron job
RUN chmod 0744 /etc/cron.d/cron-job

# Apply cron job
RUN crontab /etc/cron.d/cron-job

# Create the log file to be able to run tail
#RUN touch /var/log/cron-job.log

# Run the command on container startup
#CMD cron && tail -f /var/log/cron-job.log

# Run the command on container startup
CMD [ "/usr/bin/supervisord","-c","/etc/supervisor/supervisord.conf" ]
