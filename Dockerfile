FROM php:5.6-zts

WORKDIR /opt/altrhead

ENV TERM=xterm

RUN ln -s /opt/altrhead/vendor/bin/codecept /usr/bin/

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

RUN apt-get update && apt-get install -y \
  git \
  zip \
  unzip \
  build-essential \
  && rm -rf /var/lib/apt/lists/*

RUN pecl install pthreads-2.0.10 \
    && docker-php-ext-enable pthreads

RUN pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug

CMD [ "tail", "-f", "/dev/null" ]
