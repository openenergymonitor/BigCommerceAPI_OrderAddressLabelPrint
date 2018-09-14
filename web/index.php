<?php
    // load the settings from the .env file in the repo (ignored)
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = new Dotenv\Dotenv(dirname(__DIR__));
    $dotenv->load();

    /***
     * runs a ruby script with the big commerce api calls. settings stored in web/settings.php
     * 
     * successful messages returned in $output
     * errors returned in $errors
     * 
     * routing like behaviour done using ?action='name' URL parameter
     * 
     */
    
    $errors = array();
    $output = array();
    
    if($action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING)){
        
        // Call Ruby Script
        // runs the same script with or without the additional order_number param.
        if($action=='print_all' || $action=='print_single') {
            // escape any user input to avoid system commands being ran incorrectly
            // @note: currently order_number can only be an integer
            $order_number = "";
            if(!empty($_GET['order_number'])) {
                $order_number = filter_input(INPUT_GET, "order_number", FILTER_SANITIZE_NUMBER_INT);
                if(!$order_number){
                    $errors[] = "Order number is not valid";
                }
            }
            // add order_number as script input (if available)
            $command = sprintf('ruby %s/shop_download.rb %s %s 2>&1', dirname(__DIR__), $order_number, dirname($_SERVER['SCRIPT_FILENAME']));
            $last_line = exec($command,$output);

            if($last_line !== "Success...let's go surfing!"){
                $errors[] = array_pop($output);
            }
        // output useful information for debugging
        } elseif($action == 'debug') {
            $output[] = "IP ADDRESS = {$_SERVER['SERVER_ADDR']}";
            $output[] = "WHOAMI = ".`whoami`;
            $output[] = "SCRIPT DIR = ".dirname(__DIR__);
            $output[] = "CURRENT DIR = ".`pwd`;
            $output[] = "RUBY VERSION = " . `ruby -v`;
            $output[] = "PHP VERSION = " . `php -r 'echo phpversion();'`;
            $output[] = "APACHE VERSION = " . `apache2 -v`;
            $output[] = "UBUNTU VERSION = " . `lsb_release -d`;
        
        // get total awaiting approval via ruby script
        } elseif($action == 'orders') {
            $command = sprintf('ruby %s/awaiting_fulfilment.rb', dirname(__DIR__));
            $result = exec($command,$output);
            $jsonString = implode("\n",$output);
            $json = json_decode($jsonString,true);
            echo(json_encode($json));
            header('Content-type: application/json');
            exit();
        }
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bigcommerce</title>
    <link href="assets/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Sticky footer styles
    -------------------------------------------------- */
    html {
        position: relative;
        min-height: 100%;
    }
    body {
        margin-bottom: 60px; /* Margin bottom by footer height */
    }
    .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 60px; /* Set the fixed height of the footer here */
        line-height: 60px; /* Vertically center the text there */
        background-color: #f5f5f5;
    }
    </style>
