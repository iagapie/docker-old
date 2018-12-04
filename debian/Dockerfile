FROM php:7.2-fpm

MAINTAINER Igor Agapie <igoragapie@gmail.com>

ENV ROOT_PASSWD root

RUN set -ex \
	&& groupmod -g 1000 www-data \
	&& usermod -u 1000 www-data \
	&& chsh -s /bin/bash www-data \
	&& chown -R www-data:www-data /var/www \
	&& echo "root:$ROOT_PASSWD"|chpasswd

RUN apt-get update && apt-get upgrade -y

RUN set -ex \
	&& mkdir -p /usr/share/man/man1 \
	&& mkdir -p /usr/share/man/man7 \
	&& apt-get install -y \
		apt-utils \
		bash-completion \
		wget \
		lsof \
		nano \
		unzip \
		rsync \
		openssh-client \
		ftp \
		psmisc \
		iputils-ping \
		net-tools \
		netcat \
		gnupg \
		mysql-client \
		postgresql-client \
		snmp \
	--no-install-recommends

RUN apt-get install -y \
		libc-client-dev \
		libicu-dev \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libxslt1-dev \
		libmemcached-dev \
		zlib1g-dev \
		libmcrypt-dev \
		unixodbc-dev \
		libssh2-1-dev \
		libzip-dev \
		libenchant-dev \
		libgmp-dev \
		libkrb5-dev \
		libpq-dev \
		libsnmp-dev \
		libldb-dev \
		libldap2-dev \
		libsqlite3-dev \
		libbz2-dev \
	--no-install-recommends

RUN set -ex \
	&& apt-get autoremove -y \
	&& rm -r /var/lib/apt/lists/*

RUN set -ex \
	&& docker-php-source extract \
	&& { \
        echo '# https://github.com/docker-library/php/issues/103#issuecomment-271413933'; \
        echo 'AC_DEFUN([PHP_ALWAYS_SHARED],[])dnl'; \
        echo; \
        cat /usr/src/php/ext/odbc/config.m4; \
    } > temp.m4 \
    && mv temp.m4 /usr/src/php/ext/odbc/config.m4 \
	&& docker-php-ext-install -j$(nproc) \
		iconv \
		bcmath \
		opcache \
		snmp \
		intl \
		soap \
		xsl \
		zip \
		bz2 \
		calendar \
		dba \
		enchant \
		exif \
		gettext \
		gmp \
		mysqli \
		pgsql \
		pdo_mysql \
		pdo_pgsql \
		pcntl \
		sockets \
		wddx \
		xmlrpc \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
	&& docker-php-ext-install -j$(nproc) imap \
	&& docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
	&& docker-php-ext-install -j$(nproc) ldap \
	&& docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
	&& docker-php-ext-install -j$(nproc) pdo_odbc \
	&& docker-php-ext-configure odbc --with-unixODBC=shared,/usr \
	&& docker-php-ext-install -j$(nproc) odbc

RUN set -ex \
	&& pecl install mcrypt-1.0.0 oauth memcached apcu redis ssh2-1 xdebug \
	&& docker-php-ext-enable \
		mcrypt \
        oauth \
        memcached \
        apcu \
        redis \
        ssh2 \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.iniOLD

RUN docker-php-source delete

# Install XDebug

ENV PHP_XDEBUG_REMOTE_HOST localhost
ENV PHP_XDEBUG_REMOTE_PORT 9001
ENV PHP_XDEBUG_IDEKEY PHPSTORM

RUN set -ex \
	&& echo '#!/bin/sh' >> /usr/local/bin/xdebug \
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

RUN set -ex \
	&& echo 'memory_limit = 2048M' >> /usr/local/etc/php/php.ini \
	&& echo 'max_execution_time = 38000' >> /usr/local/etc/php/php.ini \
	&& echo 'always_populate_raw_post_data = -1' >> /usr/local/etc/php/php.ini \
	&& echo 'date.timezone = "UTC"' >> /usr/local/etc/php/php.ini \
	&& echo 'upload_max_filesize = 128M' >> /usr/local/etc/php/php.ini \
	&& echo 'zlib.output_compression = on' >> /usr/local/etc/php/php.ini \
	&& echo 'log_errors = On' >> /usr/local/etc/php/php.ini \
	&& echo 'display_errors = On' >> /usr/local/etc/php/php.ini \
	&& echo 'sendmail_path = "/opt/go/bin/mhsendmail --smtp-addr=${PHP_MAILHOG_HOST}:${PHP_MAILHOG_PORT}"' >> /usr/local/etc/php/php.ini

RUN set -ex \
	&& mkdir -p /root/.composer/cache \
	&& mkdir -p /var/www/.composer/cache \
	&& chmod 777 -Rf /var/www/.composer \
	&& chown -Rf www-data:www-data /var/www/.composer

# Install Composer

RUN	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

RUN composer global require hirak/prestissimo

ENV PATH="/root/.composer/vendor/bin:${PATH}"

USER www-data

RUN composer global require hirak/prestissimo

ENV PATH="/var/www/.composer/vendor/bin:${PATH}"

USER root
