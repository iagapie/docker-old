FROM alpine:3.8 as base

MAINTAINER Igor Agapie <igoragapie@gmail.com>

ENV ROOT_PASSWD root

# persistent / runtime deps
RUN apk add --no-cache --virtual .persistent-deps \
		ca-certificates \
		wget \
		curl \
		gnupg \
		tar \
		xz \
		zip \
		unzip \
		git \
		go \
# https://github.com/docker-library/php/issues/494
		libressl \
		openssl \
		openssh-client \
		util-linux \
		shadow \
		lsof \
		musl-dev \
		vim \
		nano \
		sudo \
		supervisor

# ensure www-data user exists
RUN set -x \
	&& echo "root:$ROOT_PASSWD"|chpasswd \
	&& addgroup -g 82 -S www-data \
	&& adduser -u 82 -D -S -G www-data www-data \
# 82 is the standard uid/gid for "www-data" in Alpine
# http://git.alpinelinux.org/cgit/aports/tree/main/apache2/apache2.pre-install?h=v3.3.2
# http://git.alpinelinux.org/cgit/aports/tree/main/lighttpd/lighttpd.pre-install?h=v3.3.2
# http://git.alpinelinux.org/cgit/aports/tree/main/nginx-initscripts/nginx-initscripts.pre-install?h=v3.3.2
	&& echo "www-data ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/www-data \
    && chmod 0440 /etc/sudoers.d/www-data \
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

# CMD ["/usr/local/bin/supercronic", "/etc/crontab"]

FROM base as php-fpm-7.3

# dependencies required for running "phpize"
# these get automatically installed and removed by "docker-php-ext-*" (unless they're already installed)
ENV PHPIZE_DEPS \
		autoconf \
		dpkg-dev dpkg \
		file \
		g++ \
		gcc \
		libc-dev \
		make \
		pkgconf \
		re2c

ENV PHP_INI_DIR /usr/local/etc/php
RUN mkdir -p $PHP_INI_DIR/conf.d

ENV PHP_EXTRA_CONFIGURE_ARGS --enable-fpm --with-fpm-user=www-data --with-fpm-group=www-data --disable-cgi

# Apply stack smash protection to functions using local buffers and alloca()
# Make PHP's main executable position-independent (improves ASLR security mechanism, and has no performance impact on x86_64)
# Enable optimization (-O2)
# Enable linker optimization (this sorts the hash buckets to improve cache locality, and is non-default)
# Adds GNU HASH segments to generated executables (this is used if present, and is much faster than sysv hash; in this configuration, sysv hash is also generated)
# https://github.com/docker-library/php/issues/272
ENV PHP_CFLAGS="-fstack-protector-strong -fpic -fpie -O2"
ENV PHP_CPPFLAGS="$PHP_CFLAGS"
ENV PHP_LDFLAGS="-Wl,-O1 -Wl,--hash-style=both -pie"

ENV GPG_KEYS 1729F83938DA44E27BA0F4D3DBDB397470D12172 B1B44D8F021E4E2D6021E995DC9FF8D3EE5AF27F

ENV PHP_VERSION 7.2.13
ENV PHP_URL="https://secure.php.net/get/php-7.2.13.tar.xz/from/this/mirror" PHP_ASC_URL="https://secure.php.net/get/php-7.2.13.tar.xz.asc/from/this/mirror"
ENV PHP_SHA256="14b0429abdb46b65c843e5882c9a8c46b31dfbf279c747293b8ab950c2644a4b" PHP_MD5=""

RUN set -xe; \
	\
	mkdir -p /usr/src; \
	cd /usr/src; \
	\
	wget -O php.tar.xz "$PHP_URL"; \
	\
	if [ -n "$PHP_SHA256" ]; then \
		echo "$PHP_SHA256 *php.tar.xz" | sha256sum -c -; \
	fi; \
	if [ -n "$PHP_MD5" ]; then \
		echo "$PHP_MD5 *php.tar.xz" | md5sum -c -; \
	fi; \
	\
	if [ -n "$PHP_ASC_URL" ]; then \
		wget -O php.tar.xz.asc "$PHP_ASC_URL"; \
		export GNUPGHOME="$(mktemp -d)"; \
		for key in $GPG_KEYS; do \
			gpg --batch --keyserver ha.pool.sks-keyservers.net --recv-keys "$key"; \
		done; \
		gpg --batch --verify php.tar.xz.asc php.tar.xz; \
		command -v gpgconf > /dev/null && gpgconf --kill all; \
		rm -rf "$GNUPGHOME"; \
	fi

