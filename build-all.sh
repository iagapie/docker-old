#!/bin/bash

if [ -z "$1" ]; then
	tag="latest"
else
	tag="$1"
fi

docker build -t iagapie/alpine-3.7:$tag alpine/3.5/os
docker build -t iagapie/alpine-3.5:$tag alpine/3.7/os
docker build -t iagapie/alpine-apache-2.4:$tag alpine/3.7/apache2
docker build -t iagapie/alpine-nginx-1.13:$tag alpine/3.7/nginx
docker build -t iagapie/alpine-php-5.6:$tag alpine/3.5/php/5.6
docker build -t iagapie/alpine-php-7.0:$tag alpine/3.5/php/7.0
docker build -t iagapie/alpine-php-7.1:$tag alpine/3.7/php/7.1
docker build -t iagapie/alpine-php-7.2:$tag alpine/3.7/php/7.2

if [ "$2" = 'yes' ]; then
	docker push iagapie/alpine-3.7:$tag
	docker push iagapie/alpine-3.5:$tag
	docker push iagapie/alpine-apache-2.4:$tag
	docker push iagapie/alpine-nginx-1.13:$tag
	docker push iagapie/alpine-php-5.6:$tag
	docker push iagapie/alpine-php-7.0:$tag
	docker push iagapie/alpine-php-7.1:$tag
	docker push iagapie/alpine-php-7.2:$tag
fi