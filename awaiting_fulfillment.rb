#!/usr/bin/ruby

require 'bigcommerce'
require 'json'

# set up the Bigcommerce api settings
Bigcommerce.configure do |config|
    config.auth = 'legacy'
    config.url = ENV['BC_API_ENDPOINT_LEGACY']
    config.username = ENV['BC_USERNAME']
    config.api_key = ENV['BC_API_KEY']
end

# extend the Bigcommerce::OrderStatus class to allow for additional properties
class StatusOrders < Bigcommerce::OrderStatus
    property :orders
end
# use the Bigcommerce API to get all the orders with a specific status
def getStatusOrders(status_id)
    return Bigcommerce::Order.all(:status_id => status_id)
end

# status id 11 == Awaiting Fulfillment (see full list below)
@status_id = 11
# get all the status' properties
@status = StatusOrders.find(@status_id)
# add to the additional total_orders property
@status.orders = getStatusOrders(@status.id)
# return object as json formatted string
puts JSON[@status]

exit






# 2018-09-13
# puts Bigcommerce::OrderStatus.all
# 
## OUTPUTS:
## "#<Bigcommerce::OrderStatus id=0 name="Incomplete" order=0>"
## "#<Bigcommerce::OrderStatus id=1 name="Pending" order=1>"
## "#<Bigcommerce::OrderStatus id=2 name="Shipped" order=8>"
## "#<Bigcommerce::OrderStatus id=3 name="Partially Shipped" order=6>"
## "#<Bigcommerce::OrderStatus id=4 name="Refunded" order=11>"
## "#<Bigcommerce::OrderStatus id=5 name="Cancelled" order=9>"
## "#<Bigcommerce::OrderStatus id=6 name="Declined" order=10>"
## "#<Bigcommerce::OrderStatus id=7 name="Awaiting Payment" order=2>"
## "#<Bigcommerce::OrderStatus id=8 name="Awaiting Pickup" order=5>"
## "#<Bigcommerce::OrderStatus id=9 name="Awaiting Shipment" order=4>"
## "#<Bigcommerce::OrderStatus id=10 name="Completed" order=7>"
## "#<Bigcommerce::OrderStatus id=11 name="Awaiting Fulfillment" order=3>"
## "#<Bigcommerce::OrderStatus id=12 name="Manual Verification Required" order=13>"
## "#<Bigcommerce::OrderStatus id=13 name="Disputed" order=12>"
## "#<Bigcommerce::OrderStatus id=14 name="Partially Refunded" order=14>"
## "#<Bigcommerce::OrderStatus id=11 name="Awaiting Fulfillment" order=3>"