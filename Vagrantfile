# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "debian/jessie64"
  config.vm.network :private_network, ip: "192.168.55.9"
  config.vm.hostname = "acmepay.local"

  config.vm.define :acmepay do |t|
  end

  config.vm.provider :virtualbox do |vb|
      vb.gui = false
  end

  config.vm.provision :salt do |salt|
    salt.masterless = true
    salt.colorize = true
    salt.verbose = true
    salt.minion_config = "minion.conf"
    salt.run_highstate = true
  end
end
