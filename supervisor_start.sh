#!/bin/bash

#   Before starting create symlynk in dirrectory /etc/supervisor/conf.d. 
#   The full file path will be /etc/supervisor/conf.d/supervisor.cong
#   that points to file PROJECT_DIR/app/supervisord/supervisord.conf
#
#   The main config file "/etc/supervisor/supervisord.conf" includes all files from "conf.d" dirrectory
#   [include]
#   files = /etc/supervisor/conf.d/*.conf
#

echo "Truing to start supervisor with param -c /etc/supervisor/supervisord.conf"
sudo supervisord -c /etc/supervisor/supervisord.conf && echo 'Success' 

echo "Truing to start supervisorctl with param -c /etc/supervisor/supervisord.conf"
sudo supervisorctl -c /etc/supervisor/supervisord.conf && echo 'Success'
