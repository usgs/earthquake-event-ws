#!/bin/bash

# container config
WS_CONTAINER="earthquake-event-ws"
DB_CONTAINER="pdl-index"
# database config
DB_NAME="product_index"
DB_USER="root" #to be used by this script
DB_PASSWORD="root-password"
PDL_DB_USER="user"
PDL_DB_PASSWORD="password"

sql_query() {
    docker exec ${DB_CONTAINER} mysql -u${DB_USER} -p${DB_PASSWORD} ${DB_NAME} -e "${1}"
}

# run database setup
docker exec -it ${WS_CONTAINER} php /var/www/apps/earthquake-event-ws/lib/sql/create_mysql_installer.php
docker cp ${WS_CONTAINER}:/var/www/apps/earthquake-event-ws/lib/sql/install_mysql.sql ./data/mysql/install_mysql.sql
#docker exec ${DB_CONTAINER} mysql -u${DB_USER} -p${DB_PASSWORD} ${DB_NAME} < ./data/mysql/install_mysql.sql
sql_query "source /var/lib/mysql/install_mysql.sql;"

# set up PDL user
sql_query "CREATE USER '${PDL_DB_USER}' IDENTIFIED BY '${PDL_DB_PASSWORD}';"
sql_query "GRANT SELECT ON ${DB_NAME}.* TO '${PDL_DB_USER}';"