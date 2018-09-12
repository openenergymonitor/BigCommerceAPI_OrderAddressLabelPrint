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

"Save As&hellip;" `.env.example` as `.env` and alter the quotedvalues. Here's the examples:

```
BC_API_ENDPOINT_LEGACY="https://store-xxxxxx.mybigcommerce.com/api/v2/"
BC_USERNAME="xxxxx"
BC_API_KEY="xxxxxxx"
```

Include the `.env` file at the bottom of your `.profile` file:

```
$ echo "[path to git repo]/.env" >> ~/.profile
```


Load the environment variables without having to logout/in:

```
. ~/.profile
```

# Usage
=====

show_order.rb - Ruby script using BigCommerce API, when passed an order ID number all order info is displayed in json


shop_download.rb - Ruby script using BigCommerce API and glables to generate a printable pdf labels with shipping address, order ID, order sub-total and shipping type for all orders which are Awaiting Fulfilment. Pdf label is then sent to Brother QL-500 label printer.


By Aled Ynyr Edwards
https://github.com/ynyreds

Re-packaged using Ruby Gems by Frank Oxener
https://github.com/dovadi

Used internally for running OpenEnergyMonitor shop
http://shop.openenergymonitor.com


# PHP GUI frontend
This assumes that Apache and PHP are installed. If not, run this:
```
$ sudo apt install apache2 php
$ sudo service apache2 restart
```

Create link to web directory in apache directory:
```
$ sudo ln -s [path to git repo]/web /var/www/html/bigcommerce
```

Access the frontend via a web browser:
```
http://[machine ip]/bigcommerce
```
---
## Default site
If no other sites are required you can make this script the default by changing the DocumentRoot in `/etc/apache2/sites-enabled/000-default.conf`:

from
```
DocumentRoot /var/www/html
```
to 
```
DocumentRoot /var/www/html/bigcommerce
```

This script will then be available at :-
```
http://[machine ip]/
```

=====