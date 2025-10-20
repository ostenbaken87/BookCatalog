# Book Catalog - Yii2 Application

Каталог книг на Yii2 с использованием Docker для локальной разработки.

## Требования

- Docker
- Docker Compose
- Make (опционально, для удобства)

## Быстрый старт

### 1. Запуск окружения

```bash
# Сборка и запуск контейнеров
make build
make up

# Или без Make:
docker-compose build
docker-compose up -d
```

### 2. Установка Yii2

```bash
# Установить Yii2 Basic в текущий проект
make yii-init

# Или без Make:
docker-compose exec php composer create-project --prefer-dist yiisoft/yii2-app-basic .
docker-compose exec php chmod -R 777 runtime web/assets
```

### 3. Настройка подключения к БД

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

### 4. Доступ к приложению

- **Приложение**: http://localhost:8080
- **Adminer (управление БД)**: http://localhost:8081
  - Сервер: `mysql`
  - Пользователь: `root`
  - Пароль: `root`
  - База данных: `bookcatalog`

## Полезные команды

```bash
# Показать все доступные команды
make help

# Войти в PHP контейнер
make shell

# Войти в MySQL консоль
make mysql-shell

# Просмотр логов
make logs
make logs-php
make logs-nginx
make logs-mysql

# Перезапустить контейнеры
make restart

# Остановить контейнеры
make down

# Запустить миграции
make yii-migrate

# Очистить кеш
make yii-cache-flush
```

## Структура проекта

```
BookCatalog/
├── docker/
│   ├── nginx/
│   │   └── default.conf       # Конфигурация NGINX
│   ├── php/
│   │   ├── Dockerfile         # Docker образ для PHP
│   │   └── php.ini            # Настройки PHP
│   └── mysql/
│       └── init.sql           # Инициализация БД
├── docker-compose.yml         # Оркестрация Docker сервисов
├── Makefile                   # Удобные команды для работы
└── README.md                  # Эта документация
```

## Сервисы

- **NGINX** - веб-сервер (порт 8080)
- **PHP 8.1-FPM** - PHP с необходимыми расширениями
- **MySQL 8.0** - база данных (порт 3306)
- **Adminer** - веб-интерфейс для управления БД (порт 8081)

## Разработка

После настройки окружения вы можете:

1. Создавать контроллеры: `docker-compose exec php php yii gii/controller`
2. Создавать модели: `docker-compose exec php php yii gii/model`
3. Создавать CRUD: `docker-compose exec php php yii gii/crud`
4. Создавать миграции: `docker-compose exec php php yii migrate/create <name>`

## Решение проблем

### Права доступа

Если возникают проблемы с правами доступа:

```bash
docker-compose exec -u root php chmod -R 777 runtime web/assets
```

### Пересоздание контейнеров

```bash
make rebuild
# или
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Просмотр логов

```bash
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f mysql
```

## Тестовое задание

Подробное описание тестового задания находится в файле `тестовое задание.md`.

### Основные требования:
- Каталог книг (название, год, описание, ISBN, фото)
- Авторы (ФИО)
- Роли: Гость (просмотр + подписка), Юзер (CRUD)
- Отчет: ТОП-10 авторов за год
- Бонус: SMS уведомления через smspilot.ru

