FROM iagapie/alpine-3.5:0.1

LABEL maintainer="Igor Agapie <igoragapie@gmail.com>"

RUN apk upgrade -U --no-cache \
	&& apk add -U --no-cache \
        nodejs \
        php5 \
        php5-mssql \
        php5-opcache \
        php5-pdo_dblib \
        php5-sysvmsg \
        php5-sysvsem \
        php5-sysvshm \
        php5-wddx \
        php5-xml \
        php5-xmlreader \
        php5-xmlrpc \
        php5-xsl \
        php5-zip \
        php5-zlib \
        php5-odbc \
        php5-openssl \
        php5-pcntl \
        php5-pdo \
        php5-pdo_mysql \
        php5-pdo_odbc \
        php5-pdo_pgsql \
        php5-pdo_sqlite \
        php5-pgsql \
        php5-phar \
        php5-posix \
        php5-pspell \
        php5-shmop \
        php5-snmp \
        php5-soap \
        php5-sockets \
        php5-sqlite3 \
        php5-curl \
        php5-dba \
        php5-dom \
        php5-enchant \
        php5-exif \
        php5-ftp \
        php5-gd \
        php5-gettext \
        php5-gmp \
        php5-iconv \
        php5-imap \
        php5-intl \
        php5-json \
        php5-ldap \
        php5-mcrypt \
        php5-mysql \
        php5-mysqli \
        php5-bcmath \
        php5-bz2 \
        php5-calendar \
        php5-cgi \
        php5-common \
        php5-ctype \
        php5-dev \
        php5-doc \
        php5-embed \
        php5-fpm \
        php5-pear \
        php5-phpdbg \
        php5-dbg \
        php5-phpmailer \
        php5-mailparse \
        php5-xcache \
        php5-memcache \
        php5-apcu \
        php5-imagick \
        php5-xdebug \

    && rm -rf /tmp/* /var/cache/apk/* \

    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && ln -s $(composer config --global home) /root/composer \
    && composer global require "hirak/prestissimo:0.3.7" \

    && wget https://git.io/psysh \
    && chmod +x psysh \
    && mv psysh /usr/bin/psysh \

    && npm install --global yarn

COPY rootfs /

ENV PATH $PATH:/root/composer/vendor/bin

RUN HOST_IP="$(/sbin/ip route|awk '/default/ { print $3 }')" \
    && sed -i "$ a\xdebug.remote_host=${HOST_IP}" /etc/php5/conf.d/xdebug.ini

WORKDIR /app

EXPOSE 20 21 22 9000 9009 10090-10100

CMD ["supervisord", "--nodaemon", "--configuration", "/etc/supervisor/conf.d/supervisord.conf"]