#!/usr/bin/ruby

require 'bigcommerce'

Bigcommerce.configure do |config|
  config.auth = 'legacy'
  config.url = ENV['BC_API_ENDPOINT_LEGACY']
  config.username = ENV['BC_USERNAME']
  config.api_key = ENV['BC_API_KEY']
end


@order = Bigcommerce::Order.all[0]
@order.id = ARGV[0].to_i

# List order shipping address
@order_shipping_addresses = Bigcommerce::OrderShippingAddress.all(@order.id)
puts @order_shipping_addresses


puts "Done!"
