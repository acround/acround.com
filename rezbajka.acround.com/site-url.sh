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
# в файлах уже есть свой (все ссылки в них — с адресом сайта). Схему
# (http/https) можно не указывать — если её нет, подставляется https;
# это нужно, когда та же переменная идёт ещё и в Traefik Host(...), а там
# схема не нужна и сломает совпадение по хосту.
set -eu

: "${REZBAJKA_DOMAIN:=https://rezbajka.acround.com}"
REZBAJKA_DOMAIN="${REZBAJKA_DOMAIN%/}"   # свой слэш не добавит — уже есть в файлах
case "$REZBAJKA_DOMAIN" in
  http://*|https://*) ;;
  # Без схемы — например, значение той же переменной ушло ещё и в Traefik
  # Host(...), которому схема не нужна и не годится. Файлам она нужна
  # всегда, поэтому достраиваем https:// сами.
  *) REZBAJKA_DOMAIN="https://$REZBAJKA_DOMAIN" ;;
esac
BAKED='https://rezbajka.acround.com'

if [ "$REZBAJKA_DOMAIN" != "$BAKED" ]; then
  find /usr/share/nginx/html -type f \( -name '*.html' -o -name '*.xml' -o -name '*.txt' \) \
    -exec sed -i "s#$BAKED#$REZBAJKA_DOMAIN#g" {} +
  echo "site-url.sh: $BAKED -> $REZBAJKA_DOMAIN"
fi
