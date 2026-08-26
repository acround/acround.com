#!/bin/sh
# Fills window.AKROUND_SETTINGS.n8nWebhookUrl in index.html.template from the
# N8N_WEBHOOK_URL env var, and writes the result as the file nginx serves.
# Runs automatically on every container start (nginx:alpine's entrypoint
# executes every *.sh script in /docker-entrypoint.d/ before starting nginx),
# so changing N8N_WEBHOOK_URL in .env + restarting the container is enough —
# no image rebuild needed.
set -eu

: "${N8N_WEBHOOK_URL:?N8N_WEBHOOK_URL is not set — add it to your .env, e.g. N8N_WEBHOOK_URL=https://n8n.acround.com/webhook/xxxxxxxx}"

envsubst '${N8N_WEBHOOK_URL}' \
  < /usr/share/nginx/html/index.html.template \
  > /usr/share/nginx/html/index.html
