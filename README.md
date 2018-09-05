BigCommerceAPI_OrderAddressLabelPrint
=====================================
Used BigCommerce Ruby API: https://github.com/bigcommerce/bigcommerce-api-ruby

Installation
============

`$ sudo apt-get update`
`$ sudo apt-get install glabels`
`$ gem install bundler`
`$ bundle update && bundle install`

Usage
=====

show_order.rb - Ruby script using BigCommerce API, when passed an order ID number all order info is displayed in json


shop_download.rb - Ruby script using BigCommerce API and glables to generate a printable pdf labels with shipping address, order ID, order sub-total and shipping type for all orders which are Awaiting Fulfilment. Pdf label is then sent to Brother QL-500 label printer.


By Aled Ynyr Edwards 
https://github.com/ynyreds

Re-packaged using Ruby Gems by Frank Oxener 
https://github.com/dovadi

Used internally for running OpenEnergyMonitor shop
http://shop.openenergymonitor.com




