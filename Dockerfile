FROM php:7.1-cli
RUN apt-get update && apt-get install wget git unzip -y
#RUN wget https://download.docker.com/linux/debian/gpg && apt-key add gpg \
#    && echo "deb [arch=amd64] https://download.docker.com/linux/debian $(lsb_release -cs) stable" | tee -a /etc/apt/sources.list.d/docker.list \
#    && apt-get update && apt-get -y install docker-ce
#RUN curl -fsSL https://download.docker.com/linux/debian/gpg | apt-key add -
#RUN add-apt-repository \
#    "deb [arch=amd64] https://download.docker.com/linux/debian \
#    $(lsb_release -cs) \
#    stable"
#RUN apt-get update && apt-get install docker-ce -y
#RUN rc-update add docker sysinit
#RUN /etc/init.d/docker start
#RUN /etc/init.d/docker status
#RUN docker version
#RUN docker run hello-world
RUN mkdir /builds
COPY . /builds
RUN cd /root \
    && wget https://composer.github.io/installer.sig -O - -q | tr -d '\n' > installer.sig \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === file_get_contents('installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php'); unlink('installer.sig');"
#RUN systemctl enable docker && systemctl start docker
#RUN service docker start