#!/bin/sh
set -e

cd /var/www/html

# Verzeichnisse anlegen (storage kann ein leeres Volume sein)
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
    storage/framework/views storage/framework/testing storage/logs storage/debugbar bootstrap/cache

# APP_KEY: wenn nicht per Umgebung gesetzt, einmalig erzeugen und im storage-Volume ablegen.
# Er wird in .env geschrieben, damit auch "docker compose exec app php artisan ..." ihn sieht.
if [ -z "${APP_KEY:-}" ]; then
    KEY_FILE=storage/app/.app_key
    if [ ! -s "$KEY_FILE" ]; then
        echo "[entrypoint] Erzeuge neuen APP_KEY"
        php -r 'echo "base64:".base64_encode(random_bytes(32));' > "$KEY_FILE"
        chmod 600 "$KEY_FILE"
    fi
    echo "APP_KEY=$(cat "$KEY_FILE")" > .env
fi

php artisan storage:link --force >/dev/null 2>&1 || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "[entrypoint] Warte auf Datenbank und migriere ..."
    tries=0
    until php artisan migrate --force; do
        tries=$((tries + 1))
        if [ "$tries" -ge 30 ]; then
            echo "[entrypoint] Migration fehlgeschlagen" >&2
            exit 1
        fi
        sleep 2
    done
fi

# Demodaten nur anlegen, wenn noch keine Gliederung existiert
if [ "${IS_DEMO:-false}" = "true" ] && [ "${DEMO_DATA_ON_START:-false}" = "true" ]; then
    clients=$(php artisan tinker --execute='echo \App\Client::count();' 2>/dev/null | tail -n 1)
    if [ "$clients" = "0" ]; then
        echo "[entrypoint] Lege Demodaten an"
        php artisan demo:createDemoClient
    fi
fi

chown -R www-data:www-data storage bootstrap/cache

exec "$@"
