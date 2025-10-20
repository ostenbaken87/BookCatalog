.PHONY: help build up down restart logs shell composer-install init

help: ## Показать эту справку
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Собрать Docker образы
	docker-compose build

up: ## Запустить все контейнеры
	docker-compose up -d

down: ## Остановить все контейнеры
	docker-compose down

restart: ## Перезапустить все контейнеры
	docker-compose restart

logs: ## Показать логи всех контейнеров
	docker-compose logs -f

logs-php: ## Показать логи PHP контейнера
	docker-compose logs -f php

logs-nginx: ## Показать логи NGINX контейнера
	docker-compose logs -f nginx

logs-mysql: ## Показать логи MySQL контейнера
	docker-compose logs -f mysql

shell: ## Войти в PHP контейнер
	docker-compose exec php bash

shell-root: ## Войти в PHP контейнер как root
	docker-compose exec -u root php bash

mysql-shell: ## Войти в MySQL консоль
	docker-compose exec mysql mysql -u root -proot bookcatalog

composer-install: ## Установить зависимости Composer
	docker-compose exec php composer install

composer-update: ## Обновить зависимости Composer
	docker-compose exec php composer update

yii-init: ## Инициализировать Yii2 проект (basic)
	docker-compose exec php composer create-project --prefer-dist yiisoft/yii2-app-basic /tmp/yii2-app
	docker-compose exec php bash -c "cp -rn /tmp/yii2-app/* /var/www/html/ && cp -rn /tmp/yii2-app/.* /var/www/html/ 2>/dev/null || true"
	docker-compose exec php rm -rf /tmp/yii2-app
	docker-compose exec php chmod -R 777 runtime web/assets

yii-migrate: ## Запустить миграции
	docker-compose exec php php yii migrate --interactive=0

yii-cache-flush: ## Очистить кеш
	docker-compose exec php php yii cache/flush-all

init: build up yii-init ## Полная инициализация проекта (build + up + установка Yii2)

clean: ## Удалить все контейнеры и volumes
	docker-compose down -v

rebuild: clean build up ## Пересобрать всё с нуля

