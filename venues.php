<?php

    // Load configuration
    include('config.php');

    // Include libraries
    require_once('inc/purplewifi.class.php');
    require_once('inc/Mailchimp.php');


    // Now create new Purple WiFi api instance.

    $api = new PurpleAPIConnection($public_key, $private_key, $domain);

    // Loop through each of our venues to extract visitors
    $venues = $api->getEndpoint('/venues');
    foreach ($venues['data']['venues'] as $venue) {
        echo "Visitors to ".$venue["name"]."... ".$venue["id"]."\n";

    }

?>
