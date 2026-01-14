# Content System

Система комментариев к контенту на Laravel 11.

## Возможности

- Новости и видео посты
- Комментарии с поддержкой вложенности
- Курсорная пагинация
- REST API
- Swagger документация

## Требования

- PHP 8.2+
- MySQL 8+
- Docker & Docker Compose

## Установка

```bash
composer install
cp .env.example .env
docker-compose up -d
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

## API

API доступен по адресу `/api/v1`

Swagger документация: `/api/documentation`

## Тестирование

```bash
docker-compose exec app php artisan test
```
