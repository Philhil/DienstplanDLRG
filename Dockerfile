FROM bitnami/laravel:11.0.3
RUN apt-get update
#RUN apt-get install -y mariadb-server nginx php phpunit php-mysql php-mbstring php-zip php-mcrypt supervisor
RUN apt-get install -y git nginx phpunit php-mysql php-mbstring php-zip php-mcrypt supervisor
COPY ./ /var/www/DienstplanDLRG
WORKDIR /var/www/DienstplanDLRG
#RUN phpenv config-rm xdebug.ini
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN cp .env.testing .env
RUN composer self-update
RUN composer install --no-interaction
RUN php artisan key:generate
EXPOSE 8000
EXPOSE 80
CMD php artisan migrate && php /var/www/DienstplanDLRG/artisan queue:work --tries=3 & php /var/www/DienstplanDLRG/artisan serve --host=0.0.0.0