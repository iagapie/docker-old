FROM alpine:3.7

LABEL maintainer="Igor Agapie <igoragapie@gmail.com>"

ARG USER=root
ARG PASSWORD=root

RUN apk upgrade -U --no-cache \
    && apk add -U --no-cache \
        ca-certificates \
        bash \
        tini \
        openssl \
        openssh \
        supervisor \
        autoconf \
        git \
        curl \
        wget \
        apache-ant \
        zip \
        vsftpd \

    && update-ca-certificates \

    && rm -rf /tmp/* /var/cache/apk/* \

    && sed -i s/#PermitRootLogin.*/PermitRootLogin\ yes/ /etc/ssh/sshd_config \
    && echo "${USER}:${PASSWORD}" | chpasswd \
    && ssh-keygen -A

COPY rootfs /

ENTRYPOINT ["/sbin/tini", "--"]

EXPOSE 20 21 22 10090-10100

CMD ["supervisord", "--nodaemon", "--configuration", "/etc/supervisor/conf.d/supervisord.conf"]