require 'bigcommerce'
require 'csv'


api = Bigcommerce::Api.new({
                             :store_url => "https://store-98a75.mybigcommerce.com/api/v2/",
                             :username  => "xxxxx",
                             :api_key   => "xxxx"
                           })


@shop_orders = []

@shop_orders <<  api.get_order(ARGV[0].to_i)

puts "Downloading..."

puts @shop_orders

@shop_orders.each do |order| 
  puts "total_ex_tax:"
  puts order["total_ex_tax"]
end


puts "Done!"
