#!/usr/bin/env bash

BUILD_PATH=$1

ls -la
ls -la /
ls -la /testing
cd ${BUILD_PATH}
composer install