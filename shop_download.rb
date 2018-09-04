require 'bigcommerce'
require 'csv'

api = Bigcommerce::Api.new({
                             :store_url => "xxxxxxxxxxxxxxxx",
                             :username  => "xxxxxxxxxxxxxxxx",
                             :api_key   => "xxxxxxxxxxxxxxxxxxxxxxxxxx"
                           })

CSV.open("order_addresses.csv","wb") do |csv|

  @shop_orders = []

  if ARGV[1] #If two arguments printer and order ID
    @shop_orders <<  api.get_order(ARGV[1].to_i) # second argument is order ID
  else
    @shop_orders =  api.get_orders(:status_id => '11')
  end


  puts "Downloading..."

  @shop_orders.each do |order|

    puts order["id"]

    address = api.get_orders_shippingaddresses(order["id"])[0]


    puts address["shipping_method"]
    shipping_method = "1st"

   case address["shipping_method"]
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

    csv << [ address["first_name"],
             address["last_name"],
             address["company"],
             address["street_1"],
             address["street_2"],
             address["city"],
             address["state"],
             address["zip"],
             address["country"],
             address["phone"],
             order["id"],
             sprintf("%.2f", order["subtotal_ex_tax"].to_f),
             order["currency_code"],
             shipping_method
           ]
  end unless @shop_orders.nil?
end

puts
puts "Generating labels"

IO.popen('glabels-3-batch --input=order_addresses.csv ~/OrderDownload/DK-2223-85.glabels') { |io| while (line = io.gets) do puts line end }

puts
puts "Sending to printer"


  if ARGV[1] or  ARGV[0]  #If printer and order ID argument
    IO.popen('lpr -P' + ARGV[0] + ' output.pdf') { |io| while (line = io.gets) do puts line end }
    puts "Enter Brother_QL-500_server or QL-720NW (QL-720NW default)"
  else
    IO.popen('lpr -P QL-720NW output.pdf') { |io| while (line = io.gets) do puts line end }
    puts "Enter Brother_QL-500_server or QL-720NW (QL-720NW default)"
  end

puts "Done!"
