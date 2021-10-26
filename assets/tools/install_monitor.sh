#!/bin/bash
# Created by didiatworkz
# Screenly OSE Monitoring
#
# October 2021
_BRANCH=v4.2
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

        Screenly OSE Monitoring (SOMO)
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

#check if previus version installed (<=4.1)
#check if previus version installed (docker)

echo
echo
echo
echo -e "[ \e[33mSOMO\e[39m ] Check if port 0.0.0.0:80 in use..."
if ! nc -z localhost 80; then
  echo -e "[ \e[33mSOMO\e[39m ] 0.0.0.0:80 is not in use!"
  echo "----------------------------------------------"
  echo
  
  _SERVERMODE="listen 80 default_server;"
  _PORT=""

else
  echo -e "[ \e[33mSOMO\e[39m ] 0.0.0.0:80 is in use!"
  echo -e "[ \e[33mSOMO\e[39m ] Choose port 0.0.0.0:9000"
  _SERVERMODE="listen 9000;"
  _PORT=":9000"
fi
echo 
echo -e "[ \e[33mSOMO\e[39m ] Start preparation for installation"
sleep 2
echo -e "[ \e[33mSOMO\e[39m ] Update apt cache"
sudo apt update
echo -e "[ \e[33mSOMO\e[39m ] Install new packages"
sudo apt-get install --no-install-recommends git-core netcat -y
echo -e "[ \e[33mSOMO\e[39m ] Install latest docker version"
curl -sSL https://get.docker.com | sh
echo -e "[ \e[33mSOMO\e[39m ] Add $(whomi) to group 'docker'"
sudo usermod -aG docker $(whoami)
sleep 5
if [ -e /var/www/html/monitor/_functions.php ]
then
  _DEMOLOGIN=""
else
  _DEMOLOGIN="\e[94mUsername: \e[93mdemo\e[39m \n\e[94mPassword: \e[93mdemo\e[39m"
fi
echo -e "[ \e[33mSOMO\e[39m ] Remove old git repository if exists"
sudo rm -rf /home/$(whoami)/somo
echo -e "[ \e[33mSOMO\e[39m ] Create /home/$(whoami)/somo folder"
sudo mkdir -p /home/$(whoami)/somo
echo -e "[ \e[33mSOMO\e[39m ] Clone repository"
sudo git clone --branch $_BRANCH https://github.com/didiatworkz/screenly-ose-monitoring.git /home/$(whoami)/somo
echo -e "[ \e[33mSOMO\e[39m ] Set installation parameter"

echo -e "[ \e[33mSOMO\e[39m ] Create and activate systemd service"
#create file in tmp
#copy file to systemd
#enable service
echo -e "[ \e[33mSOMO\e[39m ] Activate cronjob"
#copy cronjob to cron.d
echo -e "[ \e[33mSOMO\e[39m ] Register somo in /usr/bin"
#copy somo file
#set chmod


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
echo -e "Screenly OSE Monitoring can be started from this address: \n\e[93mhttp://$IP$_PORT\e[39m"
echo
echo -e "$_DEMOLOGIN"
echo
echo
echo
exit
