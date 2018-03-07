# Docker (for DEV)

---
## Credentials: SSH, FTP
**user:** `root`
**pass:** `root`
---

## List of repositories:
	* iagapie/alpine-3.7
	* iagapie/alpine-3.5
	* iagapie/alpine-apache-2.4
	* iagapie/alpine-nginx-1.13
	* iagapie/alpine-php-5.6
	* iagapie/alpine-php-7.0
	* iagapie/alpine-php-7.1
	* iagapie/alpine-php-7.2

### Generate self signed certificate example:
`openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout mysite.com.key -out mysite.com.crt`

### Docker run example:
`docker run -itd --name php-7.2 -p 2072:20 -p 2172:21 -p 2272:22 -p 9000 -p 9009 -v $PWD:/app iagapie/alpine-php-7.2`
**See docker-compose.yml for more complex example**
---

### PhpStorm Xdebug
- Languages & Frameworks > PHP > Add > Remote...
	- SSH Credentials
		- HOST
            - Docker-machine: 192.168.99.100 ([Recommended](https://github.com/adlogix/docker-machine-nfs))
            - Linux or Docker4{MAc|Windows}: localhost
        - port: 2244 (or the one you choose on the docker run command)
        - user: `root`
        - pass: `root`
        - Executable: `/usr/bin/php`
    - Path mappings:
        - <Project root> -> `/app`

Oficial PHPStrom documentation for [remote connexion via SSH Credentials](https://confluence.jetbrains.com/display/PhpStorm/Working+with+Remote+PHP+Interpreters+in+PhpStorm).