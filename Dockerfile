FROM php:8.3-cli-bookworm

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
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring zip intl gd \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/DienstplanDLRG
COPY ./ /var/www/DienstplanDLRG

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
	&& php artisan key:generate \
	&& chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan queue:work --tries=3 & php artisan serve --host=0.0.0.0 --port=8000