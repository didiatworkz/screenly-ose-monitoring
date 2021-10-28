#!/bin/bash
# Created by didiatworkz
# Screenly OSE Monitoring
#
# October 2021
_BRANCH=v4.2
_DBRANCH=nightly
#_BRANCH=master

# ==========================
PORT=""

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
echo
if [ "$INSTALL_BEGIN" != "y" ]
then
    echo
    exit
fi

#check if previous version installed (<=4.1)
FILE=/var/www/html/monitor/_functions.php
if [ -f "$FILE" ]; then
    echo -e "[ \e[33mSOMO\e[39m ] Old SOMO version found (<=4.1)"
    echo
    read -p "Do you want a backup of the old database? (y/N)" -n 1 -r -s BACKUP_C1
    echo
    if [ "$BACKUP_C1" == "y" ]
    then
        echo -e "[ \e[33mSOMO\e[39m ] Start Backup"
        sleep 2
        echo -e "[ \e[33mSOMO\e[39m ] Create backup folder"
        mkdir -p /home/"$(whoami)"/somo_backup
        mkdir -p /home/"$(whoami)"/somo_backup/avatars

        echo -e "[ \e[33mSOMO\e[39m ] Search for old database..."
        DB_FILE=$(ls /var/www/html/monitor/ | grep -x '.\{20,\}.db')
        if [ -z "$DB_FILE" ]
        then
            echo -e "[ \e[33mSOMO\e[39m ] Database Hash not found!"
            echo -e "[ \e[33mSOMO\e[39m ] Search for default database"
            DB_FILE=$(ls /var/www/html/monitor/ | grep dbase.db)
        fi
        if [ -z "$DB_FILE" ]
        then
            echo -e "[ \e[33mSOMO\e[39m ] Default database not found!"
            echo -e "[ \e[33mSOMO\e[39m ] Cancel installation!"
            echo
            echo
            echo -e "[ \e[33mSOMO\e[39m ] Please check SOMO Wiki - Error 2020"
            echo -e "[ \e[33mSOMO\e[39m ] Visit: https://git.io/JiSg5"
            exit
        fi
        echo -e "[ \e[33mSOMO\e[39m ] Database found: $DB_FILE"
        echo -e "[ \e[33mSOMO\e[39m ] Backup database: $DB_FILE"
        sudo cp -f /var/www/html/monitor/"$DB_FILE" /home/"$(whoami)"/somo_backup/database.db
        sudo chown "$(whoami)":"$(whoami)" /home/"$(whoami)"/somo_backup/database.db

        echo -e "[ \e[33mSOMO\e[39m ] Backup user avatars"
        sudo cp -rf /var/www/html/monitor/assets/img/avatars /home/"$(whoami)"/somo_backup
        sudo chown -R "$(whoami)":"$(whoami)" /home/"$(whoami)"/somo_backup/avatars

        echo -e "[ \e[33mSOMO\e[39m ] Backup finished!"
        echo
        BACKUP_C1=1
        sleep 2
    fi
fi

# Cleanup old SOMO files
FILE=/etc/nginx/sites-enabled/monitoring.conf
if [ -f "$FILE" ]; then
    echo -e "[ \e[33mSOMO\e[39m ] Start cleanup"
    sleep 2
    echo -e "[ \e[33mSOMO\e[39m ] Remove /var/www/html/monitor"
    sudo rm -rf /var/www/html/monitor
    echo -e "[ \e[33mSOMO\e[39m ] Remove /usr/share/somo"
    sudo rm -rf /usr/share/somo
    echo -e "[ \e[33mSOMO\e[39m ] Remove /etc/nginx/sites-enabled/monitoring.conf"
    sudo rm -rf /etc/nginx/sites-enabled/monitoring.conf
    echo -e "[ \e[33mSOMO\e[39m ] Restart nginx service"
    sudo systemctl restart nginx
    echo -e "[ \e[33mSOMO\e[39m ] Remove /usr/bin/somo"
    sudo rm -rf /usr/bin/somo
    echo -e "[ \e[33mSOMO\e[39m ] Cleanup complete!"
    sleep 2
    echo

    # Remove nginx?
    echo -e "[ \e[33mSOMO\e[39m ] Check if nginx installed..."
    sleep 2
    if command -v nginx &> /dev/null
    then
        echo -e "[ \e[33mSOMO\e[39m ] nginx installed!"
        echo -e "[ \e[33mSOMO\e[39m ] Check if Screenly OSE installed..."
        FILE=/home/"$(whoami)"/screenly/server.py
        if [ -f "$FILE" ]; then  
            echo -e "[ \e[33mSOMO\e[39m ] Screenly OSE installed!"
            echo -e "[ \e[33mSOMO\e[39m ] Check if nginx needed anymore..."
            DOCK_IMAGE=$(docker images -q screenly/srly-ose-server)
            if [ -n "$DOCK_IMAGE" ]; then 
                echo -e "[ \e[33mSOMO\e[39m ] nginx not needed anymore!"
                ASK_NGINX=1
            else
                echo -e "[ \e[33mSOMO\e[39m ] nginx can't be removed!"
                ASK_NGINX=0
            fi
        else 
            echo -e "[ \e[33mSOMO\e[39m ] nginx not needed anymore!"
            ASK_NGINX=1
        fi
    else 
        echo -e "[ \e[33mSOMO\e[39m ] nginx isn't installed anymore!"
        ASK_NGINX=0    
    fi