COPY docker-php-ext-* docker-php-source docker-php-entrypoint /usr/local/bin/

RUN set -xe \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		argon2-dev \
		coreutils \
		curl-dev \
		libedit-dev \
		libressl-dev \
		libsodium-dev \
		libxml2-dev \
		sqlite-dev \
		freetype-dev \
		libjpeg-turbo-dev \
		libpng-dev \
		icu-dev \
		libmcrypt-dev \
		libxml2-dev \
		libxslt-dev \
		pcre-dev \
		libmemcached-dev \
		unixodbc-dev \
		libssh2-dev \
		bzip2-dev \
		enchant-dev \
		gmp-dev \
		imap-dev \
		postgresql-dev \
		net-snmp-dev \
		openldap-dev \
		libzip-dev \
	\
	&& export CFLAGS="$PHP_CFLAGS" \
		CPPFLAGS="$PHP_CPPFLAGS" \
		LDFLAGS="$PHP_LDFLAGS" \
	&& docker-php-source extract \
	&& { \
        echo '# https://github.com/docker-library/php/issues/103#issuecomment-271413933'; \
        echo 'AC_DEFUN([PHP_ALWAYS_SHARED],[])dnl'; \
        echo; \
        cat /usr/src/php/ext/odbc/config.m4; \
    } > temp.m4 \
    && mv temp.m4 /usr/src/php/ext/odbc/config.m4 \
	&& cd /usr/src/php \
	&& gnuArch="$(dpkg-architecture --query DEB_BUILD_GNU_TYPE)" \
	&& ./configure \
		--build="$gnuArch" \
		--with-config-file-path="$PHP_INI_DIR" \
		--with-config-file-scan-dir="$PHP_INI_DIR/conf.d" \
		\
# make sure invalid --configure-flags are fatal errors intead of just warnings
		--enable-option-checking=fatal \
		\
# https://github.com/docker-library/php/issues/439
		--with-mhash \
		\
# --enable-ftp is included here because ftp_ssl_connect() needs ftp to be compiled statically (see https://github.com/docker-library/php/issues/236)
		--enable-ftp \
# --enable-mbstring is included here because otherwise there's no way to get pecl to use it properly (see https://github.com/docker-library/php/issues/195)
		--enable-mbstring \
# --enable-mysqlnd is included here because it's harder to compile after the fact than extensions are (since it's a plugin for several extensions, not an extension in itself)
		--enable-mysqlnd \
# https://wiki.php.net/rfc/argon2_password_hash (7.2+)
		--with-password-argon2 \
# https://wiki.php.net/rfc/libsodium
		--with-sodium=shared \
		\
		--with-curl \
		--with-libedit \
		--with-openssl \
		--with-zlib \
		\
# bundled pcre does not support JIT on s390x
# https://manpages.debian.org/stretch/libpcre3-dev/pcrejit.3.en.html#AVAILABILITY_OF_JIT_SUPPORT
		$(test "$gnuArch" = 's390x-linux-gnu' && echo '--without-pcre-jit') \
		\
		$PHP_EXTRA_CONFIGURE_ARGS \
	&& make -j "$(nproc)" \
	&& make install \
	&& { find /usr/local/bin /usr/local/sbin -type f -perm +0111 -exec strip --strip-all '{}' + || true; } \
	&& make clean \
	\
# https://github.com/docker-library/php/issues/692 (copy default example "php.ini" files somewhere easily discoverable)
	&& cp -v php.ini-* "$PHP_INI_DIR/" \
	\
	&& cd / \
	\
	&& docker-php-ext-install \
	  	snmp \
	  	opcache \
	  	bcmath \
	  	intl \
	  	pdo_mysql \
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
	  	imap \
	  	mysqli \
	  	pcntl \
	  	pdo_pgsql \
	  	pgsql \
	  	sockets \
	  	wddx \
	  	xmlrpc \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install gd \
	&& docker-php-ext-configure ldap --with-libdir=lib/ \
    && docker-php-ext-install ldap \
    && docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
	&& docker-php-ext-install pdo_odbc \
	&& docker-php-ext-configure odbc --with-unixODBC=shared,/usr \
	&& docker-php-ext-install odbc \
	&& pecl install mcrypt-1.0.1 oauth memcached apcu redis ssh2-1 xdebug \
	&& docker-php-ext-enable \
		mcrypt \
        oauth \
        memcached \
        apcu \
        redis \
        ssh2 \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.iniOLD \
    \
	&& docker-php-source delete \
	\
	&& runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)" \
	&& apk add --no-cache --virtual .php-rundeps $runDeps \
	\
	&& apk del .build-deps \
	\
# https://github.com/docker-library/php/issues/443
	&& pecl update-channels \
	&& rm -rf /tmp/pear ~/.pearrc

