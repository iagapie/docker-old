FROM iagapie/alpine-3.7:0.1

LABEL maintainer="Igor Agapie <igoragapie@gmail.com>"

RUN apk upgrade -U --no-cache \
	&& apk add -U --no-cache \
		pcre-dev \
		apache2 \
		apache2-mod-wsgi \
		apache2-proxy \
		apache2-ssl \
		apache2-utils \
		apache2-ctl \
		apache2-dev \
		apache2-error \
		apache2-http2 \
		apache2-icons \

	&& rm -rf /tmp/* /var/cache/apk/* \
	&& rm -rf /etc/apache2/* \
	&& mkdir -p /run/apache2

COPY rootfs /

WORKDIR /app

EXPOSE 20 21 22 80 443 10090-10100

CMD ["supervisord", "--nodaemon", "--configuration", "/etc/supervisor/conf.d/supervisord.conf"]