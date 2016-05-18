#!/bin/bash
# note that this convenience script is meant to run within vagrant vm only, not in the host machine
# trap ctrl-c and call ctrl_c()

echo '#### Starting php internal web server with xdebug...'
sudo XDEBUG_CONFIG="remote_enable=1 remote_host=10.0.2.2 idekey=phpstorm" php -d xdebug.idekey=phpstorm -d xdebug.remote_mode=req -S 0.0.0.0:80 -t web/
