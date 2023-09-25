FROM trafex/php-nginx:2.6.0

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

USER root

RUN apk --update --no-cache add php81-fileinfo php81-simplexml php81-tokenizer php81-xmlwriter php81-pdo php81-pdo_mysql php81-pdo_pgsql php81-zip php81-iconv php81-sockets php81-sodium php81-exif

USER nobody