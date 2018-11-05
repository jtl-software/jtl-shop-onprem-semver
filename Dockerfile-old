FROM php:7.1
RUN apt-get update && apt-get install -y apt-transport-https ca-certificates software-properties-common wget git unzip gnupg
RUN wget https://download.docker.com/linux/debian/gpg && apt-key add gpg \
    && echo "deb [arch=amd64] https://download.docker.com/linux/debian $(lsb_release -cs) stable" | tee -a /etc/apt/sources.list.d/docker.list \
    && apt-get update && apt-get -y install docker-ce
RUN cd /root \
  && wget https://composer.github.io/installer.sig -O - -q | tr -d '\n' > installer.sig \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('SHA384', 'composer-setup.php') === file_get_contents('installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php'); unlink('installer.sig');"
RUN echo manual | tee /etc/init/docker.override && update-rc.d docker enable && service docker start