# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.3] - 2021-12-09

### Added
- Database write permission check

### Changed
- Fix auto discovery auto name set #84
- Fix auto discovery modal close button #85
- Fix public link header title #82
- Fix wrong add-on version number shown in overview #83
- Fix wrong public link ip address #81

## [4.2] - 2021-12-08

### Added
- Manage player in groups #56

### Changed
- Change nginx to docker #72
- Changed installation process
- Fixed modal close function
- Fixed Device-Info output
- Fixed timezone settings
- Fixed upload error #64
- Fixed youtube upload #69
- Replace reboot icon
- Update packages

## [4.1] - 2021-01-28

### Changed
- Fixed upload error
- Fixed timezone settings
- Fixed Device-Info output
- Replace reboot icon
- Fix modal close function

## [4.0] - 2021-01-18

### Added
- Add-on deactivation #40
- Add-on overview
- Add-on remote installation
- Add-on version check
- Admin log system
- Avatar upload
- Background runner
- Dashboard
- Debug mode
- Group management
- Implement Device info in player details
- Light and Dark mode
- More Player information in settings
- Multi language support
- - Add en-US
- - Add de-DE
- New Design
- Rights management
- Setup Wizard
- Player service log
- Uninstall routine


### Changed
- Encrypt player password
- Extend Search
- Improve Auto discovery
- Improve https handling
- Improve login
- Improve security
- New Icons
- Public access
- Upload larger files #55
- Set asset setting on upload

### Removed
- Black Dashboard Design


## [3.4] - 2020-10-22

### Added
- Search function in the main view
- Possibility to change the monitoring name

### Changed
- Update installer script (Problem solved with WLAN IP)
- Fixed HTTPS connection problems with players
- Fixed problem with displaying the end date of assets set to Forever
- Fixed asset table order

## [3.3] - 2020-06-29

### Added
- Notification in Usermanagement Module

### Changed
- Multiuploader can't upload images
- Changes Notification System

## [3.2] - 2020-06-27

### Changed
- Adding assets with Player Authentication set, was not possible
- Changes in the settings were not saved
- Fixed wrong version number after update

## [3.1] - 2020-06-11

### Changed
- change addon url

## [3.0] - 2020-05-20

### Added
- Setup Wizard
- Image and Video upload
- Multiuploader for upload files to multiple player
- User management
- Auto discovery for add players
- Clean assets function

### Changed
- Sort assets
- Improved instalation speed
- Player rotation for public link
- php.ini parameter
- monitoring.conf template
- screenshot image refresh
- Fix name bug
- Group Assets
- Modal Bug
- File check in ose-monitoring
- update to python3
- update ansible


## [2.4] - 2019-10-31

### Changed
- Dynamic php-fpm package installation
- Error when no Player is set by using the token
- start/end date/time not saved when you edit a asset

## [2.2] - 2019-10-21
### Added
- Reboot function
- Standalone installation

### Changed
- Installer problems
- Linked php7.0 -> php (7.3)
- Package names
- Layout problems
- Black-Dashboard Design bugs
- Screenshot add-on


## [2.1] - 2019-05-17
### Added
- Managing Assets
- Add Assets
- Edit Assets
- New Design
- Monitoring Token
- Bash Tool
- Screenshots are stored in the RAM

### Changed
- Performance
- Control Assets
- Screenshot add-on
- Ansible installer

## [1.2] - 2019-03-13

### Changed
- Script installer
- Improve Extension
