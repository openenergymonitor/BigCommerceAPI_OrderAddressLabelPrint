require 'bigcommerce'
require 'csv'

api = Bigcommerce::Api.new({
                             :store_url => "https://store-xxxxx.mybigcommerce.com/api/v2/",
                             :username  => "xxxxxxxxxxxxx",
                             :api_key   => "xxxxxxxxxxxxxxxxx"
                           })

CSV.open("order_addresses.csv","wb") do |csv|

  @shop_orders = []

  if ARGV[0]
    @shop_orders <<  api.get_order(ARGV[0].to_i)
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

IO.popen('glabels-3-batch --input=order_addresses.csv ~/OrderDownload/MergeLabels.glabels') { |io| while (line = io.gets) do puts line end }

puts
puts "Sending to printer"

IO.popen('lpr -P Brother_QL-500_server output.pdf') { |io| while (line = io.gets) do puts line end }

puts "Done!"
