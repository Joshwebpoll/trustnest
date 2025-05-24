<?php

namespace App\Http\Controllers\Admin;

use App\Helper\GeneralHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\RepaymentResource;
use App\Models\AccountDetail;
use App\Models\CpContribution;
use App\Models\CpInterestRate;
use App\Models\CpLoan;
use App\Models\CpMember;
use App\Models\CpRepayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RepaymentController extends Controller
{
    public function repayLoan(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $from = $request->input('from');
            $to = $request->input('to');
            $query = CpRepayment::query();
            if ($search) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('transaction_reference', 'like', "%$search%")
                            ->orWhere('status', 'like', "%$search%");
                    }
                );
            }
            // if ($from && $to) {
            //     $query->whereBetween('created_at', [$from, $to]);
            // }
            if ($from) {
                $query->whereDate('created_at', '>=', $request->from);
            }

            if ($to) {
                $query->whereDate('created_at', '<=', $request->to);
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status); // assuming "active", "inactive", etc.
            }
            $getRepayment = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "repayments" => RepaymentResource::collection($getRepayment)->response()->getData(true),

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
    public function createLoanRepayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_account_number' => 'required',
                'membership_number' => 'nullable |string',
                'repayment_amount' => 'required|string',
                // 'remaining_balance' => 'required|string',
                'payment_method' => 'required|string',
                'loan_number' => 'required|string',
                'status' => 'required|string|in:pending,completed,processing',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $checkLoanReapyment = CpLoan::where('loan_number', $request->loan_number)->whereIn('status', ['approved', 'disbursed'])->first();


            if ($checkLoanReapyment) {
                $amountRequested = Crypt::decryptString($checkLoanReapyment->amount);
                $remaining_balance = Crypt::decryptString($checkLoanReapyment->remaining_balance);
                $total_payable = Crypt::decryptString($checkLoanReapyment->total_payable);
                $total_paid = Crypt::decryptString($checkLoanReapyment->total_paid);
                $total_interest_paid  = Crypt::decryptString($checkLoanReapyment->total_interest_paid);
                $increasingAmount =  Crypt::decryptString($checkLoanReapyment->increasing_amount);
                $decreasing_amount =  Crypt::decryptString($checkLoanReapyment->decreasing_amount);
                $over_paid =  Crypt::decryptString($checkLoanReapyment->over_paid);


                //Check maybe the users as finish paying
                $remainingBalanceToBePaid = bcsub($remaining_balance, $request->repayment_amount, 2);
                // $checkLoanPaidFully = bccomp($total_paid, $total_payable, 2);
                $calculate_total_paid = bcadd($total_paid, $request->repayment_amount, 2);
                // $decimal = bcdiv($checkLoanReapyment->interest_rate, '100', 4);
                $getInterst = new GeneralHelper();
                $InterstRatePaid = $getInterst->CalculateInterest($remaining_balance, $checkLoanReapyment->interest_rate); //bcmul($remaining_balance, $decimal, 4);
                //add it to interest rate starting from zero
                $totalInterstRatePaid = bcadd($total_interest_paid, $InterstRatePaid, 2);
                $deCreaseingAmount = bcsub($decreasing_amount, $request->repayment_amount, 2);
                // $totalAmountPaidSofar = bcadd($request->repayment_amount, $checkLoanReapyment->total_paid, 2);
                //$totalAmountPaidSofarSecond = bcadd($request->repayment_amount, $checkLoanReapyment->increasing_amount, 2);
                $balanceIncreasing = bcadd($request->repayment_amount, $increasingAmount, 2);


                $transaction_id = 'RPY-' . strtoupper(uniqid() . mt_rand(1000, 9999));
                $reference_no = Str::uuid();
                $checkForRepayMentDuplicate = CpRepayment::where("transaction_reference", $transaction_id)->count();
                if ($checkForRepayMentDuplicate === 0) {


                    if ($amountRequested === $total_paid || $amountRequested < $total_paid || $request->repayment_amount >= $remaining_balance) {
                        //$overPaid = bcsub($request->repayment_amount, $remaining_balance, 2);
                        $overPaid = bcsub($request->repayment_amount, $remaining_balance, 2);
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
                                "repayment_amount" => $request->repayment_amount,
                                "remaining_balance" => $remainingTotalPaid,
                                "payment_method" => $request->payment_method,
                                "due_date" => Carbon::now()->addMonth(),
                                "transaction_reference" => $transaction_id,
                                "repayment_date" => now(),
                                "status" => $request->status,
                                "interest_paid" => $totalInterstRatePaid


                            ]
                        );
                        //Send Mail To User that Loan as been paid successfully
                        return response()->json([
                            "status" => true,
                            "message" => "Loan is completed, proceed to saving",
                        ], 200);
                    } else {
                        CpRepayment::create(
                            [
                                "loan_id" => $checkLoanReapyment->id,
                                "user_id" => $checkLoanReapyment->user_id,
                                "repayment_amount" => $request->repayment_amount,
                                "remaining_balance" => $remainingBalanceToBePaid,
                                "payment_method" => $request->payment_method,
                                "due_date" => Carbon::now()->addMonth(),
                                "transaction_reference" => $transaction_id,
                                "repayment_date" => now(),
                                "status" => $request->status,
                                "interest_paid" => $totalInterstRatePaid


                            ]
                        );
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
                return response()->json([
                    "status" => false,
                    "message" => "Loan still pending or completed",


                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }



    public function  getSingleRepayment($id)
    {
        try {
            $singleLoan = CpRepayment::where('id', $id)->first();
            if (!$singleLoan) {
                return response()->json([
                    "status" => false,
                    "message" => "Payment not found",

                ], 404);
            }

            return response()->json([
                "status" => true,
                "contributions" => new RepaymentResource($singleLoan),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
