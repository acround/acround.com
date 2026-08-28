# Деплой на VPS с Traefik

Статика в Docker-контейнере с nginx, Traefik снаружи выдаёт TLS и
маршрутизирует по домену. Проверено сборкой и запуском локально.

## Один раз на сервере

Traefik уже поднят, слушает `web` (80) и `websecure` (443), у него настроен
certresolver `letsencrypt`, и есть внешняя сеть `acround-web`, к которой он
подключён. Если у вас другие имена — поправьте их в `docker-compose.yml`
(entrypoints, certresolver, имя сети) перед первым запуском.

## Деплой

```bash
git clone <URL вашего репозитория> rezbajka
cd rezbajka
docker compose up -d --build
```

Traefik подхватит контейнер по лейблам автоматически — отдельно
регистрировать роутер не нужно. Через минуту-две (пока выпускается
сертификат) сайт должен открыться на `https://rezbajka.acround.com`.

## Обновление после правок

```bash
git pull
docker compose up -d --build
```

Пересоберёт образ и перезапустит контейнер без простоя Traefik.

## Проверить, что всё поднялось

```bash
docker compose ps                 # rezbajka — healthy
docker compose logs -f rezbajka   # логи nginx
curl -I https://rezbajka.acround.com/
```

## Локальный тест

В `index.html`, `sitemap.xml` и `robots.txt` зашит настоящий домен —
canonical, OG-теги. Открыть это как есть на `localhost` можно, но ссылки в
этих тегах будут вести на продовый адрес, а не на ваш локальный порт.

Проще всего — без Traefik и без Compose вообще:

```bash
docker build -t rezbajka .
docker run --rm -p 8082:80 \
  -e REZBAJKA_DOMAIN=http://localhost:8082 \
  -e N8N_WEBHOOK_URL=https://n8n.example.com/webhook/rezbajka-order \
  rezbajka
# открыть http://localhost:8082
```

`REZBAJKA_DOMAIN` подменяется в отданных файлах, `N8N_WEBHOOK_URL` — в
`assets/js/main.js`, оба при старте контейнера (`site-url.sh` и
`webhook-url.sh`, запускаются образом `nginx` автоматически из
`/docker-entrypoint.d/`) — без пересборки образа и без правок в
репозитории. Не заданы — подмены нет, отдаётся ровно то, что зашито при
сборке (продовый домен, пустой адрес вебхука — форма честно говорит, что
приём заявок не подключён); так ведёт себя обычный `docker compose up`
на сервере без `.env`, ничего менять не нужно.

Через Compose — те же переменные, но из `.env`:

```bash
cp .env.example .env        # REZBAJKA_DOMAIN и N8N_WEBHOOK_URL — впишите свои
cp docker-compose.override.yml.example docker-compose.override.yml   # публикует порт 8082
docker compose up -d --build
```

`docker-compose.override.yml` требует сеть `acround-web` (Compose сольёт
её со своей `networks:`, а не заменит) — если её нет локально, `docker
network create acround-web` или просто `docker run` выше, без Compose.

Оба файла — `.env` и `docker-compose.override.yml` — в `.gitignore`,
в репозиторий не попадут.

## Что в образе

`Dockerfile` берёт готовую статику как есть (`index.html` — один файл на
все три языка, `assets/`, `sitemap.xml`, `robots.txt`) и кладёт на
`nginx:alpine`. Сборки нет.

`nginx.conf`: gzip, кэш на неделю для `/assets/` (в именах файлов нет
хэша, поэтому не `immutable`), `no-cache` на HTML — правки текста видны
сразу после редеплоя, без сброса кэша у посетителей.

**Не забудьте после клонирования на сервере** прогнать `python3
tools/seo.py`, если планируете править тексты прямо там: он пересобирает
`sitemap.xml`, `robots.txt` и блок для поиска в `index.html` (см. README).
