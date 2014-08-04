PHP?=$(shell which php)
SERVER_HOST?=localhost
SERVER_PORT?=8888

all:

install:
	$(PHP) -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
	$(PHP) composer.phar install
	$(PHP) ./bin/init-db.php

server:
	$(PHP) -S $(SERVER_HOST):$(SERVER_PORT) -t web

test:
	$(PHP) ./vendor/bin/phpunit --coverage-html ./report
