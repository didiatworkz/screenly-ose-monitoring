#!/bin/bash
# Created by didiatworkz
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
echo "Screenly OSE Monitor addon"
echo
echo
}

header
echo "Prepair Screenly Player..."
sleep 2

if [ ! -e /home/pi/screenly/server.py ]
then
	echo
	echo "No ScreenlyOSE found!"
	exit
fi

header
echo "The installation can may be take a while.."
echo
echo
echo
sudo -u pi ansible localhost -m git -a  "repo=${1:-https://github.com/didiatworkz/screenly-ose-monitoring-addon.git} dest=/tmp/addon version=master"
cd  /tmp/addon/
sudo -E ansible-playbook addon.yml

header
echo "Screenly OSE Monitor addon successfuly installed"
echo "Device is being restarted in 5 seconds!"
sleep 5
sudo reboot now
