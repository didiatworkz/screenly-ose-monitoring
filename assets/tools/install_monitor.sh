#!/bin/bash
# Created by didiatworkz
# Screenly OSE Monitor 
#
# November 2018

header() {
clear
cat << "EOF"
                            _
   ____                    | |
  / __ \__      _____  _ __| | __ ____
 / / _` \ \ /\ / / _ \| '__| |/ /|_  /
| | (_| |\ V  V / (_) | |  |   <  / /
 \ \__,_| \_/\_/ \___/|_|  |_|\_\/___|
  \____/                www.atworkz.de

EOF
echo
echo "    Screenly OSE Monitor"
echo
echo
}

header
echo
echo
read -p "Do you want to install the Screenly OSE Monitor? (y/N)" -n 1 -r -s INSTALL_BEGIN

if [ "$INSTALL_BEGIN" != 'y' ]
then
    echo
    exit
fi

header
echo

# Check if old version exists
echo
if [ -e /var/www/html/monitor/assets/tools/version.txt ]
then
    UPDATE=1
    echo -e "[ \e[93mYES\e[39m ] Found old Monitor Script"
else
    echo -e "[ \e[32mNO\e[39m ] Found old Monitor Script"
fi

# Check if screenly exists
echo
if [ -e /home/pi/screenly/server.py ]
then
    SCREENLY=1
    echo -e "[ \e[93mYES\e[39m ] Found Screenly"
    echo
    echo
    echo
    read -p "Do you want to install the Monitor Extension for Screenly? (y/N)" -n 1 -r -s UPGRADE
    if [ "$UPGRADE" == 'y' ]
    then
        MONITOR_EXTENSION=1
    else
        MONITOR_EXTENSION=0
    fi
else
    echo -e "[ \e[32mNO\e[39m ] Found Screenly"
fi
sleep 2
header
echo -e "\e[94mStart installation...\e[39m"
sleep 5

# Install packeges
echo
if [ "$UPDATE" != "1" ]
then
    sudo apt update
    if [ "$SCREENLY" != "1" ]
    then
        sudo apt install nginx -y
    fi
    sudo apt install git php-fpm php7.0-sqlite php7.0-curl -y
fi

# Clone git repository
git clone https://github.com/didiatworkz/screenly-ose-monitor.git /tmp/monitor

if [ "$UPDATE" = "1" ]
then
	OLD_VERSION=$(</var/www/html/monitor/assets/tools/version.txt)
	
	if [ "$OLD_VERSION" = "1.1" ]
	then
		echo
	else
	    sudo rm -f /tmp/monitor/dbase.db
	fi
fi

# Install monitor extension
if [ "$MONITOR_EXTENSION" = "1" ]
then
    sudo cp /tmp/monitor/assets/tools/extension.sh /tmp/extension.sh
    sudo sed -i 's/reboot now//g' /tmp/extension.sh
    sudo chmod +x /tmp/extension.sh
    header
    echo -e "\e[94mStart extension installation...\e[39m"
    sleep 5
    sudo /tmp/extension.sh "installer"
    echo
    echo -e "\e[94mExtension installed!\e[39m"
fi

sleep 2
header
echo -e "\e[94mStart configuration...\e[39m"
# Copy files and set rights
sudo mkdir -p /var/www/html/monitor
sudo cp -rf /tmp/monitor/* /var/www/html/monitor/
sudo chown www-data:www-data /var/www/html/monitor
sudo chown www-data:www-data /var/www/html/monitor/*

# Create nginx config
cat >/tmp/monitor.conf <<EOF
server {

        listen 9000;
        server_name _;


        root /var/www/html/monitor/;
        index index.php;

        location ~ \.php$ {
                include /etc/nginx/fastcgi.conf;
                fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        }
}
EOF

sudo cp -f /tmp/monitor.conf /etc/nginx/sites-enabled/monitor.conf

# Restart nginx
sudo systemctl restart nginx
IP=$(/sbin/ip -o -4 addr list eth0 | awk '{print $4}' | cut -d/ -f1)

sleep 2

header
echo -e "\e[94mInstallation finished!"
echo
echo
echo -e "You can now reach the Screenly OSE Monitor at the address: \e[93mhttp://$IP:9000\e[39m"
echo
echo -e "\e[94mUsername: \e[93mdemo\e[39m"
echo -e "\e[94mPassword: \e[93mdemo\e[39m"
echo
echo
echo
exit


