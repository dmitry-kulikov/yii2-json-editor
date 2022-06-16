# syntax=docker/dockerfile:1.4.2-labs

# PHP version
# examples of allowed values: 5.6-cli, 5.6-cli-alpine, 7.4-cli, 7.4-cli-alpine
# and other tags from https://hub.docker.com/_/php
ARG PHP_VERSION=5.6-cli

########################################################################################################################
FROM php:${PHP_VERSION} AS default

WORKDIR /usr/src/yii2-json-editor

# install PHP extensions
RUN curl --silent --show-error --location --output /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/download/1.5.20/install-php-extensions \
    && chmod a+x /usr/local/bin/install-php-extensions \
    && sync \
    && install-php-extensions \
        pcntl `# for tests` \
        xdebug `# for tests`

# install the latest stable Composer 1.x version
RUN curl --silent --show-error --location https://getcomposer.org/installer | php -- --1 \
    && mv composer.phar /usr/local/bin/composer

COPY composer.json ./

########################################################################################################################
FROM default AS alpine

# install system packages
RUN apk update \
    && apk add \
        git `# for Composer and developers` \
        nano `# for developers` \
        unzip `# for Composer`

# install dependencies using Composer
RUN --mount=type=cache,id=composer,target=/root/.composer/cache,sharing=locked \
    composer global require --optimize-autoloader 'fxp/composer-asset-plugin:^1.4.6' \
    \
    # workarounds for Composer plugin fxp/composer-asset-plugin:
    # 1) plugin may ask Git to clone repository using "git+ssh" protocol,
    # for example git+ssh://git@github.com/garycourt/uri-js.git,
    # but it requires SSH key linked with GitHub account which we have not in Docker;
    && git config --global --add url.'https://'.insteadOf 'git+ssh://git@' \
    # 2) plugin may ask Git to clone repository using "git" protocol,
    # but the unencrypted "git" protocol is permanently disabled on GitHub,
    # see https://github.blog/changelog/2022-03-15-removed-unencrypted-git-protocol-and-certain-ssh-keys.
    && git config --global --add url.'https://github.com/'.insteadOf 'git@github.com:' \
    && composer update \
    && composer clear-cache

########################################################################################################################
FROM default AS debian

# install system packages
RUN apt-get update \
    && apt-get --assume-yes --no-install-recommends install \
        gnupg2 \
    && apt-key update \
    && apt-get update \
    && apt-get --assume-yes --no-install-recommends install \
        git `# for Composer and developers` \
        nano `# for developers` \
        unzip `# for Composer` \
    \
    # clean up
    && rm --force --recursive /var/lib/apt/lists/* /tmp/* /var/tmp/*

# install dependencies using Composer
RUN --mount=type=cache,id=composer,target=/root/.composer/cache,sharing=locked \
    composer global require --optimize-autoloader 'fxp/composer-asset-plugin:^1.4.6' \
    \
    # workarounds for Composer plugin fxp/composer-asset-plugin:
    # 1) plugin may ask Git to clone repository using "git+ssh" protocol,
    # for example git+ssh://git@github.com/garycourt/uri-js.git,
    # but it requires SSH key linked with GitHub account which we have not in Docker;
    && git config --global --add url.'https://'.insteadOf 'git+ssh://git@' \
    # 2) plugin may ask Git to clone repository using "git" protocol,
    # but the unencrypted "git" protocol is permanently disabled on GitHub,
    # see https://github.blog/changelog/2022-03-15-removed-unencrypted-git-protocol-and-certain-ssh-keys.
    && git config --global --add url.'https://github.com/'.insteadOf 'git@github.com:' \
    && composer update \
    && composer clear-cache

########################################################################################################################
FROM debian AS debian-runkit

# install runkit extension
RUN pecl install \
    runkit `# for tests`

########################################################################################################################
FROM debian AS debian-runkit7

# install runkit7 extension
RUN pecl install \
    runkit7-4.0.0a3 `# for tests`
