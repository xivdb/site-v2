#!/usr/bin/env bash

# reload
Title "Restart services ..."
sudo service nginx restart &> /dev/null
sudo service php7.1-fpm restart &> /dev/null


# make sure all up to date
Title "Final update ..."
sudo apt-get autoremove -y -qq &> /dev/null
sudo apt-get update -y -qq &> /dev/null
sudo apt-get upgrade -y -qq &> /dev/null


# Install composer stuff
Title "Running: composer install (this will take a while...)"
cd /vagrant/site/
sudo -u $USER composer install &> /dev/null