<?php

namespace App\Helper;

class Verification
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
        if ($output === false) {
            $error = curl_error($ch);
            throw new \Exception('Something went wrong. Please try again later');
        }
        curl_close($ch);

        $json = json_decode($output, true);


        $accessToken = $json['responseBody']['accessToken'];

        // this is your access token
        $this->accessToken = $accessToken;
    }
    public function ninVerification($data)
    {
        $url = "https://api.monnify.com/api/v1/vas/nin-details";



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
            throw new \Exception($error);
        } else {

            $data = json_decode($response, true);
            if ($data["requestSuccessful"] === true && $data['responseMessage'] === 'success') {
                return $data;
            } else {
                throw new \Exception($data["responseMessage"]);
            }
        }

        curl_close($ch);
    }

    public function bvnVerification($data)
    {
        $url = "https://api.monnify.com/api/v1/vas/bvn-details-match";



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
            throw new \Exception($error);
        } else {

            $data = json_decode($response, true);
            if ($data["requestSuccessful"] === true && $data['responseMessage'] === 'success') {
                // return $data;
                $bvn = $data['responseBody']['bvn'];
                $bvnNameMatches = $data['responseBody']['name']['matchStatus'];
                $bvnPhone = $data['responseBody']['mobileNo'];
                if ($bvnPhone !== "FULL_MATCH") {
                    throw new \Exception('Invalid phone number. Please update your phone number in the profile page');
                }

                if ($bvnNameMatches !== "FULL_MATCH") {
                    throw new \Exception('Invalid name. Please update your name in the profile page');
                }
                return $bvn;
            } else {
                throw new \Exception($data["responseMessage"]);
            }
        }

        curl_close($ch);
    }
}
