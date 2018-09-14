BigCommerceAPI_OrderAddressLabelPrint
=====================================
Used BigCommerce Ruby API: https://github.com/bigcommerce/bigcommerce-api-ruby

# Installation
============

### Requirements: 

- Ruby (and bundler) - to run the BigCommerce API calls and to install the required libraries.
- lpr - to print the files. Usually installed with CUPS.
- PHP - to run the web frontend
- Apache - to host the web frontend
- gLabels - to produce the PDF for printing

### Install commands:

```
sudo apt-get update
sudo apt-get install glables
gem install bundler
bundle install
```

## Setup credentials

> **`[path to repo]`** is to be replaced here with wherever you've cloned the repo.
>
> eg: */home/pi/BigCommerceAPI_OrderAddressLabelPrint*

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

To easily run using the '$ label' command create symlink:

```
$ sudo ln -s /home/pi/BigCommerceAPI_OrderAddressLabelPrint/label /usr/bin/
```


By Aled Ynyr Edwards
https://github.com/ynyreds

Re-packaged using Ruby Gems by Frank Oxener
https://github.com/dovadi

Used internally for running OpenEnergyMonitor shop
http://shop.openenergymonitor.com

Altered by Emrys Roberts 2018-09-13
https://github.com/emrysr

=====


# PHP GUI frontend
=====

This assumes that Apache and PHP are installed. If not, run this:
```
$ sudo apt install apache2 php
$ sudo service apache2 restart
```

Create link to the repo's `web` directory in the apache html directory called `bigcommerce`:
```
$ sudo ln -s [path to repo]/web /var/www/html/bigcommerce
```

Access the frontend via a web browser:
```
http://[machine ip]/bigcommerce
```
---
## Default site (no sub directory path)
If no other sites are required you can make this script the default by changing the `DocumentRoot` variable in `/etc/apache2/sites-enabled/000-default.conf`:

Change from `/var/www/html` to `/var/www/html/bigcommerce` 

### Restart apache 
```
$ sudo service apache2 restart 
```

### Web browser
This script will then be available at :-
```
http://[machine ip]/
```


## Debug Errors

Debug settings can help if installation issues occur. They can be seen using the menu at the header.

Appending the following URL parameters will show you the information:
- ?action=debug
- ?action=phpinfo

=====