# -*- mode: ruby -*-
# vi: set ft=ruby :

# Declare the virtual machine host name for this project
vm_name = "SearchEngine"

Vagrant.configure(2) do |config|
    config.vm.provider :virtualbox do |v|
        v.name = vm_name
        v.customize [
            "modifyvm", :id,
            "--name", vm_name,
            "--memory", 1024,
        ]
    end

    config.vm.box = "ubuntu/trusty64"
    config.vm.network "forwarded_port", guest: 80, host: 8080           # for nginx
    config.vm.network "forwarded_port", guest: 3306, host: 3306         # for mysql
    config.vm.network :private_network, ip: "192.168.33.100"

    # Setup synced folder
    config.vm.synced_folder "./", "/vagrant", nfs: true

    # To enable ssh login to the vm
    config.ssh.forward_agent = true

    # Run provision packages
    config.vm.provision "shell", path: "bin/provision_packages.sh"
    config.vm.provision "shell", path: "bin/provision_symfony.sh"
end

