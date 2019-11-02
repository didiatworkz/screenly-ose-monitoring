
# Screenly OSE Monitoring

<img title="Manage Monitoring" alt="Manage Monitoring" src="https://github.com/didiatworkz/screenly-ose-monitor/raw/master/.github/IMAGES/manage2.png" />

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
Screenly OSE Monitoring is a web-based application that is simultaneously installed on a Screenly OSE Player. With this tool, you can manage multiple OSE players via one interface.

In addition to its current display status, you can also manage the assets of a player. It is possible to activate or deactivate assets, add new ones, edit or simply delete them.

By installing an add-on on each player, it is also possible to display a "live feed" of the player's output. This will then be displayed in the overview, as well.




## Features

<img align="right" src="http://www.atworkz.de/_git/monitor/monitoring.png">

+ Easy administration
+ Simple overview of all players
+ Quick overview if the player is online or not
+ Add-on for displaying the player’s output remotely
+ Managing Assets
+ Add Assets
+ Edit Assets
+ New Design
+ Public Link Token
+ Bash Tool
+ Screenshots are stored in the RAM
+ Control Assets
+ screenshot extension

## Bugs
[Issue List](https://github.com/didiatworkz/screenly-ose-monitor/issues?q=is:open%20is:issue%20label:bug)

---

## Requirements
+ RaspberryPi 3B+
+ Raspbian Lite
+ PHP 7.x
+ SQLite 3.x
+ Ansible

## Installation
Very simple installation:

1. Connect to the player via SSH or terminal (ctrl+alt+F1)
2. Copy this line and execute it
```bash
bash <(curl -sL https://git.io/fjg7h)
```
3. Answer the questions and installation will be start (This may take a while - Don't worry)
4. Done

---

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

## Login
After the installation is the default login:

http://[screenly-ip-address]:9000

Username: demo

Password: demo

## Bash Controll
Since version 2.0 there is a small possibility to update or check ose-monitoring via bash.
For more info check:
```bash
ose-monitoring --help
```


<img title="Monitoring Overview" alt="Monitoring Overview" src="https://github.com/didiatworkz/screenly-ose-monitor/raw/master/.github/IMAGES/screens.png" />
