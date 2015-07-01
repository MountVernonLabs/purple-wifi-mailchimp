<?php

    // Purple WiFi configuration

    $public_key   = '3135f09984723839cf35e218b199e7b6';
    $private_key  = 'd8eecd2593d04a4b3a38869448a7faf3';
    $domain       = 'region1.purpleportal.net';  // US Only

    // MailChimp configuration

    $api_key      = 'e69b20a328f3f3626333ab40512186e1-us2';
    $list_id      = '0a9fb91d40';
    $double_optin = TRUE;
    $master_group = 'Source';
    $sub_group    = 'WiFi Users';

    // Include libraries
    require_once('inc/purplewifi.class.php');
    require_once('inc/Mailchimp.php');

    // Set timezone
    date_default_timezone_set('America/New_York'); 

    $list = "";

    // Now create new Purple WiFi api instance.

    $api = new PurpleAPIConnection($public_key, $private_key, $domain);

    // Loop through each of our venues to extract visitors
    $venues = $api->getEndpoint('/venues');
    foreach ($venues['data']['venues'] as $venue) {
        echo "Visitors to ".$venue["name"]."... ";

        $visitors = $api->getEndpoint("/venue/".$venue["id"]."/visitors?from=".date("Ymd", time() - 60 * 60 * 24 * 120)."&to=".date("Ymd"));
        
        if ($visitors["response_code"] == "200"){
            echo "\n";
            foreach ($visitors['data']['visitors'] as $visitor){
                if ($visitor["email"] != ""){
                    echo $visitor["email"]."... ";
                    $list = $list.$visitor["email"]."\n";
                }
            }
            echo "\n";
        } else {
            echo "No Visitors\n";
        }
    }
    file_put_contents('list.txt', $list);


?>


  