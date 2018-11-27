FROM php:7.1-fpm-alpine

MAINTAINER Igor Agapie <igoragapie@gmail.com>

ENV ROOT_PASSWD root

RUN apk add --no-cache --virtual .persistent-deps \
		bash \
		bash-completion \
		musl-dev \
		util-linux \
		shadow \
		openssl \
		nano \
		vim \
		zip \
		unzip \
		git \
		go \
	&& chmod 777 -Rf /var/www /var/www/.* \
	&& chown -Rf www-data:www-data /var/www /var/www/.* \
	&& usermod -u 1000 www-data \
	&& chsh -s /bin/bash www-data \
	&& echo "root:$ROOT_PASSWD"|chpasswd \
	&& mkdir -p /opt/go \
  	&& export GOPATH=/opt/go \
  	&& go get github.com/mailhog/mhsendmail

# |--------------------------------------------------------------------------
# | Supercronic
# |--------------------------------------------------------------------------
# |
# | Supercronic is a drop-in replacement for cron (for containers).
# |

ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.6/supercronic-linux-amd64 \
    SUPERCRONIC=supercronic-linux-amd64 \
    SUPERCRONIC_SHA1SUM=c3b78d342e5413ad39092fd3cfc083a85f5e2b75

RUN curl -fsSLO "$SUPERCRONIC_URL" \
	&& echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
	&& chmod +x "$SUPERCRONIC" \
	&& mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
	&& ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

# Install PHP Extensions

RUN set -xe \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		freetype-dev \
		libjpeg-turbo-dev \
		libpng-dev \
		icu-dev \
		libmcrypt-dev \
		libxml2-dev \
		libxslt-dev \
		pcre-dev \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  	&& docker-php-ext-install \
	  	gd \
	  	opcache \
	  	bcmath \
	  	intl \
	  	mcrypt \
	  	pdo_mysql \
	  	soap \
	  	xsl \
	  	zip \
	&& pecl install oauth \
	&& echo "extension=oauth.so" > /usr/local/etc/php/conf.d/docker-php-ext-oauth.ini \
	&& pecl install xdebug \
	&& echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.iniOLD

# Install XDebug

ENV PHP_XDEBUG_REMOTE_HOST localhost
ENV PHP_XDEBUG_REMOTE_PORT 9001
ENV PHP_XDEBUG_IDEKEY PHPSTORM

RUN echo '#!/bin/sh' >> /usr/local/bin/xdebug \
	 && echo 'set -e' >> /usr/local/bin/xdebug \
	 && echo '' >> /usr/local/bin/xdebug \
	 && echo 'if [ -s "/usr/local/etc/php/conf.d/xdebug.ini" ]; then' >> /usr/local/bin/xdebug \
	 && echo '    mv /usr/local/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.iniOLD \' >> /usr/local/bin/xdebug \
	 && echo '    && /etc/init.d/apache2 force-reload \' >> /usr/local/bin/xdebug \
	 && echo '    && echo "========= XDebug was disabled ========="' >> /usr/local/bin/xdebug \
	 && echo 'else' >> /usr/local/bin/xdebug \
	 && echo '    mv /usr/local/etc/php/conf.d/xdebug.iniOLD /usr/local/etc/php/conf.d/xdebug.ini \' >> /usr/local/bin/xdebug \
	 && echo '    && /etc/init.d/apache2 force-reload \' >> /usr/local/bin/xdebug \
	 && echo '    && echo "========= XDebug was enabled ========="' >> /usr/local/bin/xdebug \
	 && echo 'fi' >> /usr/local/bin/xdebug \
	 && chmod +x /usr/local/bin/xdebug \
	 && echo 'xdebug.default_enable=1' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.remote_autostart=1' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.remote_handler=dbgp' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.profiler_enable=0' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.profiler_output_dir="/var/www/html"' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.remote_connect_back=1' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.cli_color=1' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.var_display_max_depth=10' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.remote_host=${PHP_XDEBUG_REMOTE_HOST}' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.remote_port=${PHP_XDEBUG_REMOTE_PORT}' >> /usr/local/etc/php/conf.d/custom-xdebug.ini \
	 && echo 'xdebug.idekey=${PHP_XDEBUG_IDEKEY}' >> /usr/local/etc/php/conf.d/custom-xdebug.ini

# PHP Configuration

ENV PHP_MAILHOG_HOST mailhog
ENV PHP_MAILHOG_PORT 1025

RUN echo 'memory_limit = 2048M' >> /usr/local/etc/php/php.ini \
	&& echo 'max_execution_time = 38000' >> /usr/local/etc/php/php.ini \
	&& echo 'always_populate_raw_post_data = -1' >> /usr/local/etc/php/php.ini \
	&& echo 'date.timezone = "UTC"' >> /usr/local/etc/php/php.ini \
	&& echo 'upload_max_filesize = 128M' >> /usr/local/etc/php/php.ini \
	&& echo 'zlib.output_compression = on' >> /usr/local/etc/php/php.ini \
	&& echo 'log_errors = On' >> /usr/local/etc/php/php.ini \
	&& echo 'display_errors = On' >> /usr/local/etc/php/php.ini \
	&& echo 'sendmail_path = "/opt/go/bin/mhsendmail --smtp-addr=${PHP_MAILHOG_HOST}:${PHP_MAILHOG_PORT}"' >> /usr/local/etc/php/php.ini
