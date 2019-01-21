#!/usr/bin/env bash

Title "Install NGINX"
Text "Set nginx repository"
sudo add-apt-repository ppa:nginx/stable -y &> /dev/null
Text "Updating ..."
sudo apt-get update &> /dev/null
Text "Installing nginx stable"
sudo apt-get install -y nginx &> /dev/nul



Text "Moving nginx configs"
rm /etc/nginx/sites-available/default &> /dev/null
sudo cp /vagrant/vm/nginx/default /etc/nginx/sites-available/default &> /dev/null
sudo cp /vagrant/vm/nginx/nginx.conf /etc/nginx/nginx.conf &> /dev/null
sed -i "s|user www-data|user $USER|" /etc/nginx/nginx.conf