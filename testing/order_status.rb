#!/usr/bin/ruby

require 'bigcommerce'

Bigcommerce.configure do |config|
  config.auth = 'legacy'
  config.url = ENV['BC_API_ENDPOINT_LEGACY']
  config.username = ENV['BC_USERNAME']
  config.api_key = ENV['BC_API_KEY']
end

# List order statuses
@order_statuses = Bigcommerce::OrderStatus.all
puts @order_statuses

# # Get an order status
@order_status = @order_statuses[0]
puts Bigcommerce::OrderStatus.find(@order_status.id)
