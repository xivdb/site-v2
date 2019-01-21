# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty64"

  # provision
  config.vm.provision :shell, path: "vm/provision.sh"

  # provider
  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    vb.memory = 4096
    vb.cpus = 2
  end

  # network
  config.vm.network "private_network", ip: "33.33.33.33"
  config.vm.hostname = "xivdb.local"
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.aliases = %w(
    api.xivdb.local
    secure.xivdb.local
    dashboard.xivdb.local
    xivdb.adminer
    xivsync.local
  )

  # sync folder
  config.vm.synced_folder ".", "/vagrant", type: "nfs"
end
