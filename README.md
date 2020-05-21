

# Screenly OSE Monitoring
<p align="center">
<img title="Manage Monitoring" alt="Manage Monitoring" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v3.0/.github/IMAGES/manage5.png" style="width:800px" />
</p>

![GitHub release (latest by date)](https://img.shields.io/github/v/release/didiatworkz/screenly-ose-monitor) ![GitHub Release Date](https://img.shields.io/github/release-date/didiatworkz/screenly-ose-monitor?color=green) ![GitHub last commit (branch)](https://img.shields.io/github/last-commit/didiatworkz/screenly-ose-monitor/v3.0) ![GitHub commit activity](https://img.shields.io/github/commit-activity/y/didiatworkz/screenly-ose-monitor)  ![GitHub issues](https://img.shields.io/github/issues-raw/didiatworkz/screenly-ose-monitor)  ![GitHub stars](https://img.shields.io/github/stars/didiatworkz/screenly-ose-monitor?style=social)

- [Intro](#what-is-this)
- [Features](#features)
- [Bugs](#Bugs)
- [Requirements](#requirements)
- [Installation](#installation)
- [Login](#login)
- [Update](#update)
- [Changelog](#changelog)
- [Bash Controll](#bash-controll)
- [More Questions?](#any-more-questions)


## What is this?
<img align="left" title="Monitoring Overview" alt="Monitoring Overview" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v3.0/.github/IMAGES/manage4.png" style="width: 400px; padding-right: 12px; padding-bottom: 12px" />

Screenly OSE Monitoring is a web-based application that is simultaneously installed on a Screenly OSE Player or as standalone solution. With this tool, you can manage multiple OSE players in one interface.

In addition to its current display status, you can also manage the assets of a player. It is possible to activate or deactivate assets, add new ones, edit or simply delete them all from the player.

By installing an add-on on each player, it is also possible to display a "live feed" of the player's HDMI output. This will then be displayed in the overview, as well.




## Features

<img align="right" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v3.0/.github/IMAGES/monitoring2.png" width="300px">

+ Easy administration
+ User management
+ Simple overview of all players with status
+ Add / Edit / Remove / Order / Control Assets
+ Auto discovery of players
+ Responsive Design
+ Public access to integrate in Monitoring tools
+ Add-on for displaying the player’s output remotely



## Bugs
No errors were found in the tests. If you find a problem or bug, please report it:<br />
[Open Issue List](https://github.com/didiatworkz/screenly-ose-monitor/issues?q=is:issue%20label:bug)

---

## Requirements
+ RaspberryPi 3B+
+ Raspbian Lite
+ PHP 7.x (will be installed)
+ SQLite 3.x (will be installed)
+ Ansible (will be installed)

## Installation
Very simple installation:

1. Connect to the player via SSH or terminal ([CTRL]+[ALT]+[F1])
2. Copy this line and execute it
```bash
bash <(curl -sL https://git.io/fjg7h)
```
3. Answer the questions and installation will be start (This may take a while - Don't worry)
4. Open your Browser to the IP address of the Raspberry Pi like: http://[screenly-ip-address]:9000

## Login
After the installation is the default login:

http://[screenly-ip-address]:9000

Username: demo<br />
Password: demo

<p align="center">
<img title="Manage Monitoring" alt="Manage Monitoring" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v3.0/.github/IMAGES/livecase.png" />
</p>

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


<p align="center">
<img title="Monitoring Overview" alt="Monitoring Overview" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v3.0/.github/IMAGES/screens2.png" />
</p>

## Bash Controll
Since version 2.0 there is a small possibility to update or check ose-monitoring via bash.
For more info check:
```bash
ose-monitoring --help
```

## Any more questions?
There's something else that wasn't answered here?
Then just have a look at the [wiki]([https://github.com/didiatworkz/screenly-ose-monitor/wiki](https://github.com/didiatworkz/screenly-ose-monitor/wiki)). Maybe you will find an answer there.

Thanks for using this project! <br />
-- didiatworkz
