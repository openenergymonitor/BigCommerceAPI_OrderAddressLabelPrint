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

Tested with ruby 2.3.3 and glabels 3.4.1

## Setup credentials

Using envioment variables add the following to:

`$ nano ~/.profile`

```
export BC_API_ENDPOINT_LEGACY="https://store-xxxxxx.mybigcommerce.com/api/v2/"
export BC_USERNAME="xxxxx"
export BC_API_KEY="xxxxxxx"
```

Load the enviroment variables without having to logout/in:

`. ~/.profile`

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




