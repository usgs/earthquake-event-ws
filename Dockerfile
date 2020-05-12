ARG BUILD_IMAGE=usgs/node:latest
ARG FROM_IMAGE=usgs/httpd-php:latest


FROM ${BUILD_IMAGE} as buildenv


# php required for pre-install
USER root
RUN yum install -y \
    bzip2 \
    php && \
    npm install -g grunt-cli

COPY --chown=usgs-user:usgs-user . /earthquake-event-ws
WORKDIR /earthquake-event-ws

# Build project
USER usgs-user
RUN /bin/bash --login -c "\
    npm install --no-save && \
    php src/lib/pre-install.php --non-interactive --skip-db && \
    grunt builddev && \
    grunt builddist && \
    rm dist/conf/config.ini dist/conf/httpd.conf \
    "

USER root
ENV APP_DIR=/var/www/apps

# Pre-configure template
RUN /bin/bash --login -c "\
    mkdir -p ${APP_DIR}/hazdev-template && \
    cp -r node_modules/hazdev-template/dist/* ${APP_DIR}/hazdev-template/. && \
    php ${APP_DIR}/hazdev-template/lib/pre-install.php --non-interactive \
    "

# Pre-configure app
ENV OFFSITE_HOST=earthquake.usgs.gov
ENV storage_directory=/data/product
ENV storage_url=/archive/product

RUN /bin/bash --login -c "\
    mkdir -p ${APP_DIR}/earthquake-event-ws && \
    cp -r dist/* ${APP_DIR}/earthquake-event-ws/. && \
    php ${APP_DIR}/earthquake-event-ws/lib/pre-install.php --non-interactive \
    "


FROM ${FROM_IMAGE}

COPY --from=buildenv /var/www/apps/ /var/www/apps/

# configure template and apps
RUN /bin/bash --login -c "\
    cp /var/www/apps/earthquake-event-ws/htdocs/_config.inc.php /var/www/html/. && \
    ln -s /var/www/apps/hazdev-template/conf/httpd.conf /etc/httpd/conf.d/hazdev-template.conf && \
    ln -s /var/www/apps/earthquake-event-ws/conf/httpd.conf /etc/httpd/conf.d/earthquake-event-ws.conf \
    "

HEALTHCHECK \
    --interval=15s \
    --timeout=1s \
    --start-period=1m \
    --retries=2 \
  CMD \
    test $(curl -s -o /dev/null -w '%{http_code}' http://localhost/) -eq 200

# this is set in usgs/httpd-php:latest, and repeated here for clarity
EXPOSE 80
