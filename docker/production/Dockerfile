FROM webdevops/php-nginx:8.0-alpine

WORKDIR /app
USER www-data

ADD application/ .

#### Скрипты установки ####
ADD sh/install-symfony.sh .

#### Очереди ####
ADD php-fpm/symfony-messenger.conf /opt/docker/etc/supervisor.d/symfony-messenger.conf

#### Запуск установки ####
RUN chmod a+x install-symfony.sh && install-symfony.sh
