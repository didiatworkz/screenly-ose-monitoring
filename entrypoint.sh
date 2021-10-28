#!/bin/bash

cat << "EOF"
                            _
   ____                    | |
  / __ \__      _____  _ __| | __ ____
 / / _` \ \ /\ / / _ \| '__| |/ /|_  /
| | (_| |\ V  V / (_) | |  |   <  / /
 \ \__,_| \_/\_/ \___/|_|  |_|\_\/___|
  \____/                www.atworkz.de

        Screenly OSE Monitoring (SOMO)
EOF

echo
echo
echo
export > /root/env.sh

#Fix cron execution
touch /etc/crontab /etc/cron.*/*
service cron start

CONTAINER_ALREADY_STARTED="CONTAINER_ALREADY_STARTED_PLACEHOLDER"
if [ ! -e $CONTAINER_ALREADY_STARTED ]; then
    touch $CONTAINER_ALREADY_STARTED
    echo "-- First container startup --"
    FILE=/var/www/html/assets/data/database.db
    DIRECTORY=/var/www/html/assets/data/avatars
    if [ ! -f "$FILE" ]; then
        echo "-- Create Database --"
        cp /var/www/html/dbase.sample.db "$FILE"
        chown www-data:www-data "$FILE"
        echo "For first login use:"
        echo
        echo "Username: demo"
        echo "Password: demo"
    fi
    if [ ! -d "$DIRECTORY" ]; then
        mkdir -p "$DIRECTORY"
        chown www-data:www-data "$DIRECTORY"
    fi
fi
if [ -x "$(command -v usermod)" ] ; then
    echo "-- Change www-data to user id: $UID --"
    usermod -u "${UID}" www-data
    echo "-- Change www-data to group id: $GID --"
    groupmod -g "${GID}" www-data
    echo "-- Activate cronjob --"
    crontab /etc/cron.d/somo
fi
exec apache2-foreground
