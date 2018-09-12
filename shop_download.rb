#!/usr/bin/ruby

require 'bigcommerce'
require 'csv'

Bigcommerce.configure do |config|
  config.auth = 'legacy'
  config.url = ENV['BC_API_ENDPOINT_LEGACY']
  config.username = ENV['BC_USERNAME']
  config.api_key = ENV['BC_API_KEY']
end


@printer="pi-Brother_QL-720NW"
@path=Dir.pwd

CSV.open("order_addresses.csv","wb") do |csv|

  @shop_orders = []
  @order = Bigcommerce::Order.all[0]

  if ARGV[0] #If two arguments printer and order ID
    @order.id = ARGV[0].to_i
    puts "Getting order #" + @order.id.to_s
    @shop_orders << Bigcommerce::Order.find(@order.id)
  else
    puts "Getting all orders with 'Awaiting Fulfillment' status..."
    @shop_orders =  Bigcommerce::Order.all(:status_id => '11')
  end

  #puts @shop_orders


  @shop_orders.each do |order|


    address = Bigcommerce::OrderShippingAddress.all(order[:id])[0]

    shipping_method = "1st"

    case address[:shipping_method]
      when /International Tracked/
      shipping_method = "TK"
      when /International Standard/
      shipping_method = "IS"
      when /Special/
      shipping_method = "SD"
      when /UPS/
      shipping_method = "UPS"
      when /International Shipping/
      shipping_method = "CO"
      when /1st Class/
      shipping_method = "1st"
      else
      shipping_method = "xx"
    end
    puts order[:id].to_s + " > " + address[:shipping_method] + " '" + shipping_method +"'"

    csv << [ address[:first_name],
             address[:last_name],
             address[:company],
             address[:street_1],
             address[:street_2],
             address[:city],
             address[:state],
             address[:zip],
             address[:country],
             address[:phone],
             order[:id],
             sprintf("%.2f", order[:subtotal_ex_tax].to_f),
             order[:currency_code],
             shipping_method
           ]
    end unless @shop_orders.nil?

#end CSV
end


puts "Generating labels"


# IO.popen('glabels-3-batch --input='+@path+'/order_addresses.csv '+@path+'/MergeLabels.glabels') { |io| while (line = io.gets) do puts line end }
# IO.popen('glabels-3-batch --input='+@path+'/order_addresses.csv '+@path+'/MergeLabels.glabels >/dev/null')

system('glabels-3-batch --input='+@path+'/order_addresses.csv '+@path+'/MergeLabels.glabels > /dev/null 2>&1')
if $? == 0
  puts "Success...label created"
else
  puts "Whoops...something went wrong :-("
end

puts "Sending to printer: " + @printer


# IO.popen('lpr -P '+@printer+' '+@path+'/output.pdf') { |io| while (line = io.gets) do puts line end }


puts "Success...let's go surfing!"
