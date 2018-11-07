#!/usr/bin/env bash

PROJECT_NAME=$1;
TAG=$2;
VERSION="${TAG//[\/\.]/-}";
FILENAME="$PROJECT_NAME-$VERSION.zip";
ARCHIVE_PATH="$3/$FILENAME";

zip -r ${FILENAME} . -x *.git*\* .idea*\* tools*\* Tests*\* .travis.yml\* phpunit.xml\*