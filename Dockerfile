FROM php:8.5-fpm-bookworm

ARG CUSTOM_CA_CERT_B64=
ARG HTTP_PROXY=
ARG HTTPS_PROXY=
ARG NO_PROXY=

ENV HTTP_PROXY=${HTTP_PROXY} \
	HTTPS_PROXY=${HTTPS_PROXY} \
	NO_PROXY=${NO_PROXY} \
	http_proxy=${HTTP_PROXY} \
	https_proxy=${HTTPS_PROXY} \
	no_proxy=${NO_PROXY}

RUN set -eux; \
	if [ -n "$CUSTOM_CA_CERT_B64" ]; then \
		echo "$CUSTOM_CA_CERT_B64" | base64 -d > /usr/local/share/ca-certificates/custom-root-ca.crt; \
		update-ca-certificates; \
	fi; \
	if [ -n "$HTTP_PROXY" ] || [ -n "$HTTPS_PROXY" ]; then \
		{ \
			[ -n "$HTTP_PROXY" ] && echo "Acquire::http::Proxy \"$HTTP_PROXY\";"; \
			[ -n "$HTTPS_PROXY" ] && echo "Acquire::https::Proxy \"$HTTPS_PROXY\";"; \
		} > /etc/apt/apt.conf.d/01proxy; \
	fi

RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		ca-certificates \
		git \
		unzip \
		libzip-dev \
		libonig-dev \
		libicu-dev \
		libpng-dev \
		libjpeg62-turbo-dev \
		libfreetype6-dev \
		default-mysql-client \
		nginx \
		supervisor \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring zip intl gd pcntl \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# Abhängigkeiten zuerst installieren (Layer-Cache bleibt bei Codeänderungen erhalten)
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .

# Autoloader + package:discover. Laravel bootet dabei die Config,
# spatie/laravel-backup verlangt eine gültige MAIL_FROM_ADDRESS.
RUN MAIL_FROM_ADDRESS=build@example.com composer dump-autoload --optimize \
	&& mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
		storage/framework/views storage/framework/testing storage/logs storage/debugbar bootstrap/cache \
	&& chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8080

ENTRYPOINT ["entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]