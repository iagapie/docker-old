FROM iagapie/alpine-3.7:0.1

LABEL maintainer="Igor Agapie <igoragapie@gmail.com>"

ADD https://php.codecasts.rocks/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN apk upgrade -U --no-cache \
	&& apk add -U --no-cache \
    && echo "@edge-main http://dl-cdn.alpinelinux.org/alpine/edge/main" >> /etc/apk/repositories \
    && echo "@edge-community http://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories \
    && echo "@cast https://php.codecasts.rocks/v3.7/php-7.2" >> /etc/apk/repositories \
    && apk add -U --no-cache \
        nodejs \
        yarn@edge-community \
        php7@cast \
	    php7-apcu@cast \
	    php7-bcmath@cast \
	    php7-bz2@cast \
	    php7-calendar@cast \
	    php7-cgi@cast \
	    php7-common@cast \
	    php7-ctype@cast \
	    php7-curl@cast \
	    php7-dba@cast \
	    php7-dev@cast \
	    php7-doc@cast \
	    php7-dom@cast \
	    php7-embed@cast \
	    php7-enchant@cast \
	    php7-exif@cast \
	    php7-fpm@cast \
	    php7-ftp@cast \
	    php7-gd@cast \
	    php7-gettext@cast \
	    php7-gmp@cast \
	    php7-iconv@cast \
	    php7-imagick@cast \
	    php7-imap@cast \
	    php7-intl@cast \
	    php7-json@cast \
	    php7-ldap@cast \
	    php7-libsodium@cast \
	    php7-litespeed@cast \
	    php7-mbstring@cast \
	    php7-memcached@cast \
	    php7-mongodb@cast \
	    php7-mysqli@cast \
	    php7-mysqlnd@cast \
	    php7-odbc@cast \
	    php7-opcache@cast \
	    php7-openssl@cast \
	    php7-pcntl@cast \
	    php7-pdo@cast \
	    php7-pdo_dblib@cast \
	    php7-pdo_mysql@cast \
	    php7-pdo_odbc@cast \
	    php7-pdo_pgsql@cast \
	    php7-pdo_sqlite@cast \
	    php7-pear@cast \
	    php7-pgsql@cast \
	    php7-phar@cast \
	    php7-phpdbg@cast \
	    php7-posix@cast \
	    php7-pspell@cast \
	    php7-redis@cast \
	    php7-session@cast \
	    php7-shmop@cast \
	    php7-snmp@cast \
	    php7-soap@cast \
	    php7-sockets@cast \
	    php7-sodium@cast \
	    php7-sqlite3@cast \
	    php7-ssh2@cast \
	    php7-sysvmsg@cast \
	    php7-sysvsem@cast \
	    php7-sysvshm@cast \
	    php7-tidy@cast \
	    php7-wddx@cast \
	    php7-xdebug@cast \
	    php7-xml@cast \
	    php7-xmlreader@cast \
	    php7-xsl@cast \
	    php7-zip@cast \
	    php7-zlib@cast \

    && ln -s /usr/bin/php7 /usr/bin/php \
    && ln -s /usr/bin/php-cgi7 /usr/bin/php-cgi \
    && ln -s /usr/bin/php-config7 /usr/bin/php-config \
    && ln -s /usr/bin/phpize7 /usr/bin/phpize \

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