<?php

    // Purple WiFi configuration

    $public_key   = 'YOUR_PURPLE_WIFI_KEY';
    $private_key  = 'YOUR_PURPLE_WIFI_PRIVATE_KEY';
    $domain       = 'region1.purpleportal.net';  // US Only, International remove the region1.

    // MailChimp configuration

    $api_key      = 'YOUR_MAILCHIMP_API_KEY';
    $list_id      = 'YOUR_MAILCHIMP_LIST_ID';
    $double_optin = TRUE; // Do you want MailChimp to require a double-opt-in?  TRUE/FALSE
    $master_group = 'Source'; // Group you want to assign contacts to
    $sub_group    = 'WiFi Users'; // Sub group within the group above that you want to assign people to

    $days         = 1; // Days to retrieve

    // Set timezone
    date_default_timezone_set('America/New_York');

?>
