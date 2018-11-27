## Mag 2 (Alpine)

`docker network create web`

`docker run -dit --network web -v $PWD/app/www:/var/www/html --name m2-php71-fpm iagapie/m2-php71-fpm`

`docker run -dit --network web -v $PWD/app/www:/var/www/html:ro -v $PWD/app/httpd-my.conf:/usr/local/apache2/conf/extra/httpd-my.conf -p 8082:80 -p 8083:443 --name apache2 iagapie/apache2-alpine`