fi
sleep 2

#Remove nginx
if [ "$ASK_NGINX" == "1" ]
then
    echo -e "[ \e[33mSOMO\e[39m ] SOMO no longer requires the following..."
    echo -e "[ \e[33mSOMO\e[39m ] packages due to a system change:"
    echo "- nginx-light"
    echo "- php-fpm"
    echo "- php-sqlite3"
    echo "- php-curl"
    echo "- php-ssh2"
    read -p "Do you want to remove nginx-light, php-fpm, php-sqlite3, php-curl, php-ssh2 (y/N)" -n 1 -r -s REM_PACK
    echo
    if [ "$REM_PACK" == "y" ]
    then
        echo -e "[ \e[33mSOMO\e[39m ] Start removing the packages..."
        sudo apt remove nginx-light php-fpm php-sqlite3 php-curl php-ssh2 -y

        echo -e "[ \e[33mSOMO\e[39m ] Removed all packages that are no longer needed!"
    fi
fi


#check if previous version installed (docker)
DOCK_ID=$(docker ps -q -f name=somo)
if [ -n "$DOCK_ID" ]; then
    echo -e "[ \e[33mSOMO\e[39m ] Old SOMO version found (docker)"
    sleep 2
    PORT=$(sudo docker container port "$DOCK_ID" | head -n 1 | awk '{print $3}' | sed s'/0.0.0.0://')
    _PORT=":$PORT"

    echo -e "[ \e[33mSOMO\e[39m ] Stop somo service..."
    sudo systemctl stop docker.somo

    echo -e "[ \e[33mSOMO\e[39m ] Start Backup"

    echo -e "[ \e[33mSOMO\e[39m ] Create backup folder"
    mkdir -p /home/"$(whoami)"/somo_backup
    mkdir -p /home/"$(whoami)"/somo_backup/avatars

    echo -e "[ \e[33mSOMO\e[39m ] Backup database: $DB_FILE"
    cp -f /home/"$(whoami)"/somo/database.db /home/"$(whoami)"/somo_backup/database.db

    echo -e "[ \e[33mSOMO\e[39m ] Backup user avatars"
    sudo cp -rf /home/"$(whoami)"/somo/assets/img/avatars /home/"$(whoami)"/somo_backup

    echo -e "[ \e[33mSOMO\e[39m ] Backup finished!"
    BACKUP_C2=1
    sleep 2
fi
echo 
echo -e "[ \e[33mSOMO\e[39m ] Start preparation for installation..."
sleep 2
echo -e "[ \e[33mSOMO\e[39m ] Update apt cache..."
sudo apt update

echo -e "[ \e[33mSOMO\e[39m ] Install new packages..."
sudo apt-get install --no-install-recommends git-core netcat -y

echo -e "[ \e[33mSOMO\e[39m ] Install latest docker version..."
if command -v docker &> /dev/null
then
    echo -e "[ \e[33mSOMO\e[39m ] Docker already installed!"
else
    curl -sSL https://get.docker.com | sh
fi

echo -e "[ \e[33mSOMO\e[39m ] Add $(whoami) to group 'docker'..."
sudo usermod -aG docker "$(whoami)"

echo -e "[ \e[33mSOMO\e[39m ] Pull docker image..."
sudo docker pull atworkz/somo:"$_DBRANCH"
sleep 5

