#!/usr/bin/env bash

# redis
Title "Installing: Redis (This will take some time)"
sudo apt-get install redis-server -y -qq  &> /dev/null


# phpredis
Title "Installing: Predis"

Text "Cloning latatest phpredis repository ..."
git clone https://github.com/phpredis/phpredis.git &> /dev/null
cd phpredis &> /dev/null

Text "Moving to PHP 7 branch"
phpize &> /dev/null
./configure &> /dev/null

Text "Compiling Predis ..."
make &> /dev/null
sudo make install &> /dev/null
cd ..
rm -rf phpredis &> /dev/null

Text "Adding Predis to PHP 7 ..."
sudo echo "extension=redis.so" > /etc/php/7.1/mods-available/redis.ini
sudo ln -sf /etc/php/7.1/mods-available/redis.ini /etc/php/7.1/fpm/conf.d/20-redis.ini &> /dev/null
sudo ln -sf /etc/php/7.1/mods-available/redis.ini /etc/php/7.1/cli/conf.d/20-redis.ini &> /dev/null
sudo service php7.1-fpm restart &> /dev/null