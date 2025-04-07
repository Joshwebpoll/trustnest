<?php

namespace App\Http\Controllers\WebHook;

use App\Helper\BankAccount;
use App\Http\Controllers\Controller;
use App\Models\AccountDetail;
use App\Models\MonnifyPaymentTransaction;
use App\Models\Saving;
use App\Models\WalletUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class webHookController extends Controller
{
    public function paymentWebhook(Request $request)
    {
        // $eventData = $request->json()->all();
        $eventData = $request->input('eventData');
        $signature = $request->header('monnify-signature');
        $rawBody = $request->getContent();
        $secretKey = 'Z9NW81F1KUNWYAHRBPWSUDVFV6MXBX2G';
        $computedHash = hash_hmac('sha512', $rawBody, $secretKey);
        if ($signature == $computedHash) {
            $checkDuplicate = MonnifyPaymentTransaction::where("transaction_reference", $eventData['transactionReference'])->first();
            $verifyPayment = new BankAccount();
            $urlcode = urlencode($eventData['transactionReference']);
            $results = $verifyPayment->verifyUserPayment($urlcode);
            $requestSuccess = $results['requestSuccessful'] == true;
            $requestCode = $results["responseCode"] == "0";
            $requestMessage = $results["responseMessage"] == "success";
            if (!$checkDuplicate) {
                $verifyPayment = new BankAccount();
                $urlcode = urlencode($eventData['transactionReference']);
                $results = $verifyPayment->verifyUserPayment($urlcode);
                $requestSuccess = $results['requestSuccessful'] == true;
                $requestCode = $results["responseCode"] == "0";
                $requestMessage = $results["responseMessage"] == "success";
                if ($requestCode && $requestMessage && $requestSuccess) {
                    $result = MonnifyPaymentTransaction::create([
                        'transaction_reference' => $eventData['transactionReference'],
                        'payment_reference' => $eventData['paymentReference'],
                        'payment_description' => $eventData['paymentDescription'],
                        'payment_method' => $eventData['paymentMethod'],
                        'amount_paid' => $eventData['amountPaid'],
                        'total_payable' => $eventData['totalPayable'],
                        'settlement_amount' => $eventData['settlementAmount'],
                        'currency' => $eventData['currency'],
                        'payment_status' => $eventData['paymentStatus'],
                        'customer_name' => $eventData['customer']['name'],
                        'customer_email' => $eventData['customer']['email'],
                        // Payment source information (if available)
                        'bank_code' => $eventData['paymentSourceInformation'][0]['bankCode'] ?? null,
                        'amount_paid_from_bank' => $eventData['paymentSourceInformation'][0]['amountPaid'] ?? null,
                        'account_name' => $eventData['paymentSourceInformation'][0]['accountName'] ?? null,
                        'session_id' => $eventData['paymentSourceInformation'][0]['sessionId'] ?? null,
                        'account_number' => $eventData['paymentSourceInformation'][0]['accountNumber'] ?? null,
                        // Destination account information (if available)
                        'destination_bank_code' => $eventData['destinationAccountInformation']['bankCode'] ?? null,
                        'destination_bank_name' => $eventData['destinationAccountInformation']['bankName'] ?? null,
                        'destination_account_number' => $eventData['destinationAccountInformation']['accountNumber'] ?? null,
                    ]);
                    $saving_id = 'SVD-' . strtoupper(uniqid() . mt_rand(1000, 9999));
                    $transaction_id = Str::uuid();
                    if ($result) {


                        $getBanks = AccountDetail::where("account_number", $result->destination_account_number)->first();

                        // //Get the wallet balance
                        $getWalletBalance = WalletUser::where('user_id', $getBanks->user_id)->first();
                        $decryptBalance = Crypt::decryptString($getWalletBalance->wallet_balance);
                        $totalBalance = bcadd($decryptBalance, $results["responseBody"]["settlementAmount"], 2);
                        $getWalletBalance->update([
                            "wallet_balance" => Crypt::encryptString($totalBalance)
                        ]);
                        $checkDub = Saving::where("transaction_reference", $eventData['transactionReference'])->first();
                        if (!$checkDub) {
                            Saving::create([
                                "transaction_id" => $saving_id,
                                'account_number' => $result->destination_account_number,
                                'amount_deposited' => Crypt::encryptString($results["responseBody"]["settlementAmount"]),
                                'saving_type' => "saving",
                                'status' => 'completed',
                                'transaction_reference' => $eventData['transactionReference'],
                                'deposit_type' => "transfer",
                                'processed_by' => "1",
                                'deposit_date' => now(),

                            ]);
                        }
                    }
                    return response()->json([
                        "status" => true,
                        "messsage" => "deposited successfully"
                    ], 201);
                }
            }
        }
    }
}
