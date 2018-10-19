FROM php:7.0-apache

MAINTAINER Igor Agapie <igoragapie@gmail.com>

# Install System Dependencies

RUN apt-get update \
	&& DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
	software-properties-common \
	&& apt-get update \
	&& DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
	build-essential \
	libfreetype6-dev \
	libicu-dev \
  	libssl-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	libmcrypt-dev \
	libedit-dev \
	libedit2 \
	libxslt1-dev \
	libpcre3 \
  	libpcre3-dev \
	apt-utils \
	gnupg \
	git \
	vim \
	wget \
	curl \
	lynx \
	psmisc \
	unzip \
	tar \
	cron \
	bash-completion \
	openssl \
	openssh-server \
	ftp \
	iputils-ping \
	net-tools \
	netcat \
	lsof \
	nano \
	&& apt-get clean \
	&& sed -i "s/#PermitRootLogin prohibit-password/PermitRootLogin yes/" /etc/ssh/sshd_config

# Configuring system

ENV ROOT_PASSWD root

RUN mkdir -p /root/.composer/cache \
	&& mkdir -p /var/www/.composer/cache \
	&& chmod 777 -Rf /var/www /var/www/.* \
	&& chown -Rf www-data:www-data /var/www /var/www/.* \
	&& usermod -u 1000 www-data \
	&& chsh -s /bin/bash www-data \
	&& a2enmod ssl \
	&& a2enmod rewrite \
	&& a2enmod headers \
	&& echo "root:$ROOT_PASSWD"|chpasswd

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

RUN docker-php-ext-configure \
  	gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/; \
  	docker-php-ext-install \
  	opcache \
  	gd \
  	bcmath \
  	intl \
  	mbstring \
  	mcrypt \
  	pdo_mysql \
  	soap \
  	xsl \
  	zip \
  	iconv

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

# Install oAuth

RUN pecl install oauth \
  	&& echo "extension=oauth.so" > /usr/local/etc/php/conf.d/docker-php-ext-oauth.ini

# Install Node, NPM, Grunt and Yarn

RUN curl -sL https://deb.nodesource.com/setup_8.x | bash - \
  	&& apt-get install -y nodejs \
  	&& apt-get clean \
    && npm i -g grunt-cli yarn

# Install Composer

RUN	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

USER www-data

RUN composer global require hirak/prestissimo

USER root

ENV PATH="/var/www/.composer/vendor/bin:/root/.composer/vendor/bin:${PATH}"

# Install XDebug

ENV PHP_XDEBUG_REMOTE_HOST localhost
ENV PHP_XDEBUG_REMOTE_PORT 9000
ENV PHP_XDEBUG_IDEKEY PHPSTORM

RUN yes | pecl install xdebug \
	 && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.iniOLD \
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

# Install Mhsendmail

RUN DEBIAN_FRONTEND=noninteractive apt-get -y install golang-go && apt-get clean \
   && mkdir /opt/go \
   && export GOPATH=/opt/go \
   && go get github.com/mailhog/mhsendmail

# Install Code Sniffer

USER www-data

RUN git clone https://github.com/magento/marketplace-eqp.git ~/.composer/vendor/magento/marketplace-eqp
RUN cd ~/.composer/vendor/magento/marketplace-eqp && composer install

USER root

RUN ln -s /var/www/.composer/vendor/magento/marketplace-eqp/vendor/bin/phpcs /usr/local/bin

# Install Magerun 2

RUN wget https://files.magerun.net/n98-magerun2.phar \
	&& chmod +x ./n98-magerun2.phar \
	&& mv ./n98-magerun2.phar /usr/local/bin/ \
	&& ln -s /usr/local/bin/n98-magerun2.phar /usr/local/bin/n98

RUN curl -o /etc/bash_completion.d/m2install-bash-completion https://raw.githubusercontent.com/yvoronoy/m2install/master/m2install-bash-completion
RUN curl -o /etc/bash_completion.d/n98-magerun2.phar.bash https://raw.githubusercontent.com/netz98/n98-magerun2/master/res/autocompletion/bash/n98-magerun2.phar.bash

RUN echo "alias n98='n98-magerun2.phar'\nalias magerun='n98-magerun2.phar'\nalias mage='php -d memory_limit=-1 -f bin/magento'\nalias magento='php -d memory_limit=-1 -f bin/magento'\nsource /etc/bash_completion" >> /root/.bashrc \
	&& echo "alias n98='n98-magerun2.phar'\nalias magerun='n98-magerun2.phar'\nalias mage='php -d memory_limit=-1 -f bin/magento'\nalias magento='php -d memory_limit=-1 -f bin/magento'\nsource /etc/bash_completion" >> /var/www/.bashrc

EXPOSE 22 80 443
