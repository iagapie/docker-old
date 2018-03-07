FROM iagapie/alpine-3.7:0.1

LABEL maintainer="Igor Agapie <igoragapie@gmail.com>"

RUN apk upgrade -U --no-cache \
	&& apk add -U --no-cache \
        nodejs \
        yarn \
        php7 \
	    php7-common \
	    php7-wddx \
	    php7-xml \
	    php7-xmlreader \
	    php7-xmlrpc \
	    php7-xmlwriter \
	    php7-xsl \
	    php7-zip \
	    php7-zlib \
	    php7-session \
	    php7-shmop \
	    php7-simplexml \
	    php7-snmp \
	    php7-soap \
	    php7-sockets \
	    php7-sqlite3 \
	    php7-sysvmsg \
	    php7-sysvsem \
	    php7-sysvshm \
	    php7-tidy \
	    php7-tokenizer \
	    php7-pgsql \
	    php7-phar \
	    php7-posix \
	    php7-pspell \
	    php7-recode \
	    php7-mysqli \
	    php7-mysqlnd \
	    php7-odbc \
	    php7-opcache \
	    php7-openssl \
	    php7-pcntl \
	    php7-pdo \
	    php7-pdo_dblib \
	    php7-pdo_mysql \
	    php7-pdo_odbc \
	    php7-pdo_pgsql \
	    php7-pdo_sqlite \
	    php7-exif \
	    php7-fileinfo \
	    php7-ftp \
	    php7-gd \
	    php7-gettext \
	    php7-gmp \
	    php7-iconv \
	    php7-imap \
	    php7-intl \
	    php7-json \
	    php7-ldap \
	    php7-mbstring \
	    php7-mcrypt \
	    php7-bcmath \
	    php7-bz2 \
	    php7-calendar \
	    php7-cgi \
	    php7-ctype \
	    php7-curl \
	    php7-dba \
	    php7-dom \
	    php7-enchant \
	    php7-fpm \
	    php7-litespeed \
	    php7-pear \
	    php7-dev \
	    php7-doc \
	    php7-embed \
	    php7-phpdbg \
	    php7-memcached \
	    php7-imagick \
	    php7-imagick-dev \
	    php7-ssh2 \
	    php7-xdebug \
	    php7-amqp \
	    php7-mailparse \
	    php7-zmq \
	    php7-apcu \
	    php7-redis \

    && rm -rf /tmp/* /var/cache/apk/* \

    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && ln -s $(composer config --global home) /root/composer \
    && composer global require "hirak/prestissimo:0.3.7" \

    && wget https://git.io/psysh \
    && chmod +x psysh \
    && mv psysh /usr/bin/psysh

COPY rootfs /

ENV PATH $PATH:/root/composer/vendor/bin

RUN HOST_IP="$(/sbin/ip route|awk '/default/ { print $3 }')" \
    && sed -i "$ a\xdebug.remote_host=${HOST_IP}" /etc/php7/conf.d/xdebug.ini

WORKDIR /app

EXPOSE 20 21 22 9000 9009 10090-10100

CMD ["supervisord", "--nodaemon", "--configuration", "/etc/supervisor/conf.d/supervisord.conf"]