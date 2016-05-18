#!/bin/sh

export DEBIAN_FRONTEND=noninteractive

# Apache
echo '#### Install Apache2'
if [ `dpkg -s apache2 | grep -c 'ok installed'` -ne 1 ]; then
    apt-get install -y apache2
    echo '####     done.'
else
    echo '####     already installed.'
fi

# MySQL
echo '#### Install MySQL'
if [ `dpkg -s mysql-server | grep -c 'ok installed'` -ne 1 ]; then
    echo "mysql-server mysql-server/root_password password 4root2use" | debconf-set-selections
    echo "mysql-server mysql-server/root_password_again password 4root2use" | debconf-set-selections
    apt-get -y install mysql-server
else
    echo '####     already installed.'
fi

echo '#### Configure mysql bind address'
sed -i 's/bind-address            = 127.0.0.1/bind-address            = 0.0.0.0/' /etc/mysql/my.cnf
echo '####     done.'


# PHP
echo '#### Install PHP'
if [ `dpkg -s php5-common | grep -c 'ok installed'` -ne 1 ]; then
    apt-get install -y php5 php5-common php5-xdebug php5-ldap
else
    echo '####     already installed.'
fi

echo '### Configure php timezone'
sed -i 's/;date\.timezone =/date.timezone = "Asia\/Singapore"/' /etc/php5/*/php.ini
echo '####     done.'

echo '#### Configure xdebug for development'
if [ `grep -c 'xdebug_remote_enable=on' /etc/php5/mods-available/xdebug.ini` -ne 1 ]; then
    tee -a /etc/php5/mods-available/xdebug.ini << 'EOF'
xdebug.remote_enable=on
xdebug.remote_host=10.0.2.2
xdebug.remote_mode=req
xdebug.idekey=phpstorm
EOF
    echo '####     done.'
else
    echo '####     already configured.'
fi

# Phpmyadmin, notice that by default phpmyadmin is configured with Apache2
echo '#### Install phpmyadmin and set default root password'
if [ `dpkg -s phpmyadmin | grep -c 'ok installed'` -ne 1 ]; then
    echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/app-password-confirm password 4root2use" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/mysql/admin-pass password 4root2use" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/mysql/app-pass password 4root2use" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | debconf-set-selections
    apt-get install -y phpmyadmin
    echo '####     done.'
else
    echo '####     already installed.'
fi

# Some other dev tools
echo '#### Install git, vim, tree'
if [ `dpkg -s git | grep -c 'ok installed'` -ne 1 ]; then
    apt-get install -y git vim tree
    echo '####     done.'
else
    echo '####     already installed.'
fi


echo Install composer and symfony
if [ ! -f /usr/bin/composer ]; then
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
fi

if [ ! -f /usr/local/bin/symfony ]; then
	curl -LsS http://symfony.com/installer -o /usr/local/bin/symfony
	chmod a+x /usr/local/bin/symfony
fi

chmod -R a+w /vagrant/var/cache /vagrant/var/logs


echo '#### Restart all services'
service mysql restart
service apache2 restart
echo '####     done.'