if [ -z "$PORT" ]; then
  echo -e "[ \e[33mSOMO\e[39m ] Check if port 0.0.0.0:80 in use..."
  if ! nc -z localhost 80; then
    PORT="80"
    _PORT=""
  else
    if ! nc -z localhost 9000; then
      PORT="9000"
      _PORT=":9000"
    else
      echo -e "[ \e[33mSOMO\e[39m ] 0.0.0.0:9000 is in use!"
      read -p "Enter a free port 0.0.0.0:xxxx" -r MANUEL_PORT
      echo
      PORT="$MANUEL_PORT"
      _PORT=":$MANUEL_PORT"
    fi
  fi
fi
echo -e "[ \e[33mSOMO\e[39m ] Set port in config to: 0.0.0.0:$PORT!"

if [ -e /home/"$(whoami)"/somo/_functions.php ]
then
    UPGRADE=1
else
    UPGRADE=0
    cp /home/"$(whoami)"/somo/dbase.sample.db /home/"$(whoami)"/somo/database.db
fi

echo -e "[ \e[33mSOMO\e[39m ] Create /home/$(whoami)/somo folder"
sudo rm -rf /home/"$(whoami)"/somo
mkdir -p /home/"$(whoami)"/somo

echo -e "[ \e[33mSOMO\e[39m ] Clone repository"
git clone --branch "$_BRANCH" https://github.com/didiatworkz/screenly-ose-monitoring.git /home/"$(whoami)"/somo

echo -e "[ \e[33mSOMO\e[39m ] Create and activate systemd service"

cat <<EOT > /tmp/docker.somo.service
[Unit]
Description=Screenly OSE Monitoring Service
After=docker.service
Wants=network-online.target docker.socket
Requires=docker.socket

[Service]
Restart=always
ExecStart=/usr/bin/docker run --rm --name somo -v /home/$(whoami)/somo:/var/www/html -p $PORT:80 atworkz/somo:$_DBRANCH

[Install]
WantedBy=multi-user.target
EOT
sudo cp -f /tmp/docker.somo.service /etc/systemd/system/docker.somo.service
sudo systemctl enable docker.somo
sudo systemctl daemon-reload

echo -e "[ \e[33mSOMO\e[39m ] Register somo command"
sudo cp -f /home/"$(whoami)"/somo/assets/tools/somo /usr/bin/somo
sudo chmod 755 /usr/bin/somo

echo -e "[ \e[33mSOMO\e[39m ] Create and activate cronjob"
cat <<EOT > /tmp/somo
0 */2 * * * * "$(whoami)" /usr/bin/somo --scriptupdate
EOT
sudo cp -f /tmp/somo /etc/cron.d/somo

if [ "$BACKUP_C1" == "1" ]
then
    echo -e "[ \e[33mSOMO\e[39m ] Restore Backup..."
    cp -f /home/"$(whoami)"/somo_backup/database.db /home/"$(whoami)"/somo/database.db
    cp -rf /home/"$(whoami)"/somo_backup/avatars /home/"$(whoami)"/somo/assets/img/avatars
    sudo rm -rf /home/"$(whoami)"/somo_backup
    echo -e "[ \e[33mSOMO\e[39m ] Restore complete!"
fi

if [ "$BACKUP_C2" == "1" ]
then
    echo -e "[ \e[33mSOMO\e[39m ] Restore Backup..."
    cp -f /home/"$(whoami)"/somo_backup/database.db /home/"$(whoami)"/somo/database.db
    cp -f /home/"$(whoami)"/somo_backup/assets /home/"$(whoami)"/somo/img/assets
    sudo rm -rf /home/"$(whoami)"/somo_backup
    echo -e "[ \e[33mSOMO\e[39m ] Restore complete!"
fi

echo -e "[ \e[33mSOMO\e[39m ] Start docker.somo.service"
sudo systemctl start docker.somo.service

if [ "$UPGRADE" == "1" ]
then
  _DEMOLOGIN=""
else
  _DEMOLOGIN="\e[94mUsername: \e[93mdemo\e[39m \n\e[94mPassword: \e[93mdemo\e[39m"
fi

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
if [ "$UPGRADE" == "0" ]
then
    read -p "The system need to be restarted. Do you want to this now? (y/N)" -n 1 -r -s REBOOT
    echo
    if [ "$REBOOT" == "y" ]
    then
        sudo reboot
    fi
fi
exit
