#!/bin/bash
# Created by didiatworkz
# Screenly OSE Monitor
#
# January 2021
_ANSIBLE_VERSION=2.9.9
_BRANCH=v4.0
#_BRANCH=master

# ==========================

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
echo
echo
echo -e "[ \e[33mSOMO\e[39m ] Check if Screenly installed..."
if [ ! -e /home/pi/screenly/server.py ]
then
  echo -e "[ \e[33mSOMO\e[39m ] [ \e[32mNO\e[39m ] Screenly installed"
  echo "----------------------------------------------"
  echo
  echo -e "[ \e[33mSOMO\e[39m ] Start Server installation preperation"
  echo -e "[ \e[33mSOMO\e[39m ] Create /etc/ansible folder"
  sudo mkdir -p /etc/ansible
  echo -e "[ \e[33mSOMO\e[39m ] Add localhost connection to /etc/ansible/hosts"
  echo -e "[local]\nlocalhost ansible_connection=local" | sudo tee /etc/ansible/hosts > /dev/null
  echo -e "[ \e[33mSOMO\e[39m ] Update system"
  sudo apt update
  echo -e "[ \e[33mSOMO\e[39m ] Remove old package"
  sudo apt-get purge -y python-setuptools python-pip python-pyasn1 libffi-dev
  echo -e "[ \e[33mSOMO\e[39m ] Install new packages"
  sudo apt-get install -y python3-dev git-core libffi-dev libssl-dev
  echo -e "[ \e[33mSOMO\e[39m ] Install pip3 via python3"
  curl -s https://bootstrap.pypa.io/get-pip.py | sudo python3
  sudo pip3 install ansible=="$_ANSIBLE_VERSION"
  _SERVERMODE="listen 80 default_server;"
  _PORT=""

else
  echo -e "[ \e[33mSOMO\e[39m ] [ \e[93mYES\e[39m ] Screenly installed"
  _SERVERMODE="listen 9000;"
  _PORT=":9000"
fi
sleep 2
echo -e "[ \e[33mSOMO\e[39m ] Start installation..."
sleep 5
if [ -e /var/www/html/monitor/_functions.php ]
then
  _DEMOLOGIN=""
else
  _DEMOLOGIN="\e[94mUsername: \e[93mdemo\e[39m \n\e[94mPassword: \e[93mdemo\e[39m"
fi
echo -e "[ \e[33mSOMO\e[39m ] Remove old git repository if exists"
sudo rm -rf /tmp/monitor
echo -e "[ \e[33mSOMO\e[39m ] Clone repository"
sudo git clone --branch $_BRANCH https://github.com/didiatworkz/screenly-ose-monitor.git /tmp/monitor
cd /tmp/monitor/assets/tools/ansible/
echo -e "[ \e[33mSOMO\e[39m ] Create /var/www/monitor folder"
sudo mkdir -p /var/www/html
echo -e "[ \e[33mSOMO\e[39m ] Set installation parameters"
export SERVER_MODE=$_SERVERMODE
export MONITOR_BRANCH=$_BRANCH
echo -e "[ \e[33mSOMO\e[39m ] Start ansible installation"
sudo -E ansible-playbook site.yml
sudo systemctl restart nginx
IP=$(ip route get 8.8.8.8 | sed -n '/src/{s/.*src *\([^ ]*\).*/\1/p;q}')
sleep 2
echo
echo
echo
echo
echo
header
echo -e "[ \e[33mSOMO\e[39m ] Installation finished!"
echo
echo
echo -e "You can now start Screenly OSE Monitoring with the address: \n\e[93mhttp://$IP$_PORT\e[39m"
echo
echo -e "$_DEMOLOGIN"
echo
echo
echo
exit
