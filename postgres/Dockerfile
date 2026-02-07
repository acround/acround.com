# Выбираем официальный образ PostgreSQL в качестве основы
FROM postgres:15

# Устанавливаем необходимые зависимости для сборки pgvector
# Включая git, make, gcc и заголовочные файлы PostgreSQL.
# Примечание: Для более новых версий PostgreSQL (15+) pgvector может быть в contrib или отдельном пакете.
# В этом примере мы его компилируем, так как это наиболее универсальный способ.
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    postgresql-server-dev-15 \
    git \
    postgresql-server-dev-all \
    && rm -rf /var/lib/apt/lists/*

# Клонируем репозиторий pgvector
RUN cd /tmp \
  && git clone https://github.com/pgvector/pgvector.git \
  && cd pgvector \
  && make \
  && make install \
  && rm -rf /tmp/pgvector
# Альтернативный способ клонирования конкретной версии pgvector
#RUN git clone --branch v0.6.0 https://github.com/pgvector/pgvector.git /usr/src/pgvector

# Собираем и устанавливаем расширение
RUN cd /usr/src/pgvector && make && make install
# Очистка исходного кода pgvector после установки
RUN rm -rf /usr/src/pgvector    
# Добавляем команду для инициализации базы данных при запуске контейнера
# Эта команда создаст расширение pgvector в базе данных по умолчанию
# COPY ./postgres/init-db.sh /docker-entrypoint-initdb.d/init-db.sh
# RUN chmod +x /docker-entrypoint-initdb.d/init-db.sh
# Устанавливаем права на выполнение скрипта инициализации