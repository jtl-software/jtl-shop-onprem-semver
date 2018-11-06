#!/usr/bin/env bash

cd /builds
php /root/composer.phar install
/builds/vendor/bin/phpunit