</head>
<body>
    <header>
        <div class="collapse bg-dark" id="navbarHeader">
            <div class="container">
                <div class="row">
                    <div class="col-sm-8 col-md-7 py-4">
                        <h4 class="text-white">About</h4>
                        <p class="text-muted">Interact with BigCommerce to produce delivery lables for orders awaiting fulfilment.</p>
                    </div>
                    <div class="col-sm-4 offset-md-1 py-4">
                        <h4 class="text-white">Links</h4>
                        <ul class="list-unstyled">
                            <li><a href="https://github.com/openenergymonitor/BigCommerceAPI_OrderAddressLabelPrint" class="text-white">Github</a></li>
                            <li><a href="?action=debug" class="text-white">Debug</a></li>
                            <li><a href="info.php" class="text-white">PHP info</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container d-flex justify-content-between">
                <a href="https://www.bigcommerce.com/" class="navbar-brand d-flex align-items-center">
                    <img src="assets/BigCommerce-logomark-light.svg" style="height:2em;margin-right:.5em">
                    <strong>BigCommerce</strong>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
    </header>
    
    <main role="main" class="mb-4">
        <section class="jumbotron text-center">
            <div class="container">
                <h1 class="jumbotron-heading"><a class="text-body" href="<?php echo str_replace( 'index.php', '', $_SERVER['PHP_SELF'] ) ?>">Address Labels</a></h1>
                <p class="lead text-muted">Print all labels awaiting fulfilment, or print individual label based on order number.</p>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <form class="mr-1">
                            <input type="hidden" name="action" value="print_all">
                            <button id="printAllButton" class="btn btn-lg btn-primary">Print All</button>
                        </form>
                        <form>
                            <input type="hidden" name="action" value="print_single">
                            <div class="input-group input-group-lg mb-3">
                                <input type="search" pattern="\d*" name="order_number" class="form-control text-right" placeholder="Order Number" required value="<?php echo $order_number ?>">
                                <div class="input-group-append" id="button-addon4">
                                    <button class="btn btn-outline-secondary">Print One</button>
                                </div>
                            </div> 
                        </form>
                    </div> 
                </form>

                <pre class="text-left d-flex justify-content-center"><?php echo implode("\n",$output); ?></pre>

                <?php if(!empty($errors)): ?>
                    <h4 title="Error running the following command:\n <?php echo $command ?>">Error:</h4>
                    <pre class="text-left d-flex justify-content-center"><?php echo implode("\n",$errors); ?></pre>
                <?php endif; ?>
            </div>
        </section>
        <section id="list">
            <div class="container">
                <h4 id="list_title" class="text-center"><button class="btn btn-outline-primary" onclick="getOrderAwaitingFulfillment()">Get List of Orders Awaiting Fulfillment</button></h4>
                <div class="d-flex justify-content-center">
                    <div id="order-list" class="list-group text-left col-sm-6 p-0 mb-5"></div>
                    <template id="order-list-items">
                        <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    <small class="country badge badge-secondary badge-pill"></small>
                                    <span class="customer_name"></span> 
                                    <small class="date ml-2 text-secondary"></small>
                                </h5>
                                <h4 class="order_number text-primary"></h4>
                            </div>
                            <blockquote class="mb-1">
                                <p class="mb-0 customer_message"></p>
                                <footer class="email blockquote-footer"></footer>
                            </blockquote>
                        </a>
                    </template>
                </div>
            </div>
        </section>
    </main>
    
    <footer class="text-muted footer">
        <div class="container">
            <p class="text-center"><abbr title="GNU GPL">🄯</abbr> OpenEnergyMonitor. Source on <a href="https://github.com/openenergymonitor"> on Github</a>.</p>
        </div>
    </footer>
    
    <script src="assets/jquery.slim.min.js"></script>
    <script src="assets/popper.min.js"></script>
    <script src="assets/bootstrap.min.js"></script>
    <script src="assets/moment.min.js"></script>
    <script>
        function getOrderAwaitingFulfillment(){
            document.getElementById('list_title').innerHTML = "Loading list&hellip;"
            // get the details regarding the orders via an AJAX call
            var xhr = new XMLHttpRequest()
            xhr.open('GET', '?action=orders')
            xhr.setRequestHeader('Content-Type', 'application/json')
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let orderStatus = JSON.parse(xhr.responseText)
                    if (orderStatus.orders.length>0) {
                        document.getElementById('printAllButton').title = orderStatus.orders.length + ' orders ' + orderStatus.name
                        document.getElementById('list_title').innerHTML = 'Orders '+ orderStatus.name + ': ('+orderStatus.orders.length+')'
                        let list = document.getElementById('order-list')
                        orderStatus.orders.forEach(function (order) {
                            // console.log(order)
                            listItem = document.createElement('div')
                            listItemTemplate = document.getElementById('order-list-items').innerHTML
                            listItem.innerHTML = listItemTemplate
                            listItem.querySelector('.customer_message').innerText = order.customer_message
                            listItem.querySelector('.email').innerText = order.billing_address.email
                            listItem.querySelector('.country').innerText = order.geoip_country_iso2
                            listItem.querySelector('.country').title = order.geoip_country
                            listItem.querySelector('.customer_name').innerText = order.billing_address.last_name +', '+order.billing_address.first_name.substr(0,1)
                            listItem.querySelector('.customer_name').title = 'total= '+format_currency(order.total_inc_tax, order.currency_code) + ' [shipping= '+format_currency(order.shipping_cost_inc_tax)+']'
                            listItem.querySelector('.order_number').innerText = order.id
                            listItem.querySelector('.date').innerText = moment(order.date_created).fromNow()
                            listItem.querySelector('.date').title = order.date_created
                            list.appendChild(listItem.firstElementChild)
                        })
                    } else {
                        document.getElementById('list_title').innerHTML = 'No orders ' + orderStatus.name + ' <button class="btn btn-outline-primary" onclick="getOrderAwaitingFulfillment()">Reload</button>'
                    }
                }
            }
            xhr.send()
        }
        // getOrderAwaitingFulfillment();
        // return the closest match up the DOM from the elem to the given selector. null if not found
        var getClosest = function ( elem, selector ) {
            if ( elem.matches( selector ) ) return elem
            for ( ; elem && elem !== document; elem = elem.parentNode ) {
                if ( elem.matches( selector ) ) return elem
            }
            return null;
        };

        // use a symbol for the recognized formats
        var format_currency = function(amount, currency) {
            currency = currency || 'GBP'
            let symbols = {GBP: '£', EUR: '€', USD: '$'}
            return (symbols[currency]||'') + Number(amount).toFixed(2) + (!symbols[currency] ? ' ('+currency+')': '')
        }

        //populate the search field on click of the list items
        document.addEventListener('click', function(event) {
            // if clicked element has a parent of ".list-group-item" then pass the order number to the search field
            let listItem = getClosest(event.target, '.list-group-item')
            if(listItem) {
                event.preventDefault()
                let order_number = listItem.querySelector('.order_number').innerText
                document.querySelector('[name="order_number"]').value = order_number
            }
        });

    </script>
</body>
</html>