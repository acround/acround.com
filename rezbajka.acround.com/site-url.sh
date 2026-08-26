#!/bin/sh
# Подменяет домен и протокол в отданной статике на REZBAJKA_DOMAIN из
# окружения. Лежит в /docker-entrypoint.d/ — образ nginx запускает всё
# исполняемое оттуда сам, до старта самого nginx (см. /docker-entrypoint.sh
# в базовом образе). Никаких правок в Dockerfile CMD/ENTRYPOINT для этого
# не нужно.
#
# По умолчанию — тот же адрес, что зашит в файлы при сборке: без
# REZBAJKA_DOMAIN в окружении подмена ничего не меняет, прод остаётся как
# есть. Для локального запуска задайте REZBAJKA_DOMAIN в .env (см.
# .env.example) — без слэша на конце, он не нужен и удваивается там, где
# в файлах уже есть свой (все ссылки в них — с адресом сайта).
set -eu

: "${REZBAJKA_DOMAIN:=https://rezbajka.akround.com}"
REZBAJKA_DOMAIN="${REZBAJKA_DOMAIN%/}"   # свой слэш не добавит — уже есть в файлах
BAKED='https://rezbajka.akround.com'

if [ "$REZBAJKA_DOMAIN" != "$BAKED" ]; then
  find /usr/share/nginx/html -type f \( -name '*.html' -o -name '*.xml' -o -name '*.txt' \) \
    -exec sed -i "s#$BAKED#$REZBAJKA_DOMAIN#g" {} +
  echo "site-url.sh: $BAKED -> $REZBAJKA_DOMAIN"
fi
