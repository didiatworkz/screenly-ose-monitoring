#!/bin/bash
# Created by didiatworkz
# Screenly OSE Monitor 
#
# March 2019

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
echo "         Screenly OSE Monitoring"
echo
echo
}

header
echo
echo
read -p "Do you want to install the Screenly OSE Monitoring? (y/N)" -n 1 -r -s INSTALL_BEGIN

if [ "$INSTALL_BEGIN" != 'y' ]
then
    echo
    exit
fi

header
echo

# Check if screenly exists
echo Check if Screenly installed...
echo
if [ ! -e /home/pi/screenly/server.py ]
then
    echo -e "[ \e[32mNO\e[39m ] Screenly installed"
	echo
	echo Installation aborted because no Screenly was found!
	echo Please check if the file /home/pi/screenly/server.py exists!
	exit

else
    echo -e "[ \e[93mYES\e[39m ] Screenly installed"
fi
sleep 2
header
echo -e "\e[94mStart installation...\e[39m"
sleep 5
ansible localhost -m git -a "repo=${1:-https://github.com/didiatworkz/screenly-ose-monitor.git} dest=/tmp/monitor version=dev"
cd  /tmp/monitor/assets/tools/ansible/
ansible-playbook site.yml
cd /var/www/html/monitor/ && git rev-parse HEAD > ~/.monitor/latest_monitor
IP=$(/sbin/ip -o -4 addr list eth0 | awk '{print $4}' | cut -d/ -f1)
sleep 2
header
echo -e "\e[94mInstallation finished!"
echo
echo
echo -e "You can now reach the Screenly OSE Monitor at the address: \n\e[93mhttp://$IP:9000\e[39m"
echo
echo -e "\e[94mUsername: \e[93mdemo\e[39m"
echo -e "\e[94mPassword: \e[93mdemo\e[39m"
echo
echo
echo
exit
exit




