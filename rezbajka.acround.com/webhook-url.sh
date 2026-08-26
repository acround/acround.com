#!/bin/sh
# Подставляет адрес вебхука n8n из N8N_WEBHOOK_URL в assets/js/main.js при
# старте контейнера — тем же приёмом, что домен в site-url.sh: образ nginx
# сам запускает всё исполняемое из /docker-entrypoint.d/ до старта nginx.
#
# Не задан N8N_WEBHOOK_URL — не трогаем файл вовсе: в нём остаётся пустая
# строка, и форма честно говорит «приём заявок не подключён» (main.js).
set -eu

if [ -n "${N8N_WEBHOOK_URL:-}" ]; then
  sed -i "s|var WEBHOOK_URL = '[^']*';|var WEBHOOK_URL = '$N8N_WEBHOOK_URL';|" \
    /usr/share/nginx/html/assets/js/main.js
  echo "webhook-url.sh: WEBHOOK_URL -> $N8N_WEBHOOK_URL"
fi
