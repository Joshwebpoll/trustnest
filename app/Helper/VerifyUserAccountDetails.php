<?php

namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyUserAccountDetails
{

    public static function normalizeAndCompare(string $name1, string $name2): bool
    {
        $normalize = function ($name) {
            $words = explode(' ', strtoupper(trim($name)));
            sort($words);
            return implode(' ', $words);
        };

        return $normalize($name1) === $normalize($name2);
    }

    public static function verifyBankDetails($accountNumber, $bankCode, $getUsername)
    {



        $pageurl = "https://api.monnify.com/api/v1/disbursements/account/validate?accountNumber=$accountNumber&bankCode=$bankCode";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $pageurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $request = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Something went wrong, Please try again later');
        }

        curl_close($ch);

        if ($request) {
            $result = json_encode($request, true);
            $result = json_decode($request, true);
        }


        $json_array = json_encode($result);
        $jsonArray = json_decode($json_array, true);

        $message = $jsonArray['responseMessage'];



        //Check if the input is valid account number

        if ($message === "success") {
            $account_number = $jsonArray['responseBody']['accountNumber'];
            $account_name = $jsonArray['responseBody']['accountName'];
            $userDetails = $getUsername->name . " " . $getUsername->surname . " " . $getUsername->lastname;



            if (($account_number === $accountNumber) && (self::normalizeAndCompare($userDetails, $account_name))) {

                return $account_name;
            } else {

                throw new \Exception('Invalid name, your name must match with your bank account');
            }
        } else {
            throw new \Exception($message);
        }
    }
}
