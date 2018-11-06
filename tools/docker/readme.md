1. Docker installieren
2. Dockerfile anlegen
3. Bei Gitlab Container Registry anmelden: "sudo docker login gitlab.jtl-software.de:4567"
4. Dockerfile builden "sudo docker build --pull -t "gitlab.jtl-software.de:4567/jtlshop/semver/building_testing-php7.1" -f "tools/docker/php7.1/Dockerfile" ."
5. Docker Image hoch laden: "sudo docker push gitlab.jtl-software.de:4567/jtlshop/semver/building_testing-php7.1"
6. Docker image in der gitlab.yml referenzieren.
