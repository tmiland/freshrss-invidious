# FreshRSS - invidious video extension

This FreshRSS extension allows you to directly watch Invidios videos from within subscribed channel feeds.

To use it, upload the ```xExtension-Invidios``` directory to the FreshRSS `./extensions` directory on your server and enable it on the extension panel in FreshRSS.

## Installation

The first step is to put the extension into your FreshRSS extension directory:
```
cd /var/www/FreshRSS/extensions/
wget https://github.com/tmiland/freshrss-invidious/archive/master.zip
unzip master.zip
mv freshrss-invidious-master/xExtension-Invidios .
rm -rf freshrss-invidious-master/
```

Then switch to your browser https://localhost/FreshRSS/p/i/?c=extension and activate it.

## About FreshRSS
[FreshRSS](https://freshrss.org/) is a great self-hosted RSS Reader written in PHP, which is can also be found here at [GitHub](https://github.com/FreshRSS/FreshRSS).

More extensions can be found at [FreshRSS/Extensions](https://github.com/FreshRSS/Extensions).

## Changelog

0.1:
* Forked and customized for Invidious from https://github.com/kevinpapst/freshrss-youtube
