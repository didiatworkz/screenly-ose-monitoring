

# Screenly OSE Monitoring (SOMO)
<p align="center">
<img width="800px" title="Manage Monitoring" alt="Manage Monitoring" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v4.0/.github/IMAGES/manage5.png" />
</p>

![GitHub release (latest by date)](https://img.shields.io/github/v/release/didiatworkz/screenly-ose-monitor) ![GitHub Release Date](https://img.shields.io/github/release-date/didiatworkz/screenly-ose-monitor?color=green) ![GitHub last commit (branch)](https://img.shields.io/github/last-commit/didiatworkz/screenly-ose-monitor/v4.0) ![GitHub commit activity](https://img.shields.io/github/commit-activity/y/didiatworkz/screenly-ose-monitor)  ![GitHub issues](https://img.shields.io/github/issues-raw/didiatworkz/screenly-ose-monitor)  ![GitHub stars](https://img.shields.io/github/stars/didiatworkz/screenly-ose-monitor?style=social)

- [Intro](#what-is-this)
- [Features](#features)
- [Bugs](#Bugs)
- [Requirements](#requirements)
- [Installation](#installation)
- [Login](#login)
- [Update](#update)
- [Changelog](#changelog)
- [More Questions?](#any-more-questions)


## What is this?
<img align="left" width="300px" title="Monitoring Overview" alt="Monitoring Overview" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v4.0/.github/IMAGES/manage4.png" style="padding-right: 12px; padding-bottom:12px" />

Screenly OSE Monitoring is a web-based application that is simultaneously installed on a Screenly OSE Player or as standalone server solution.

With this tool, you can manage multiple Screenly OSE players in one web-interface.

In addition to its current display status, you can also manage the assets of a player. It is possible to activate or deactivate assets, add new ones, edit or simply delete them all from the player.
Also it's possible to reboot the player directly or upload new assets to multiple players parallel.
<br />
<br />
By installing an add-on on each player, it is also possible to display a "live feed" of the player's HDMI output and live-data from the system. This will then be displayed in the overview, as well.




## Features

<img width="300px" align="right" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v4.0/.github/IMAGES/image1.png">

+ Easy administration
+ Dashboard
+ User management
+ Group Management incl. access restrictions
+ Simple overview of all players with status
+ Add / Edit / Remove / Order / Switch Assets
+ Auto discovery of players in a subnet
+ Upload assets simultaneously to multiple playes
+ Responsive Design
+ Dark and Light Mode
+ Public access to integrate in other monitoring tools
+ Add-on overview of all players
+ Add-on for displaying the player’s output remotely
+ Add-on for displaying the player’s system information

## Bugs
No errors were found in the tests. If you find a problem or bug, please report it:<br />
[Open Issue List](https://github.com/didiatworkz/screenly-ose-monitoring/issues?q=is:issue%20label:bug)

---

## Requirements
+ RaspberryPi 3B+
+ Raspbian Lite
+ PHP 7.x (will be installed)
+ SQLite 3.x (will be installed)
+ Ansible (will be installed)

## Installation
__IMPORTANT: The monitoring was designed to run on the local network with the Screenly OSE Player. Problems or restrictions may occur if the server is hosted externally or accessed from an external network!__

Very simple installation:

1. Connect to the player via SSH or terminal ([CTRL]+[ALT]+[F1])
2. Copy this line and execute it
```sh
bash <(curl -sL https://git.io/JtTFf)
```
3. Answer the questions and installation will be start (This may take a while - Don't worry)
4. Open your Browser to the IP address of the Raspberry Pi like: http://[screenly-ip-address]:9000

## Login
After the installation is the default login:

http://[screenly-ip-address]:9000 (when Screenly OSE is installed)<br />
http://[screenly-ip-address] (Server installation)

Username: demo<br />
Password: demo

<p align="center">
<img width="600px" title="Manage Monitoring" alt="Manage Monitoring" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v4.0/.github/IMAGES/livecase.png" />
</p>

---

## Update
For the update you only have to run the installer again.
The installer checks if there is an old installation and saves it if necessary.
From version 2.0 there are changes to the database structure which is prepared for future updates and no longer needs to be saved.

But please note that the Add-on may have to be reinstalled on the players. (Current for all older versions before version 2.0)

__IMPORTANT: If monitoring is installed on a Raspberry Pi that does not have a Screenly OSE Player (standalone installation) the port changes from 9000 to 80.
So the monitoring is accessible via the normal IP address!__

### Update SOMO
```sh
bash <(curl -sL https://git.io/JtTFf)
```

### Update Extension
Link via Monitoring or here:
```sh
bash <(curl -sL https://git.io/Jf900)
```

### Changelog
[Open Changelog](https://github.com/didiatworkz/screenly-ose-monitoring/blob/master/CHANGELOG.md)


<p align="center">
<img title="Monitoring Overview" alt="Monitoring Overview" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v4.0/.github/IMAGES/screens3.png" />
<img title="Monitoring Overview" alt="Light and Dark Mode" src="https://raw.githubusercontent.com/didiatworkz/screenly-ose-monitor/v4.0/.github/IMAGES/light_dark.png" />
</p>

## Any more questions?
There's something else that wasn't answered here?
Then just have a look at the [wiki-page]([https://github.com/didiatworkz/screenly-ose-monitoring/wiki](https://github.com/didiatworkz/screenly-ose-monitoring/wiki)). Maybe you will find an answer there.

Thanks for using this project! <br />
\- didiatworkz

<p align="center">
<img width="128px" title="atworkz logo" alt="atworkz logo" src="https://assets.atworkz.de/img/atworkz_logo_512.png" />
</p>
