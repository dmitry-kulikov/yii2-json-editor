#!/bin/sh

PHP_VERSION=${1}
NAME="yii2-json-editor:php-${PHP_VERSION}"
sudo docker build --build-arg PHP_VERSION=${PHP_VERSION} --tag ${NAME} .
sudo docker run --interactive --rm --tty ${NAME}
sudo docker image prune --force
