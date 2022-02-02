#!/usr/bin/ruby

require 'bigcommerce'
require 'csv'
require 'json'

Bigcommerce.configure do |config|
  config.auth = 'legacy'
  config.url = ENV['BC_API_ENDPOINT_LEGACY']
  config.username = ENV['BC_USERNAME']
  config.api_key = ENV['BC_API_KEY']
end

@printer="pi-Brother_QL-720NW"
# path is passed to script if ran from php.

# if 1 param passed and is not integer - it is the path
# if 1 param passed and is an integer - it is the order id
# if 2 param passed the first is the path, second is the id
# if 0 param passed the path is the current directory

if ARGV[0].to_i.to_s == ARGV[0].to_s
  @order_id = ARGV[0].to_i
end

CSV.open("/tmp/order_addresses.csv","wb") do |csv|

  @shop_orders = []
  @order = Bigcommerce::Order.all[0]

  if @order_id && @order_id > 0
    @order.id = @order_id
    puts "Getting order #" + @order.id.to_s
    begin
      @shop_orders << Bigcommerce::Order.find(@order.id)
    rescue StandardError => e
      # unexpected returned message from api
      JSON.parse(e.message).each do |message|
        puts message["message"]
      end
      exit
    end
  else
    puts "Getting all orders with 'Awaiting Fulfillment' status..."
    @shop_orders =  Bigcommerce::Order.all(:status_id => '11')
  end

  # puts @shop_orders
  # puts @order_id

  if (@shop_orders.length<1)
    puts "No orders Awaiting Fulfillment :-)"
    exit
  end


  @shop_orders.each do |order|


    address = Bigcommerce::OrderShippingAddress.all(order[:id])[0]

    shipping_method = "1st"

    case address[:shipping_method]
      when /International Tracked/
      shipping_method = "TK"
      when /International Signed/
      shipping_method = "SG"
      when /International Standard/
      shipping_method = "IS"
      when /Royal Mail Tracked/
      shipping_method = "SD"
      when /UPS/
      shipping_method = "UPS"
      when /International Shipping/
      shipping_method = "CO"
      when /Royal Mail Standard/
      shipping_method = "RM"
      else
      shipping_method = "xx"
    end
    puts order[:id].to_s + " > " + address[:shipping_method] + " '" + shipping_method +"'"

    phone_number  = address[:phone]    
    order_amount  = sprintf("%.2f", order[:subtotal_ex_tax].to_f)
    zip_code = address[:zip]
    country = address[:country]
    
    if shipping_method == "RM" || shipping_method == "SD"
      phone_number = ""
      if zip_code[0...2].upcase.include? "BT"
        country = "Northern Ireland"
      else
        order_amount = ""
      end
    end

    # check if address is in Northern Ireland (needs customs form post Brexit)
    # remove phone number and order amount if not needed (for UK orders)

    csv << [ address[:first_name],
             address[:last_name],
             address[:company],
             address[:street_1],
             address[:street_2],
             address[:city],
             address[:state],
             zip_code,
             country,
             #address[:phone],
             phone_number,
             order[:id],
             order_amount,
             #sprintf("%.2f", order[:subtotal_ex_tax].to_f),
             order[:currency_code],
             shipping_method
           ]
    end unless @shop_orders.nil?

#end CSV
end


puts "Generating labels"


# IO.popen('glabels-3-batch --input='+@path+'/order_addresses.csv '+@path+'/MergeLabels.glabels') { |io| while (line = io.gets) do puts line end }
# IO.popen('glabels-3-batch --input='+@path+'/order_addresses.csv '+@path+'/MergeLabels.glabels >/dev/null')

system('glabels-3-batch --input=/tmp/order_addresses.csv '+__dir__+'/MergeLabels.glabels --output=/tmp/output.pdf > /dev/null 2>&1')

if $? == 0
  puts "Success...label created"
else
  puts "Whoops...something went wrong :-("
end

puts "Sending to printer: " + @printer


IO.popen('lpr -P '+@printer+' /tmp/output.pdf') { |io| while (line = io.gets) do puts line end }


puts "Done"
