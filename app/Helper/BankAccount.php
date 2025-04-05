<?php

namespace App\Helper;

class BankAccount
{
    /**
     * Create a new class instance.
     */
    public $apiKey = 'MK_PROD_D2HK4HQ1R6';
    public $secretKey = 'Z9NW81F1KUNWYAHRBPWSUDVFV6MXBX2G';
    protected $accessToken;
    public function __construct()
    {
        $ch = curl_init();

        // Concatenate "ApiKey" + ":" +  "SecretKey", then Base 64 encode the string and prefix with the word "Basic". See in next line
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic ' . base64_encode($this->apiKey . ":" . $this->secretKey) // <---
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, "https://api.monnify.com/api/v1/auth/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);

        curl_close($ch);

        $json = json_decode($output, true);
        // print_r($json);

        $accessToken = $json['responseBody']['accessToken'];

        // this is your access token
        $this->accessToken = $accessToken;
    }
    public function createBankAccountForUsers($data)
    {
        $url = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";



        $payload = json_encode($data);
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Bearer ' . $this->accessToken // <---
        );

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {

            $data = json_decode($response, true);
            return $data;
        }

        curl_close($ch);
    }

    public function verifyUserPayment($ref)
    {

        $url = "https://api.monnify.com/api/v2/transactions/" . $ref;



        // Initialize a cURL session
        $ch = curl_init();

        // Set the URL for the GET request
        curl_setopt($ch, CURLOPT_URL, $url);

        // Return the response instead of outputting it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set custom headers, including the Authorization header
        $headers =
            $headers = array(
                'Content-Type:application/json',
                'Authorization: Bearer ' . $this->accessToken //
            );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the GET request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            // Print the response


            return json_decode($response, true);
        }

        // Close the cURL session
        curl_close($ch);
    }
}
