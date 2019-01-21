#!/usr/bin/env bash


# basic
source /vagrant/vm/provision_colours.sh
source /vagrant/vm/provision_title.sh
source /vagrant/vm/provision_setup.sh

Gap

# install stuff
source /vagrant/vm/provision_install_nginx.sh
source /vagrant/vm/provision_install_extras.sh
source /vagrant/vm/provision_install_php.sh
source /vagrant/vm/provision_install_composer.sh
source /vagrant/vm/provision_install_mysql.sh
source /vagrant/vm/provision_install_redis.sh

# clean up
Gap

# fin
source /vagrant/vm/provision_finish.sh

# reoirt to use
Gap

Title "XIVDB vagrant setup"
Info "Go to: xivdb.dev"