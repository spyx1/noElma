#!/usr/bin/env bash

# Первый запуск noElma на новом сервере.
# Скрипт создаёт секреты, поднимает MySQL, восстанавливает стартовую БД
# и запускает приложение. Повторный запуск не перезаписывает существующую БД.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
    echo "Нужны Docker Engine и Docker Compose v2."
    exit 1
fi

if [ ! -f .env.production ]; then
    db_password="$(openssl rand -hex 24)"
    root_password="$(openssl rand -hex 32)"
    server_ip="$(hostname -I 2>/dev/null | awk '{print $1}')"
    server_ip="${server_ip:-127.0.0.1}"
    app_key="base64:$(openssl rand -base64 32 | tr -d '\n')"

    sed \
        -e "s#^APP_KEY=.*#APP_KEY=${app_key}#" \
        -e "s#^APP_URL=.*#APP_URL=http://${server_ip}:18743#" \
        -e "s#^DB_PASSWORD=.*#DB_PASSWORD=${db_password}#" \
        -e "s#^MYSQL_ROOT_PASSWORD=.*#MYSQL_ROOT_PASSWORD=${root_password}#" \
        .env.production.example > .env.production

    echo "Создан .env.production с уникальными паролями."
fi

# Секреты создаются на сервере и не должны попасть в последующий git add.
if [ -d .git ] && ! grep -qxF '.env.production' .git/info/exclude 2>/dev/null; then
    printf '%s\n' '.env.production' >> .git/info/exclude
fi

compose=(docker compose --env-file .env.production -f docker-compose.production.yml)

echo "Запуск базы данных…"
"${compose[@]}" up -d --wait db

echo "Ожидание готовности MySQL…"
until "${compose[@]}" exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -N "$MYSQL_DATABASE" -e "SELECT 1"' >/dev/null 2>&1; do
    sleep 2
done

migrations_count="$("${compose[@]}" exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -N "$MYSQL_DATABASE" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 0x6d6967726174696f6e73"' 2>/dev/null || true)"

if [ "$migrations_count" != "1" ]; then
    echo "Восстановление стартовой базы…"
    gzip -dc database/bootstrap/laravel.sql.gz | "${compose[@]}" exec -T db sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"'
else
    echo "База уже инициализирована — импорт пропущен."
fi

echo "Сборка и запуск приложения…"
"${compose[@]}" up -d --build --wait

app_url="$(grep '^APP_URL=' .env.production | cut -d= -f2-)"
echo ""
echo "noElma запущена: ${app_url}"
