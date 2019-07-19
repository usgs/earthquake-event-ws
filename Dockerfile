ARG BUILD_IMAGE=usgs/node:latest
ARG FROM_IMAGE=usgs/httpd-php:latest


FROM ${BUILD_IMAGE} as buildenv


# php required for pre-install
USER root
RUN yum install -y \
    php

COPY --chown=usgs-user:usgs-user . /earthquake-event-ws
WORKDIR /earthquake-event-ws

# Build project
USER usgs-user
RUN /bin/bash --login -c "\
    npm install -g grunt-cli && \
    grunt builddist \
    "

FROM ${FROM_IMAGE}

COPY --from=buildenv \
    /earthquake-event-ws/.build/src/ \
    /var/www/apps/earthquake-event-ws/

# Configure the application and install it:
#   - Run pre-install for application (generating httpd.conf)
#   - Link http configuration
RUN /bin/bash --login -c "\
    php /var/www/apps/earthquake-event-ws/lib/pre-install.php --non-interactive --skip-db && \
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
