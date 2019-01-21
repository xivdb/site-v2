#!/usr/bin/env bash


# install composer
Title "Install: Composer ..."
curl -sS https://getcomposer.org/installer | php &> /dev/null
mv composer.phar /usr/local/bin/composer &> /dev/null
chmod +x /usr/local/bin/composer &> /dev/null
Info "Ready for composer install at anytime"