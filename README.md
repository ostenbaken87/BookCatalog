Запуск окружения

Сборка и запуск контейнеров
make build
make up

# Или без Make:
docker-compose build
docker-compose up -d


Установка Yii2

```bash
# Установить Yii2 Basic в текущий проект
make yii-init

# Или без Make:
docker-compose exec php composer create-project --prefer-dist yiisoft/yii2-app-basic .
docker-compose exec php chmod -R 777 runtime web/assets
```

Настройка подключения к БД

После установки Yii2 отредактируйте файл `config/db.php`:

```php
<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=mysql;dbname=bookcatalog',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
];
```
Доступ к приложению

- http://localhost:8080

