FROM nginx:1.27-alpine

COPY nginx.conf /etc/nginx/conf.d/default.conf

# Только то, что реально отдаётся браузеру — не весь репозиторий.
# Один index.html на все три языка — переключаются на месте, без
# отдельных страниц под sr/en (см. README, «Три языка»).
COPY index.html sitemap.xml robots.txt /usr/share/nginx/html/
COPY assets /usr/share/nginx/html/assets

# COPY переносит режим файлов из build context как есть — если там
# каталог случайно оказался без прав на чтение для «остальных» (например,
# уцелел от прошлой распаковки архива поверх старого), nginx (не root)
# внутри контейнера не сможет его открыть. Не полагаемся на права хоста.
RUN chmod -R a+rX /usr/share/nginx/html

# Домен в canonical/hreflang/OG подменяется на REZBAJKA_DOMAIN, адрес
# вебхука формы — на N8N_WEBHOOK_URL, оба при старте контейнера — само по
# себе, образ nginx запускает всё исполняемое из /docker-entrypoint.d/ до
# старта nginx.
COPY site-url.sh     /docker-entrypoint.d/40-site-url.sh
COPY webhook-url.sh  /docker-entrypoint.d/41-webhook-url.sh
RUN chmod +x /docker-entrypoint.d/40-site-url.sh /docker-entrypoint.d/41-webhook-url.sh

EXPOSE 80
