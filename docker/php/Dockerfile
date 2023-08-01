FROM php:7.4-fpm-alpine

RUN apk --update --no-cache add acl autoconf bash build-base gettext gettext-dev git mysql-dev icu-dev libxslt-dev zip libzip-dev linux-headers yarn \
        && docker-php-ext-install  pdo_mysql ctype fileinfo gettext \
        && docker-php-ext-enable pdo_mysql ctype fileinfo gettext

#Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

##Install symfony
RUN wget https://get.symfony.com/cli/installer -O /tmp/installer && \
    chmod a+x /tmp/installer && \
    /tmp/installer --install-dir=/usr/local/bin/ && \
    rm /tmp/installer

COPY setup-symfony.sh /usr/bin/setup-symfony.sh

RUN chmod a+x /usr/bin/setup-symfony.sh

WORKDIR /var/www

CMD ["setup-symfony.sh"]
