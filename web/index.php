<?php
    define('validFileIncludeToggle',true);
    include 'settings.php';

    /***
     * runs a ruby script with the big commerce api calls. settings stored in web/settings.php
     * 
     * successful messages returned in $output
     * errors returned in $errors
     * 
     * routing like behaviour done using ?action= URL parameter
     * 
     */
    
    $errors[] = "";
    $output[] = "";
    if($action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING)){
        
        // Call Ruby Script
        // runs the same script with or without the additional order_number param.
        if($action=='print_all' || $action=='print_one') {
            // escape any user input to avoid system commands being ran incorrectly
            // @note: currently order_number can only be an integer
            if(!empty($_GET['order_number'])) {
                $order_number = escapeshellarg(filter_input(INPUT_GET, "order_number", FILTER_SANITIZE_NUMBER_INT));
                if(!empty($order_number)){
                    $errors[] = "Order number is not valid";
                    $order_number = "";
                }
            } else {
                $order_number = "";
            }
            $DIR = "/home/emrys/Documents/BigCommerceAPI_OrderAddressLabelPrint";// during development

            // add order number as ruby script input if available
            $env = sprintf('BC_USERNAME=%s BC_API_KEY=%s BC_API_ENDPOINT_LEGACY=%s',$BC_USERNAME,$BC_API_KEY,$BC_API_ENDPOINT_LEGACY);
            $command = $env .' '. sprintf('ruby %s/shop_download.rb %s 2>&1', $DIR, $order_number);
            $output[] = exec($command,$errors);
        // output usefull information for debugging
        } elseif($action == 'debug') {
            $output[] = "IP ADDRESS = {$_SERVER['SERVER_ADDR']}";
            $output[] = "WHOAMI = ".`whoami`;
            $output[] = "SCRIPT DIR = $DIR";
            $output[] = "CURRENT DIR = ".`pwd`;
            $output[] = "RUBY VERSION = " . `ruby -v`;
            $output[] = "PHP VERSION = " . `php -r 'echo phpversion();'`;
            $output[] = "APACHE VERSION = " . `apache2 -v`;
            $output[] = "UBUNTU VERSION = " . `lsb_release -d`;
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
    
    <main role="main">
        <section class="jumbotron text-center">
            <div class="container">
                <h1 class="jumbotron-heading">Address Labels</h1>
                <p class="lead text-muted">Print all labels awaiting fulfilment, or print individual label based on order number.</p>
                
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <form class="mr-1">
                            <input type="hidden" name="action" value="print_all">
                            <button class="btn btn-lg btn-primary">Print All</button>
                        </form>
                        <form>
                            <input type="hidden" name="action" value="print_single">
                            <div class="input-group input-group-lg mb-3">
                                <input type="text" pattern="\d*" name="order_number" class="form-control text-right" placeholder="Order Number" required>
                                <div class="input-group-append" id="button-addon4">
                                    <button class="btn btn-outline-secondary">Print One</button>
                                </div>
                            </div> 
                        </form>
                    </div> 
                </form>
                <pre class="text-left"><?php echo implode("\n",$output); ?></pre>
                <?php if(!empty($errors)): ?>
                <h4>Errors</h4>
                <p>Error running: <code><?php echo $command ?></code></p>
                <pre class="text-left"><?php echo implode("\n",$errors); ?></pre>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <footer class="text-muted">
        <div class="container">
            <p class="text-center"><a href="https://github.com/openenergymonitor">OpenEnergyMonitor on Github</a> or read our <a href="https://github.com/openenergymonitor/BigCommerceAPI_OrderAddressLabelPrint/blob/master/README.md">README.md</a>.</p>
        </div>
    </footer>
    
    <script src="assets/jquery.slim.min.js"></script>
    <script src="assets/popper.min.js"></script>
    <script src="assets/bootstrap.min.js"></script>
</body>
</html>