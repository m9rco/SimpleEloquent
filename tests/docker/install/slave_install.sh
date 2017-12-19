#!/bin/bash

# set basic master info
mysql -uroot -e "RESET MASTER; \
    CHANGE MASTER TO \
    MASTER_HOST=$REPLICATION_MASTER_HOST, \
    MASTER_PORT=$REPLICATION_MASTER_PORT, \
    MASTER_USER=$REPLICATION_USER, \
    MASTER_PASSWORD=$REPLICATION_PASSWORD;"

# dump data from master to slave
mysqldump \
    --host=$REPLICATION_MASTER_HOST \
    --port=$REPLICATION_MASTER_PORT \
    --user=root \
    --protocol=tcp \
    --master-data=1 \
    --add-drop-database \
    --flush-logs \
    --flush-privileges \
    --all-databases \
    | mysql -uroot

# start slave
mysql -uroot -e "START SLAVE;"