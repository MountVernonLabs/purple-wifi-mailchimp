<?php

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

?>