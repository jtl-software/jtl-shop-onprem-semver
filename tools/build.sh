#!/usr/bin/env bash

php /root/composer.phar install /builds
/builds/vendor/bin/phpunit