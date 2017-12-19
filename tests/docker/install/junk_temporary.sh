#!/usr/bin/env bash

docker exec -i smart_master \
mysql -uroot -psmart_master -e "GRANT REPLICATION SLAVE ON *.* to 'backup'@'%' identified by '123456';";

docker exec -i smart_master \
mysql -uroot -psmart_master -e "show master status;";

master_position=$( docker exec -i smart_master  mysql -uroot -psmart_master -e  'show master status \G' | grep Position | sed -n -e 's/^.*: //p');
master_file=$(docker exec -i smart_master  mysql -uroot -psmart_master -e 'show master status \G'       | grep File     | sed -n -e 's/^.*: //p');

docker exec -i smart_slave_one \
mysql -uroot -psmart_slave_one -e "change master to master_host='smart_master',master_user='backup',master_password='123456', master_log_file='${master_file}',master_log_pos=${master_position};start slave;";

docker exec -i smart_slave_two \
mysql -uroot -psmart_slave_two -e "change master to master_host='smart_master',master_user='backup',master_password='123456', master_log_file='${master_file}',master_log_pos=${master_position};start slave;";

docker exec -i smart_master \
mysql -uroot -psmart_master -e "source /tmp/install.sql;";

docker exec -i biz_master \
mysql -uroot -pbiz_master -e "source /tmp/install.sql;";
