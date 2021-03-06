# FreshRSS - Invidious video extension

This FreshRSS extension allows you to directly watch Invidious videos from within subscribed channel feeds.



To use it, upload the ```xExtension-Invidious``` directory to the FreshRSS `./extensions` directory on your server and enable it on the extension panel in FreshRSS.

## Installation

The first step is to put the extension into your FreshRSS extension directory:
```
cd /var/www/FreshRSS/extensions/
wget https://github.com/tmiland/freshrss-invidious/archive/master.zip
unzip master.zip
mv freshrss-invidious-master/xExtension-Invidious .
rm -rf freshrss-invidious-master/
```

Then switch to your browser https://localhost/FreshRSS/p/i/?c=extension and activate it.

Set "Player domain" in settings.

# Screenshots

Settings in FreshRSS:

![screenshot settings](https://raw.githubusercontent.com/tmiland/freshrss-invidious/master/settings.png?raw=true "Custom option to set the domain of your own instance, player height/width and option to redirect YouTube videos to Invidious")

With FreshRSS and an original Invidious Channel feed:

![screenshot before](https://raw.githubusercontent.com/tmiland/freshrss-invidious/master/example2.png?raw=true "Without this extension the video is not shown")

With activated Invidious extension:

![screenshot after](https://raw.githubusercontent.com/tmiland/freshrss-invidious/master/example.png?raw=true "After activationg the extension you can enjoy your video directly in the FreshRSS stream")

## About Invidious
Invidious is an alternative front-end to YouTube
https://invidio.us
https://github.com/omarroth/invidious

## About FreshRSS
[FreshRSS](https://freshrss.org/) is a great self-hosted RSS Reader written in PHP, which is can also be found here at [GitHub](https://github.com/FreshRSS/FreshRSS).

More extensions can be found at [FreshRSS/Extensions](https://github.com/FreshRSS/Extensions).

## Changelog

0.2:
* Added option to redirect YouTube videos to Invidious
* Moved note to language file

0.1:
* Forked and customized for Invidious from https://github.com/kevinpapst/freshrss-youtube
