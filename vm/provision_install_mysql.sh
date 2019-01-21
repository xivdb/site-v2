#!/usr/bin/env bash

# mysql mariadb
Title "Install: MariaDB ..."
echo "mysql-server mysql-server/root_password password xivdb" | debconf-set-selections &> /dev/null
echo "mysql-server mysql-server/root_password_again password xivdb" | debconf-set-selections &> /dev/null


Text "Installing"
sudo apt-get install software-properties-common &> /dev/null
sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db &> /dev/null
sudo add-apt-repository 'deb [arch=amd64,i386,ppc64el] http://mirror.sax.uk.as61049.net/mariadb/repo/10.2/ubuntu trusty main' &> /dev/null
sudo apt-get update &> /dev/null
sudo apt-get install mariadb-server -y -qq  &> /dev/null


# mysql settings
Text "Setup mysql configuration ..."
sed -i 's|max_connections         = 100|max_connections         = 300|' /etc/mysql/my.cnf &> /dev/null
sed -i 's|slow_query_log_file     =|#slow_query_log_file     =|' /etc/mysql/my.cnf &> /dev/null
sed -i 's|long_query_time =|#long_query_time =|' /etc/mysql/my.cnf &> /dev/null
sed -i 's|log_bin                 =|#log_bin                 =|' /etc/mysql/my.cnf &> /dev/null
sed -i 's|log_bin_index           =|#log_bin_index           =|' /etc/mysql/my.cnf &> /dev/null
sed -i 's|expire_logs_days        =|#expire_logs_days        =|' /etc/mysql/my.cnf &> /dev/null


# default database
Text "Create default database and user ..."
Info "Importing setup"
mysql -uroot -pxivdb < /vagrant/vm/setup.sql
Info "Importing db"
#mysql -uroot -pxivdb xivdb < /vagrant/vm/xivdb.sql
Info "user xivdb, pass: xivdb"