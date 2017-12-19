#!/bin/bash


cat /var/run/mysqld/mysqld.sock;

mysql -h $MYSQL_HOST  -uroot -p$MYSQL_ROOT_PASSWORD -e "GRANT \
    REPLICATION SLAVE, \
    REPLICATION CLIENT \
    ON *.* \
    TO $REPLICATION_USER@% \
    IDENTIFIED BY $REPLICATION_PASS; \
    FLUSH PRIVILEGES;"

mysql -uroot -p$MYSQL_ROOT_PASSWORD < /tmp/install.sql


GRANT REPLICATION SLAVE ON *.* to '$REPLICATION_USER'@'%' identified by '123456';