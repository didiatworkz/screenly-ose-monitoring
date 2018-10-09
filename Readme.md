# Screenly OSE Monitor

![Screenshot show](http://www.atworkz.de/_git/monitor/head.png)

- [Intro](#what-is-this)
- [Requirements](#requirements)
- [Install instructions](#installation)
- [Screenshots](#screenshots)

## What is this?
Screenly OSE Monitor is a web tool that allows you to manage multiple Screenly OSE players in one place.
In addition to the actual administration there is also an extension that allows you to display a "live" image of the player. So you can see at a glance which players show what and which are maybe even offline.
The site is also fully responsive, so that you can work with a smartphone.



## Requirements
+ RaspberryPi 2 or 3
+ PHP 7.0
+ SQLite 3.x

## Installation
Very simple installation:

1. connect to the player via SSH or terminal (ctrl+alt+F1)
2. copy this line and execute it
```bash
curl -sL http://screenly-monitor.atworkz.de | sudo bash
```
This may take a while - Don't be afraid
3. answer the questions
4. Done


Screenshots
---------------------------------------
![Screenshot Settings](http://www.atworkz.de/_git/monitor/layers.png)

![Screenshot show](http://www.atworkz.de/_git/monitor/sample1.jpg)
