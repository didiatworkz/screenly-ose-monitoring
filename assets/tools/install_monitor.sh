#!/bin/bash
# Created by didiatworkz
# Screenly OSE Monitor
#
# October 2020
_ANSIBLE_VERSION=2.9.9
_BRANCH=v4.0
#_BRANCH=master


header() {
#clear
tput setaf 172
cat << "EOF"
                            _
   ____                    | |
  / __ \__      _____  _ __| | __ ____
 / / _` \ \ /\ / / _ \| '__| |/ /|_  /
| | (_| |\ V  V / (_) | |  |   <  / /
 \ \__,_| \_/\_/ \___/|_|  |_|\_\/___|
  \____/                www.atworkz.de

        Screenly OSE Monitoring
EOF
tput sgr 0
echo
echo
echo
}

header
echo
echo
read -p "Do you want to install the Screenly OSE Monitoring? (y/N)" -n 1 -r -s INSTALL_BEGIN

if [ "$INSTALL_BEGIN" != "y" ]
then
    echo
    exit
fi

echo

# Check if screenly exists
echo Check if Screenly installed...
echo
if [ ! -e /home/pi/screenly/server.py ]
then
  echo -e "[ \e[32mNO\e[39m ] Screenly installed"
  echo -e "[ \e[93mYES\e[39m ] Standalone Installation"
  echo "----------------------------------------------"
  sudo mkdir -p /etc/ansible
  echo -e "[local]\nlocalhost ansible_connection=local" | sudo tee /etc/ansible/hosts > /dev/null
  sudo apt update
  sudo apt-get purge -y python-setuptools python-pip python-pyasn1 libffi-dev
  sudo apt-get install -y python3-dev git-core libffi-dev libssl-dev
  curl -s https://bootstrap.pypa.io/get-pip.py | sudo python3
  sudo pip3 install ansible=="$_ANSIBLE_VERSION"
  _SERVERMODE="listen 80 default_server;"
  _PORT=""

else
  echo -e "[ \e[93mYES\e[39m ] Screenly installed"
  _SERVERMODE="listen 9000;"
  _PORT=":9000"
fi
sleep 2
echo
echo
echo -e "\e[94mStart installation...\e[39m"
sleep 5
sudo rm -rf /tmp/monitor
sudo git clone --branch $_BRANCH https://github.com/didiatworkz/screenly-ose-monitor.git /tmp/monitor
cd /tmp/monitor/assets/tools/ansible/
sudo mkdir -p /var/www/html
export SERVER_MODE=$_SERVERMODE
export MONITOR_BRANCH=$_BRANCH
sudo -E ansible-playbook site.yml
cd /var/www/html/monitor/ && git rev-parse HEAD > ~/.monitor/latest_monitor
sudo systemctl restart nginx
ETH=$(/sbin/ip -o -4 addr list eth0 | awk '{print $4}' | cut -d/ -f1)
WLAN=$(/sbin/ip -o -4 addr list wlan0 | awk '{print $4}' | cut -d/ -f1)
if [ -z "$ETH" ]; then
 IP="$WLAN"
else
 IP="$ETH"
fi
sleep 2
echo
echo
echo
echo
echo
header
echo -e "\e[94mInstallation finished!"
echo
echo
echo -e "You can now reach the Screenly OSE Monitor at the address: \n\e[93mhttp://$IP$_PORT\e[39m"
echo
echo -e "\e[94mUsername: \e[93mdemo\e[39m"
echo -e "\e[94mPassword: \e[93mdemo\e[39m"
echo
echo
echo
exit
