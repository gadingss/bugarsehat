#!/bin/bash.


docker exec mysql-db bash -c '/usr/bin/mysqldump -u root --password=${MYSQL_ROOT_PASSWORD} --databases bugar-sehat --skip-comments > /backup_db/backup_bugar_sehat_$(date +"%Y-%m-%d").sql'
