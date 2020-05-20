

# Screenly OSE Monitoring

<img title="Manage Monitoring" alt="Manage Monitoring" src="https://github.com/didiatworkz/screenly-ose-monitor/raw/master/.github/IMAGES/manage2.png" />

![GitHub release (latest by date)](https://img.shields.io/github/v/release/didiatworkz/screenly-ose-monitor) ![GitHub Release Date](https://img.shields.io/github/release-date/didiatworkz/screenly-ose-monitor?color=green) ![GitHub last commit (branch)](https://img.shields.io/github/last-commit/didiatworkz/screenly-ose-monitor/v3.0) ![GitHub commit activity](https://img.shields.io/github/commit-activity/y/didiatworkz/screenly-ose-monitor)  ![GitHub issues](https://img.shields.io/github/issues-raw/didiatworkz/screenly-ose-monitor)  ![GitHub stars](https://img.shields.io/github/stars/didiatworkz/screenly-ose-monitor?style=social)
- [Intro](#what-is-this)
- [Features](#Features)
- [Bugs](#Bugs)
- [Requirements](#requirements)
- [Installation](#installation)
- [Update](#update)
- [Login](#login)
- [Bash Controll](#bash-controll)


## What is this?
<img align="right" width="400px" title="Monitoring Overview" alt="Monitoring Overview" src="https://github.com/didiatworkz/screenly-ose-monitor/raw/master/.github/IMAGES/manage.png" />
Screenly OSE Monitoring is a web-based application that is simultaneously installed on a Screenly OSE Player or as standalone solution. With this tool, you can manage multiple OSE players in one interface.

In addition to its current display status, you can also manage the assets of a player. It is possible to activate or deactivate assets, add new ones, edit or simply delete them all from the player.

By installing an add-on on each player, it is also possible to display a "live feed" of the player's HDMI output. This will then be displayed in the overview, as well.




## Features

<img align="right" src="http://www.atworkz.de/_git/monitor/monitoring.png">

+ Easy administration
+ User management
+ Simple overview of all players with status
+ Add / Edit / Remove / Order / Control Assets
+ Auto discovery of players
+ Responsive Design
+ Public access to integrate in Monitoring tools
+ Add-on for displaying the player’s output remotely

## Bugs
No errors were found in the tests. If you find a problem or bug, please report it:
[Issue List](https://github.com/didiatworkz/screenly-ose-monitor/issues?q=is:issue%20label:bug)

---

## Requirements
+ RaspberryPi 3B+
+ Raspbian Lite
+ PHP 7.x (will be installed)
+ SQLite 3.x (will be installed)
+ Ansible (will be installed)

## Installation
Very simple installation:

1. Connect to the player via SSH or terminal ([CTRL]+[ALT]+F1)
2. Copy this line and execute it
```bash
bash <(curl -sL https://git.io/fjg7h)
```
3. Answer the questions and installation will be start (This may take a while - Don't worry)
4. Open your Browser to the IP address of the Raspberry Pi like: 192.168.178.2:9000

## Login
After the installation is the default login:

http://[screenly-ip-address]:9000

Username: demo

Password: demo

## Update
For the update you only have to run the installer again.
The installer checks if there is an old installation and saves it if necessary.
From version 2.0 there are changes to the database structure which is prepared for future updates and no longer needs to be saved.

But please note that the Add-on may have to be reinstalled on the players. (Current for all older versions before version 2.0)

### Monitoring
```bash
bash <(curl -sL https://git.io/fjg7h)
```

### Extensions
Link over Webfrontend or this:
```bash
bash <(curl -sL https://git.io/fjg5e)
```

### Changelog
[Open Changelog](https://github.com/didiatworkz/screenly-ose-monitor/blob/master/CHANGELOG.md)



<img title="Monitoring Overview" alt="Monitoring Overview" src="https://github.com/didiatworkz/screenly-ose-monitor/raw/master/.github/IMAGES/screens.png" />

## Bash Controll
Since version 2.0 there is a small possibility to update or check ose-monitoring via bash.
For more info check:
```bash
ose-monitoring --help
```

## Any more questions?
There's something else that wasn't answered here?
Then just have a look at the [wiki]([https://github.com/didiatworkz/screenly-ose-monitor/wiki](https://github.com/didiatworkz/screenly-ose-monitor/wiki)). Maybe you will find an answer there.

Thanks for using this project!
-- didiatworkz
