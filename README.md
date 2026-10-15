# Symfony 6.0 basic (PHP 8+)

Стандартная Symfony 6.0, развернутая в докере

# Локальная установка

* Скопировать `/.env.example` в `/.env` - заполнить параметры
* Запустить `docker-compose up -d`
* Подключиться в `php-fpm` запустить `composer install`
* В `php-fpm` запустить `composer dump-env dev`
* Заполнить параметры (если требуется) в файле `/application/.env.local.php`
* Запустить миграции `php bin/console doctrine:migration:migrate`
* Создать пользователя админа `php bin/console admin:create {username} {password}`
* Через админку загрузить электронную подпись с паролем

# Конфигурация (.env)

* SOCIAL_API_TOKEN - Токен для работы api от соц. сетей
* SOCIAL_TELEGRAM_URL - Ссылка на сервис соц. сетей для отправки сообщений в telegram
* SOCIAL_TELEGRAM_CHAT_ID - chatId телеграмма, для уведомлений

# Использование

* Для актуализации списка социально значимых ресурсов через ЭП необходимо запустить
  команду `php bin/console social-list:actualize`
* Для актуализации списка социально значимых ресурсов через логин/пароль необходимо запустить
  команду `php bin/console social-list:actualize-v2` 
* В проекте используются message (только для работы с ЭП). Поэтому постоянно должна быть запущена
  команда `php bin/console messenger:consume async`

# FAQ

* Для того чтобы сделать тестовый запрос в ркн, необходимо заменить ссылку `RKN_BASE_URL`
  на `http://vigruzki.rkn.gov.ru/services/OperatorRequestTest/?wsdl` и в файле `SocialResourceResponseSubscriber`
  указать название файла `register_test.xml`
* В проекте установлен swagger по ссылке `/api/doc`