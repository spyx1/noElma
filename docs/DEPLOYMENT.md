# Развёртывание noElma в Docker

Эта инструкция переносит приложение, MySQL и пользовательские файлы на другой сервер. Production-конфигурация не использует bind-mount исходников: приложение собирается в образ, а база и `storage` хранятся в Docker volumes.

## 1. Подготовка сервера

На сервере должны быть установлены Docker Engine и Docker Compose v2. Клонируйте проект и создайте production-настройки:

```bash
git clone git@github.com:spyx1/noElma.git
cd noElma
cp .env.production.example .env.production
```

В `.env.production` задайте сильные разные пароли `DB_PASSWORD` и `MYSQL_ROOT_PASSWORD`, публичный адрес в `APP_URL` и нужный порт в `APP_PORT`.

Соберите образ приложения, получите ключ Laravel и вставьте его в `APP_KEY`:

```bash
docker compose -f docker-compose.production.yml --env-file .env.production build app
docker compose -f docker-compose.production.yml --env-file .env.production run --rm --entrypoint php app artisan key:generate --show
```

## 2. Экспорт текущей базы

На текущей машине, из корня проекта:

```bash
docker compose exec -T db sh -c 'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction --routines --events "$MYSQL_DATABASE"' > noelma-backup.sql
```

Передайте `noelma-backup.sql` на новый сервер безопасным каналом, например `scp`.

## 3. Импорт и первый запуск

На новом сервере сначала поднимите только MySQL:

```bash
docker compose -f docker-compose.production.yml --env-file .env.production up -d db
```

После готовности базы импортируйте резервную копию:

```bash
docker compose -f docker-compose.production.yml --env-file .env.production exec -T db sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < noelma-backup.sql
```

Запустите приложение:

```bash
docker compose -f docker-compose.production.yml --env-file .env.production up -d --build
```

Откройте `http://SERVER_IP:APP_PORT`. При старте контейнер приложения выполнит миграции, создаст ссылку `storage` и закэширует конфигурацию, маршруты и шаблоны.

## Обновление приложения

```bash
git pull
docker compose -f docker-compose.production.yml --env-file .env.production up -d --build
```

## Резервное копирование на сервере

```bash
docker compose -f docker-compose.production.yml --env-file .env.production exec -T db sh -c 'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction --routines --events "$MYSQL_DATABASE"' > noelma-backup-$(date +%F).sql
```

Пользовательские загруженные файлы находятся в Docker volume `storage-data`; его нужно включить в регламентный backup вместе с SQL-дампом.