# sodium was built as a shared module (so that it can be replaced later if so desired), so let's enable it too (https://github.com/docker-library/php/issues/598)
RUN docker-php-ext-enable sodium

# Install XDebug

ENV PHP_XDEBUG_REMOTE_HOST localhost
ENV PHP_XDEBUG_REMOTE_PORT 9001
ENV PHP_XDEBUG_IDEKEY PHPSTORM

RUN echo '#!/bin/sh' >> /usr/local/bin/xdebug \
	 && echo 'set -e' >> /usr/local/bin/xdebug \
	 && echo '' >> /usr/local/bin/xdebug \
	 && echo 'if [ -s "/usr/local/etc/php/conf.d/xdebug.ini" ]; then' >> /usr/local/bin/xdebug \
	 && echo '    mv /usr/local/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.iniOLD \' >> /usr/local/bin/xdebug \
	 && echo '    && /usr/bin/supervisorctl restart php-fpm \' >> /usr/local/bin/xdebug \
	 && echo '    && echo "========= XDebug was disabled ========="' >> /usr/local/bin/xdebug \
	 && echo 'else' >> /usr/local/bin/xdebug \
	 && echo '    mv /usr/local/etc/php/conf.d/xdebug.iniOLD /usr/local/etc/php/conf.d/xdebug.ini \' >> /usr/local/bin/xdebug \
	 && echo '    && /usr/bin/supervisorctl restart php-fpm \' >> /usr/local/bin/xdebug \
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

RUN set -xe \
	&& mkdir -p /root/.composer/cache \
	&& mkdir -p /home/www-data/.composer/cache \
	&& chmod 777 -Rf /home/www-data/.composer \
	&& chown -Rf www-data:www-data /home/www-data/.composer \
	&& sed -i \
		-e 's/loglevel=info/loglevel=debug/' \
		-e 's/;nodaemon=false/nodaemon=true/' \
		-e 's/;pidfile/pidfile/' \
		/etc/supervisord.conf \
	&& mkdir -p /etc/supervisor.d \
	&& echo '[program:php-fpm]' >> /etc/supervisor.d/php-fpm.ini \
	&& echo 'command=/usr/local/sbin/php-fpm' >> /etc/supervisor.d/php-fpm.ini \
	&& echo 'autostart=true' >> /etc/supervisor.d/php-fpm.ini

ENTRYPOINT ["docker-php-entrypoint"]

WORKDIR /var/www/html

RUN set -ex \
	&& cd /usr/local/etc \
	&& if [ -d php-fpm.d ]; then \
		# for some reason, upstream's php-fpm.conf.default has "include=NONE/etc/php-fpm.d/*.conf"
		sed 's!=NONE/!=!g' php-fpm.conf.default | tee php-fpm.conf > /dev/null; \
		cp php-fpm.d/www.conf.default php-fpm.d/www.conf; \
	else \
		# PHP 5.x doesn't use "include=" by default, so we'll create our own simple config that mimics PHP 7+ for consistency
		mkdir php-fpm.d; \
		cp php-fpm.conf.default php-fpm.d/www.conf; \
		{ \
			echo '[global]'; \
			echo 'include=etc/php-fpm.d/*.conf'; \
		} | tee php-fpm.conf; \
	fi \
	&& { \
		echo '[global]'; \
		echo 'error_log = /proc/self/fd/2'; \
		echo; \
		echo '[www]'; \
		echo '; if we send this to /proc/self/fd/1, it never appears'; \
		echo 'access.log = /proc/self/fd/2'; \
		echo; \
		echo 'clear_env = no'; \
		echo; \
		echo '; Ensure worker stdout and stderr are sent to the main error log.'; \
		echo 'catch_workers_output = yes'; \
		echo 'decorate_workers_output = no'; \
	} | tee php-fpm.d/docker.conf \
	&& { \
		echo '[global]'; \
		echo 'daemonize = no'; \
		echo; \
		echo '[www]'; \
		echo 'listen = 9000'; \
	} | tee php-fpm.d/zz-docker.conf

EXPOSE 9000

CMD ["supervisord"]