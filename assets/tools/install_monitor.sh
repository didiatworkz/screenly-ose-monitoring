#!/bin/bash
# Created by didiatworkz
# Screenly OSE Monitor 
#
# October 2018

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
echo "Screenly OSE Monitor"
echo
echo
}

header
if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit
fi

# Check if old version exists
if [ -e /var/www/html/assets/tools/version.txt ]; then
    UPDATE=true;
fi


header
# Check if screenly exists
if [ -e /home/pi/screenly/server.py ]; then
    SCREENLY=true;
	echo && read -p "Do you want to install the Monitor Extension for Screenly? (y/N)" -n 1 -r -s UPGRADE && echo
	if [ "$UPGRADE" == 'y' ]; then
	  MONITOR_EXTENSION=true
	else
	  MONITOR_EXTENSION=false
	fi
fi

# Install packeges
if [ "$UPDATE" != true ]; then
	sudo apt update
    if [ "$SCREENLY" != true ]; then
		apt install nginx -y
	fi
	apt install php-fpm php7.0-sqlite php7.0-curl git -y
fi

# Clone git repository
git clone https://github.com/didiatworkz/screenly-ose-monitor.git /tmp/monitor

# Install monitor extension
if [ "$MONITOR_EXTENSION" = true ]; then
    cp /tmp/monitor/assets/tools/extension.sh /tmp/extension.sh
	sed -i 's/reboot now//g' /tmp/extension.sh
	chmod +x /tmp/extension.sh
	/tmp/extension.sh "installer"
fi

# Copy files and set rights
mkdir -p /var/www/html
cp -rf /tmp/monitor/* /var/www/html/
sudo chown www-data:www-data /var/www/html/*

# Create nginx config
cat >/etc/nginx/sites-enabled/monitor.conf <<EOF
server {

        #Nginx should listen on port 80 for requests to yoursite.com
        listen 9000;
        server_name _;


        root /var/www/html/;
        index index.php;

        location ~ \.php$ {
                try_files $uri =404;
                include /etc/nginx/fastcgi.conf;
                fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        }
}
EOF

# Restart nginx
systemctl restart nginx
IP=$(/sbin/ip -o -4 addr list eth0 | awk '{print $4}' | cut -d/ -f1)

header
echo "Installation finished!"
echo
echo
echo "You can now reach the Screenly OSE Monitor at the address: http://$IP:9000"
exit


