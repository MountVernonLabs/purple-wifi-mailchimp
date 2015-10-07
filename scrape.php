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
        echo "Visitors to ".$venue["name"]."... ";

        $visitors = $api->getEndpoint("/venue/".$venue["id"]."/visitors?from=".date("Ymd", time() - 60 * 60 * 24 * 30)."&to=".date("Ymd"));
        
        if ($visitors["response_code"] == "200"){
            echo "\n";
            foreach ($visitors['data']['visitors'] as $visitor){
                echo $visitor["email"]."... ";
                    // MailChimp Subscribe
                    $Mailchimp = new Mailchimp( $api_key );
                    $Mailchimp_Lists = new Mailchimp_Lists( $Mailchimp );
                    $merge_vars = array("FNAME"=>$visitor["first_name"],"LNAME"=>$visitor["last_name"],'groupings' => array( array("name"=>$master_group,"groups"=> array($sub_group))));
                    if (strpos($visitor["email"], 'none') === false){
                        try {
                            $subscriber = $Mailchimp_Lists->subscribe( $list_id, array('email' => $visitor["email"]), $merge_vars,'html',$double_optin,TRUE,FALSE);  
                            echo $subscriber['leid']."\n";
                        } catch( Mailchimp_ValidationError $e ){
                            echo "bad email, skipping...\n";
                        }
                    } else {
                         echo "bad email, skipping...\n";
                    }
                    
            }
            echo "\n";
        } else {
            echo "No Visitors\n";
        }
    }





  