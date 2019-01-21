#!/usr/bin/env bash

Title "Set variables"
cd /vagrant &> /dev/null
USER=vagrant &> /dev/null
sudo locale-gen en_GB.UTF-8 &> /dev/null



Title "Updating Ubuntu packages and running upgrades ..."
sudo apt-get update -y -qq &> /dev/null
sudo apt-get upgrade -y -qq &> /dev/null



Title "Installing python software properties and common packages ..."
sudo apt-get install -y python-software-properties &> /dev/null
sudo apt-get install -y software-properties-common &> /dev/null