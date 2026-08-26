FROM nginx:alpine
COPY nginx-default.conf /etc/nginx/conf.d/default.conf
COPY index.html.template /usr/share/nginx/html/index.html.template
COPY css/ /usr/share/nginx/html/css/
COPY js/ /usr/share/nginx/html/js/
COPY assets/ /usr/share/nginx/html/assets/
COPY docker-entrypoint.d/10-inject-settings.sh /docker-entrypoint.d/10-inject-settings.sh
RUN chmod +x /docker-entrypoint.d/10-inject-settings.sh
EXPOSE 80
