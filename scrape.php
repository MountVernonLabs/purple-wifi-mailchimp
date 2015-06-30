<?php

    // Enter your details here; find the Key and Secret from the excel CSV Spread sheet. 
	//If this has not been provided to you, you will need to request this from support@purplewifi.net
    //Note: you need to make sure you call upon http and not https.
    // You will also need to your amend your domain accordingly depending on which Portal region you are on or if you are a white label. 


    $public_key   = 'YOUR_PUBLIC_KEY';
    $private_key  = 'YOUR_PRIVATE_KEY';
    $domain       = 'region1.purpleportal.net';  // US Only

    // Now create new api instance.

    $api = new PurpleAPIConnection($public_key, $private_key, $domain);

    // Run a few API calls:
    // First: test api; this tests the APi Function to see if it recalls back correctly.

    print_r($api->getEndpoint('/ping')['data']);

    // Second: get venues; This retrieves a list of venues beneath that Customer record group; APi Key & Secret. 

    $response = $api->getEndpoint('/venues');
    if ($response['data']) {

        print_r($response['data']['venues']);

        // Third: grab a random venue

        $random = round(rand(0, count($response['data']['venues'])));
        $venue_id = $response['data']['venues'][$random]['id'];

        if ($venue_id) {

            // Fourth: get visitors from august 1st onwards for that venue

            $response = $api->getEndpoint("/venue/$venue_id/visitors?from=20140801");
            if ($response) {

                print_r($response);

            }

        }

    } else {

        echo "No venues found!\n";
        print_r($response);

    }


    // portal api class

    class PurpleAPIConnection {

        protected $public_key;
        protected $private_key;
        protected $domain;
        protected $protocol;
        protected $contenttype;

        public function __construct($public_key, $private_key, $domain, $protocol = 'https') {

            $this->public_key = $public_key;
            $this->private_key = $private_key;
            $this->domain = $domain;
            $this->protocol = $protocol;
            $this->contenttype = 'application/json';

            $this->ensureCurl();

        }

        private function ensureCurl() {

            if (!function_exists('curl_init')) {
                throw new \Exception("This requires PHP's CURL library");
            }

        }

        public function getEndpoint($url, $get_data = array(), $post_data = array()) {

            $url = '/api/company/v1' . $url;

            $date = new DateTime('now', new DateTimeZone('UTC'));
            $date = $date->format('D, d M Y H:i:s T');
            $post_string = is_array($post_data) ? http_build_query($post_data) : '';
            $query_string = is_array($get_data) ? http_build_query($get_data) : '';
            if ($query_string) {
                $url .= '?' . $query_string;
            }

            $token_string = 
                $this->contenttype . "\r\n" .
                $this->domain . "\r\n" .
                $url . "\r\n" .
                $date . "\r\n" .
                $post_string . "\r\n";

            $token = hash_hmac('sha256', $token_string, $this->private_key);

            $headers = array(
                'Content-Type: ' . $this->contenttype,
                'Content-Length: ' . strlen($post_string),
                'Date: ' . $date,
                'X-API-Authorization: ' . $this->public_key . ':' . $token
            );

            $url = $this->protocol . '://' . $this->domain . $url;

            return $this->parseResponse($this->curlRequest($url, 'GET', $headers, $post_string));

        }

        private function parseResponse($response_string) {

            $response = json_decode($response_string, true);

            if (!$response) {
                throw new \Exception("Unexpected output from $url:\n$response_string");
            }

            return $response;

        }

        private function curlRequest($url, $method = 'GET', $headers = array(), $post_string = '') {

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if (is_array($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            return curl_exec($ch);

        }

    }

