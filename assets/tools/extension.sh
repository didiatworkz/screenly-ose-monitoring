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
echo "Screenly OSE Monitor extension"
echo
echo
}

header
echo "The installation can may be take a while.."
echo
echo
echo

echo "Check packages"
sleep 2
dpkg -s imagemagick &> /dev/null
if [ $? -ne 0 ]; then
    sudo apt update && sudo apt-get install x11-apps imagemagick -y
fi

header
echo "Prepair Screenly Player..."
sleep 2
wget https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/master/assets/img/loading.png -P /home/pi/

cat >/home/pi/screenshot.sh <<EOF
#!/bin/bash
cp /home/pi/loading.png /home/pi/screenly/static/img/screenshot.png
sleep 60;
while true; do
   DISPLAY=:0 XAUTHORITY=/var/run/lightdm/root/$DISPLAY xwd -root > /tmp/screenshot.xwd
   convert /tmp/screenshot.xwd /home/pi/screenly/static/img/screenshot.png
   sleep 10;
done
exit
EOF

sudo chmod +x /home/pi/screenshot.sh
sudo chown pi:pi /home/pi/screenshot.sh
( sudo crontab -l ; echo "@reboot sleep 20 && /home/pi/screenshot.sh >> /home/pi/screenshot.log 2>1" ) | sudo crontab -
echo "true" > /tmp/monitor.txt
sudo cp -f /tmp/monitor.txt /home/pi/screenly/static/monitor.txt

if [ "$1" != "installer" ]
then
    header
    echo "Screenly OSE Monitor extension successfuly installed"
    echo "Device is being restarted in 5 seconds!"
    sleep 5
    sudo reboot now
fi
exit