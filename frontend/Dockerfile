FROM php:7.2-apache

MAINTAINER Igor Agapie <igoragapie@gmail.com>

# Install System Dependencies

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends software-properties-common \
	&& apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
	apt-utils \
	wget \
	curl \
	unzip \
	tar \
	libicu-dev \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	libxslt1-dev \
	libmemcached-dev \
	zlib1g-dev \
	gnupg \
	cron \
	bash-completion \
	openssl \
	openssh-server \
	ftp \
	iputils-ping \
	net-tools \
	netcat \
	lsof \
	lynx \
	psmisc \
	vim \
	nano \
	&& apt-get clean \
	&& sed -i "s/#PermitRootLogin prohibit-password/PermitRootLogin yes/" /etc/ssh/sshd_config

# Configuring system

ENV ROOT_PASSWD root

RUN sed -i "s#/var/www/html#/var/www/html/public#" /etc/apache2/sites-available/000-default.conf \
	&& mkdir -p /root/.composer/cache \
	&& mkdir -p /var/www/.composer/cache \
	&& mkdir -p /var/www/workspace \
	&& chmod 777 -Rf /var/www /var/www/.* \
	&& chown -Rf www-data:www-data /var/www /var/www/.* \
	&& usermod -u 1000 www-data \
	&& chsh -s /bin/bash www-data \
	&& a2enmod rewrite \
	&& a2enmod headers \
	&& echo "root:$ROOT_PASSWD"|chpasswd

RUN sed -i "s#set -e#set -e\n\nif [ -S /var/run/docker.sock ]; then\n    DOCKER_GID=\$(stat -c '%g' /var/run/docker.sock)\n    groupadd -for -g \${DOCKER_GID} docker\n    usermod -aG docker www-data\nfi\n#" /usr/local/bin/docker-php-entrypoint

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

RUN pecl install memcached \
	&& docker-php-ext-enable memcached \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/; \
  	docker-php-ext-install \
  	intl \
  	gd \
  	bcmath \
  	iconv \
  	mbstring \
  	xsl \
  	zip \
  	soap \
  	pdo_mysql

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

EXPOSE 22 80
