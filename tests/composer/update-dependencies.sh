#!/usr/bin/env sh

# This script can be executed in Docker container to update composer dependencies and composer.lock for specified
# version of PHP.

cd "$(dirname "$0")" || exit
BASE_PATH="$(pwd)"
LATEST_COMPOSER_SUFFIX='7.4'
COMPOSER_LOCK_TO_UPDATE="${BASE_PATH}/composer-${COMPOSER_SUFFIX}.lock"

# change directory to directory containing composer.json
cd "${BASE_PATH}/../.." || exit

# remove composer.lock because it probably created for other version of PHP
rm composer.lock
composer update
mv --force composer.lock "${COMPOSER_LOCK_TO_UPDATE}"
cp "${BASE_PATH}/composer-${LATEST_COMPOSER_SUFFIX}.lock" composer.lock
chown 1000:1000 composer.lock "${COMPOSER_LOCK_TO_UPDATE}"
chmod 664 composer.lock "${COMPOSER_LOCK_TO_UPDATE}"
