FROM python:3.10-slim

WORKDIR /app

# Установка системных зависимостей
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Установка Python зависимостей
RUN pip install --no-cache-dir \
    flask \
    gunicorn \
    sentence-transformers \
    torch --extra-index-url https://download.pytorch.org/whl/cpu

# Предзагрузка конкретной модели (MiniLM - оптимальна для CPU)
# [cite_start]Важно: модель здесь должна СОВПАДАТЬ с моделью в app.py [cite: 1, 2]
RUN python -c "from sentence_transformers import SentenceTransformer; SentenceTransformer('paraphrase-multilingual-mpnet-base-v2')"

COPY app.py .

EXPOSE 5000

# [cite_start]1 воркер — это критически важно для CPU-инференса, чтобы избежать перегрузки процессора [cite: 1]
CMD ["gunicorn", "--bind", "0.0.0.0:5000", "--workers", "1", "--timeout", "120", "app:app"]