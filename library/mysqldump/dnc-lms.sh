#!/bin/sh

path='/home/kaizen/web/htdocs/library/mysqldump'
db_name=("kaizen")

date=`date +%Y%m%d`
date_old=`date --date "7 days ago" +%Y%m%d`

for i in ${db_name[@]}
do
  mysqldump --user=root --password=5owfCs12X2uVNMcg --skip-lock-tables --no-create-db --single-transaction --databases ${i} > ${path}/${date}_${i}.sql
  chmod 777 ${path}/${date}_${i}.sql
  rm -f ${path}/${date_old}_${i}.sql
done
