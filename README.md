BigCommerceAPI_OrderAddressLabelPrint
=====================================
Used BigCommerce Ruby API: https://github.com/bigcommerce/bigcommerce-api-ruby

# Installation
============

```
sudo apt-get update
sudo apt-get install glables
gem install bundler
bundle install
```

## Setup credentials

"Save As&hellip;" `.env.example` as `.env` and alter the quoted values. Here's the examples:

```
BC_API_ENDPOINT_LEGACY="https://store-xxxxxx.mybigcommerce.com/api/v2/"
BC_USERNAME="xxxxx"
BC_API_KEY="xxxxxxx"
```

Add the following lines to the end of your `.bashrc` file to make the above variables available to the scripts

```
if [ -f [path to repo]/.env ] ; then
    . [path to repo]/.env
fi
```

Load the environment variables without having to logout/in:

```
. ~/.bashrc
```

# Usage
=====

shop_download.rb - Ruby script using BigCommerce API and glables to generate a printable pdf labels with shipping address, order ID, order sub-total and shipping type for all orders which are Awaiting Fulfilment. Pdf label is then sent to Brother QL-500 label printer.

To easily run using the '$ label' command create synlink 

`$ sudo ln -s /home/pi/BigCommerceAPI_OrderAddressLabelPrint/label /usr/bin/`


By Aled Ynyr Edwards
https://github.com/ynyreds

Re-packaged using Ruby Gems by Frank Oxener
https://github.com/dovadi

Used internally for running OpenEnergyMonitor shop
http://shop.openenergymonitor.com

Altered by Emrys Roberts 2018-09-13
https://github.com/emrysr

# PHP GUI frontend
This assumes that Apache and PHP are installed. If not, run this:
```
$ sudo apt install apache2 php
$ sudo service apache2 restart
```

Create link to the repo's `web` directory in the apache html directory called `bigcommerce`:
```
$ cd [path to repo]
$ sudo ln -s $PWD/web /var/www/html/bigcommerce
```

Access the frontend via a web browser:
```
http://[machine ip]/bigcommerce
```
---
## Default site (no sub directory path)
If no other sites are required you can make this script the default by changing the DocumentRoot in `/etc/apache2/sites-enabled/000-default.conf`:

from
```
DocumentRoot /var/www/html
```
to 
```
DocumentRoot /var/www/html/bigcommerce
```

Restart apache 
```
$ sudo service apache2 restart 
```

This script will then be available at :-
```
http://[machine ip]/
```

=====