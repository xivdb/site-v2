#!/usr/bin/env bash

# php 7
Title "Installing: PHP 7 + Modules ..."
Text "Set php repository"
sudo add-apt-repository ppa:ondrej/php -y &> /dev/null
Text "Updating"
sudo apt-get update -y &> /dev/null
Text "Installing PHP"
sudo apt-get install -y -qq php7.1-fpm &> /dev/null
Text "Installing PHP modules"
sudo apt-get install -y -qq php-apcu php7.1-dev php7.1-cli php7.1-json php7.1-fpm php7.1-intl php7.1-mysql php7.1-sqlite php7.1-curl php7.1-mcrypt php7.1-gd php7.1-mbstring php7.1-dom php7.1-xml php7.1-zip php7.1-tidy &> /dev/null


# change some settings
Title "Adjusting PHP settings"
sed -i 's|display_errors = Off|display_errors = On|' /etc/php/7.1/fpm/php.ini
sed -i 's|upload_max_filesize = 2M|upload_max_filesize = 64M|' /etc/php/7.1/fpm/php.ini
sed -i 's|post_max_size = 8M|post_max_size = 64M|' /etc/php/7.1/fpm/php.ini
sed -i 's|max_execution_time = 30|max_execution_time = 300|' /etc/php/7.1/fpm/php.ini
sed -i 's|memory_limit = 128M|memory_limit = 2G|' /etc/php/7.1/fpm/php.ini
sed -i 's|;request_terminate_timeout = 0|request_terminate_timeout = 300|' /etc/php/7.1/fpm/pool.d/www.conf
sed -i "s|www-data|$USER|" /etc/php/7.1/fpm/pool.d/www.conf