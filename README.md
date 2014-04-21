PHP-PVR-Client
======================

PHP based application to allow client access to all of your favorite media providers!
Supports: Headphones, Couchpotato, and Sickbeard. (More coming soon)

##Features:<br>
 - Search and add music artists/albums to Headphones
 - Search and add TV Shows to Sickbeard
 - Search and add Movies to Couchpotato
 - Search for and send NZBs directly to sabnzbd
 - Password protected administration and logging
 - Displays headphones history, sickbeard's upcoming shows, and couchpotato's wanted list
 - Per show/artist per user subscription based notifications via twitter

===
## Installation:<br>
 Clone this repository to an php>=5.3 folder on a webserver and navigate to /manage/
 This will prompt you to create and verify a username and password (this can be changed later in the settings).
 Once there, you must configure the pvr apps you want to use. If no pvr apps are configured nothing will work.
 
 A last.fm apikey is currently required in order to provide extra artist and album info.
 You can obtain one by going to http://www.last.fm/api.

===
##Screenshots:<br>
**Settings**<br>
![Administrative settings page](https://rose-llc.com/dump/PHP-PVR-Client/html/settingsSS.jpg)<br>
**Sickbeard Search**<br>
![Sickbeard Search Results](https://rose-llc.com/dump/PHP-PVR-Client/html/tvsearchSS.jpg)<br>
**Headphones Search**<br>
![Headphones Search Results](https://rose-llc.com/dump/PHP-PVR-Client/html/musicsearchSS.jpg)<br>
**Couchpotato Search**<br>
![Couchpotato Search Results](https://rose-llc.com/dump/PHP-PVR-Client/html/moviesearchSS.jpg)<br>
===
## TODO:<br>
 - Create subscription based notification service
 - Create custom headphones repo to eliminate the need for a last.fm api key
 - Create responsive layout to facillitate mobile app.
