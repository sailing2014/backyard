#!bin/sh
exec_time="2016012415"

echo "start update mysql...";

mysql -uroot -S /data/mysql/50161/data/mysql.sock < /home/bbc_helper/conf/executed.sql;

echo "end.";