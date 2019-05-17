#!/bin/bash
# Screenly OSE Monitoring Add-on
cp /var/www/html/extension/loading.png /var/www/html/extension/screen/screenshot.png
sleep 60;
while true; do
   DISPLAY=:0 XAUTHORITY=/var/run/lightdm/root/ xwd -root > /var/www/html/extension/screen/screenshot.xwd
   convert /var/www/html/extension/screen/screenshot.xwd /var/www/html/extension/screen/screenshot_tmp.png
   cp -f /var/www/html/extension/screen/screenshot_tmp.png /var/www/html/extension/screen/screenshot.png
   sleep 3;
done
exit
