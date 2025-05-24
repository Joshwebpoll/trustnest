<?php

namespace App\Http\Controllers\WebHook;

use App\Helper\BankAccount;
use App\Helper\GeneralHelper;
use App\Http\Controllers\Controller;
use App\Models\AccountDetail;
use App\Models\CpContribution;
use App\Models\CpLoan;
use App\Models\CpMember;
use App\Models\CpRepayment;
use App\Models\MonnifyPaymentTransaction;
use App\Models\Saving;
use App\Models\WalletUser;
use Carbon\Carbon;
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
                        // $getWalletBalance = WalletUser::where('user_id', $getBanks->user_id)->first();
                        // $decryptBalance = Crypt::decryptString($getWalletBalance->wallet_balance);
                        // $totalBalance = bcadd($decryptBalance, $results["responseBody"]["settlementAmount"], 2);
                        // $getWalletBalance->update([
                        //     "wallet_balance" => Crypt::encryptString($totalBalance)
                        // ]);


                        //Check maybe user as an unpaid loan
                        $checkLoanReapyment = CpLoan::where('user_id', $getBanks->user_id)->whereIn('status', ['approved', 'disbursed', 'defaulted'])->first();
                        $updateMemberPayment = CpMember::where('user_id', $getBanks->user_id)->where('status', 'active')->first();
                        $amountFromWeb = $results["responseBody"]["settlementAmount"];
                        if ($checkLoanReapyment) {
                            $transaction_id = 'RPY-' . strtoupper(uniqid() . mt_rand(1000, 9999));
                            $amountRequested = Crypt::decryptString($checkLoanReapyment->amount);
                            $remaining_balance = Crypt::decryptString($checkLoanReapyment->remaining_balance);
                            $total_payable = Crypt::decryptString($checkLoanReapyment->total_payable);
                            $total_paid = Crypt::decryptString($checkLoanReapyment->total_paid);
                            $total_interest_paid  = Crypt::decryptString($checkLoanReapyment->total_interest_paid);
                            $increasingAmount =  Crypt::decryptString($checkLoanReapyment->increasing_amount);
                            $decreasing_amount =  Crypt::decryptString($checkLoanReapyment->decreasing_amount);
                            $over_paid =  Crypt::decryptString($checkLoanReapyment->over_paid);


                            //Check maybe the users as finish paying
                            $remainingBalanceToBePaid = bcsub($remaining_balance, $amountFromWeb, 2);
                            // $checkLoanPaidFully = bccomp($total_paid, $total_payable, 2);
                            $calculate_total_paid = bcadd($total_paid, $amountFromWeb, 2);
                            // $decimal = bcdiv($checkLoanReapyment->interest_rate, '100', 4);
                            $getInterst = new GeneralHelper();
                            $InterstRatePaid = $getInterst->CalculateInterest($remaining_balance, $checkLoanReapyment->interest_rate); //bcmul($remaining_balance, $decimal, 4);
                            //add it to interest rate starting from zero
                            $totalInterstRatePaid = bcadd($total_interest_paid, $InterstRatePaid, 2);
                            $deCreaseingAmount = bcsub($decreasing_amount, $amountFromWeb, 2);
                            // $totalAmountPaidSofar = bcadd($amountFromWeb, $checkLoanReapyment->total_paid, 2);
                            //$totalAmountPaidSofarSecond = bcadd($amountFromWeb, $checkLoanReapyment->increasing_amount, 2);
                            $balanceIncreasing = bcadd($amountFromWeb, $increasingAmount, 2);
                            $checkForRepayMentDuplicate = CpRepayment::where("transaction_reference", $transaction_id)->count();
                            if ($checkForRepayMentDuplicate === 0) {
                                if ($amountRequested === $total_paid || $amountRequested < $total_paid || $amountFromWeb >= $remaining_balance) {
                                    //$overPaid = bcsub($amountFromWeb, $remaining_balance, 2);
                                    $overPaid = bcsub($amountFromWeb, $remaining_balance, 2);
                                    $addOverPaid = bcadd($over_paid, $overPaid);
                                    $remainingDueNotOverPaid = bcsub($amountRequested, $increasingAmount, 2);
                                    $remainingTotalPaid = bcsub($remaining_balance, $remainingDueNotOverPaid, 2);
                                    $remainigTotalPaid = bcadd($total_paid, $remainingDueNotOverPaid);
                                    $remainDecreasingBal = bcsub($decreasing_amount, $remainingDueNotOverPaid, 2);
                                    $remainIncreasingBal = bcadd($remainingDueNotOverPaid, $increasingAmount, 2);
                                    // return [$remainingDueNotOverPaid, $overPaid];
                                    $checkLoanReapyment->update([
                                        "status" => "completed",
                                        "remaining_balance" => Crypt::encryptString($remainingTotalPaid),
                                        "total_paid" => Crypt::encryptString($remainigTotalPaid),
                                        "decreasing_amount" => Crypt::encryptString($remainDecreasingBal),
                                        "increasing_amount" => Crypt::encryptString($remainIncreasingBal),
                                        "total_paid" => Crypt::encryptString($remainigTotalPaid),
                                        "total_interest_paid" => Crypt::encryptString($totalInterstRatePaid),
                                        "over_paid" => Crypt::encryptString($addOverPaid)
                                    ]);
                                    CpRepayment::create(
                                        [
                                            "loan_id" => $checkLoanReapyment->id,
                                            "user_id" => $checkLoanReapyment->user_id,
                                            "repayment_amount" => $amountFromWeb,
                                            "remaining_balance" => $remainingTotalPaid,
                                            "payment_method" => 'transfer',
                                            "due_date" => Carbon::now()->addMonth(),
                                            "transaction_reference" => $transaction_id,
                                            "repayment_date" => now(),
                                            "status" => 'completed',
                                            "interest_paid" => $totalInterstRatePaid


                                        ]
                                    );
                                    //Send Mail To User that Loan as been paid successfully
                                    return response()->json([
                                        "status" => true,
                                        "message" => "Loan is completed, proceed to saving",
                                    ], 200);
                                } else {
                                    try {

                                        CpRepayment::create(
                                            [
                                                "loan_id" => $checkLoanReapyment->id,
                                                "user_id" => $checkLoanReapyment->user_id,
                                                "repayment_amount" => $amountFromWeb,
                                                "remaining_balance" => $remainingBalanceToBePaid,
                                                "payment_method" => 'transfer',
                                                "due_date" => Carbon::now()->addMonth(),
                                                "transaction_reference" => $transaction_id,
                                                "repayment_date" => now(),
                                                "status" => 'completed',
                                                "interest_paid" => $totalInterstRatePaid


                                            ]
                                        );
                                    } catch (\Exception $e) {
                                        return $e;
                                    }
                                    $checkLoanReapyment->update([
                                        "remaining_balance" => Crypt::encryptString($remainingBalanceToBePaid),
                                        "total_paid" => Crypt::encryptString($calculate_total_paid),
                                        "decreasing_amount" => Crypt::encryptString($deCreaseingAmount),
                                        "increasing_amount" => Crypt::encryptString($balanceIncreasing),
                                        "total_paid" => Crypt::encryptString($calculate_total_paid),
                                        "total_interest_paid" => Crypt::encryptString($totalInterstRatePaid),

                                    ]);
                                    return response()->json([
                                        "status" => true,
                                        "message" => "Repayment successful",
                                    ], 200);
                                }
                            }
                        } else {


                            //Create a contribution and calculate the shares and savings balance

                            try {

                                $checkDuplicate = CpContribution::where("reference_number", $eventData['transactionReference'])->count();
                                if ($checkDuplicate === 0) {
                                    $transaction_id = 'CONT-' . strtoupper(uniqid() . mt_rand(1000, 9999));

                                    $depositAmounts = bcdiv($results["responseBody"]["settlementAmount"], "2", 2);
                                    $updateMemberPayment = CpMember::where('user_id', $getBanks->user_id)->where('status', 'active')->first();
                                    $decryptShares = Crypt::decryptString($updateMemberPayment->total_shares);
                                    $decryptSavings = Crypt::decryptString($updateMemberPayment->total_savings);
                                    $addBalanceSavings = bcadd($decryptSavings,  $depositAmounts, 2);
                                    $addBalanceShares = bcadd($decryptShares, $depositAmounts, 2);
                                    $updateMemberPayment->update([
                                        "total_savings" => Crypt::encryptString($addBalanceSavings),
                                        "total_shares" => Crypt::encryptString($addBalanceShares)
                                    ]);



                                    CpContribution::create([
                                        "member_id" => $updateMemberPayment->id,
                                        "user_id" => $updateMemberPayment->user_id,
                                        "transaction_id" => $transaction_id,
                                        "contribution_type" => 'savings',
                                        "amount_contributed" => Crypt::encryptString($depositAmounts),
                                        "payment_method" => 'transfer',
                                        "reference_number" => $eventData['transactionReference'],
                                        'account_number' => $result->destination_account_number,
                                        "contribution_date" => now(),
                                        "status" => 'completed',
                                        "contribution_deposit_type" => 'transfer',
                                        "processed_by" => "Automatic Payment"
                                    ]);
                                    CpContribution::create([
                                        "member_id" => $updateMemberPayment->id,
                                        "user_id" => $updateMemberPayment->user_id,
                                        "transaction_id" => $transaction_id,
                                        "contribution_type" => 'shares',
                                        "amount_contributed" => Crypt::encryptString($depositAmounts),
                                        "payment_method" => 'transfer',
                                        "reference_number" => $eventData['transactionReference'],
                                        'account_number' => $result->destination_account_number,
                                        "contribution_date" => now(),
                                        "status" => 'completed',
                                        "contribution_deposit_type" => 'transfer',
                                        "processed_by" => "Automatic Payment"
                                    ]);
                                } else {
                                    return response()->json([
                                        "status" => false,
                                        "messsage" => 'Record already exist' . Crypt::encryptString(0)
                                    ], 500);
                                }
                            } catch (\Exception $e) {
                                return $e;
                            }
                            return response()->json([
                                "status" => true,
                                "messsage" => $decryptShares . "deposited successfully"
                            ], 201);
                        }
                    }
                }
            }
        }
    }
}
