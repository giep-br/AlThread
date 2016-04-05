FROM php:7-zts
RUN apt-get update \
    && pecl install pthreads \
    && docker-php-ext-enable pthreads

CMD ["/bin/bash"]