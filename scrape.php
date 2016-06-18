<?php

    // Load configuration
    include('config.php');

    // Include libraries
    require_once('inc/purplewifi.class.php');
    include('inc/mailchimp/MailChimp.php');

    use \DrewM\MailChimp\MailChimp;

    // Now create new Purple WiFi api instance.

    $api = new PurpleAPIConnection($public_key, $private_key, $domain);

    // Loop through each of our venues to extract visitors
    $venues = $api->getEndpoint('/venues');
    foreach ($venues['data']['venues'] as $venue) {
        echo "Visitors to ".$venue["name"]."... ";

        $visitors = $api->getEndpoint("/venue/".$venue["id"]."/visitors?from=".date("Ymd", time() - 60 * 60 * 24 * $days)."&to=".date("Ymd"));

        if ($visitors["response_code"] == "200"){
            echo "\n";
            foreach ($visitors['data']['visitors'] as $visitor){
                echo $visitor["email"]."... ";
                // MailChimp Subscribe

                $MailChimp = new MailChimp($api_key);
                $result = $MailChimp->post("lists/$list_id/members", [
                                'email_address' => $visitor["email"],
                                'status'        => 'subscribed',
                            ]);
                $result = $MailChimp->post("lists/$list_id/segments/$segment_id/members", [
                                'id' => $MailChimp->subscriberHash($visitor["email"]),
                                'email_address' => $visitor["email"],
                                'status'        => 'subscribed'
                            ]);
            echo "\n";
            }
        } else {
            echo "No Visitors\n";
        }
    }
