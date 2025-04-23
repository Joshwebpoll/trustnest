<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RepaymentResource;
use App\Models\AccountDetail;
use App\Models\CpContribution;
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
                'loan_id' => 'required|string',
                'user_id' => 'required',
                'repayment_amount' => 'required|string',
                // 'remaining_balance' => 'required|string',
                'payment_method' => 'required|string',
                'repayment_date' => 'required|date',
                'status' => 'required|string|in:pending,completed,processing',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $checkLoanReapyment = CpLoan::where('user_id', $request->user_id)->whereIn('status', ['approved', 'disbursed'])->first();
            if ($checkLoanReapyment) {
                $remaining_balance = Crypt::decryptString($checkLoanReapyment->remaining_balance);
                $total_payable = Crypt::decryptString($checkLoanReapyment->total_payable);
                $total_paid = Crypt::decryptString($checkLoanReapyment->total_paid);
                //Check maybe the users as finish paying
                $remainingBalanceToBePaid = bcsub($remaining_balance, $request->repayment_amount, 2);
                $checkLoanPaidFully = bccomp($total_paid, $total_payable, 2);
                $calculate_total_paid = bcadd($total_paid, $request->repayment_amount, 2);

                $transaction_id = 'RPY-' . strtoupper(uniqid() . mt_rand(1000, 9999));
                $reference_no = Str::uuid();
                $checkForRepayMentDuplicate = CpRepayment::where("transaction_reference", $transaction_id)->count();
                if ($checkForRepayMentDuplicate === 0) {
                    if ($checkLoanPaidFully === 0) {
                        $checkLoanReapyment->update([
                            "status" => "completed"
                        ]);
                    } elseif ($checkLoanPaidFully === -1) {
                        CpRepayment::create([
                            "loan_id" => $request->loan_id,
                            "user_id" => $request->user_id,
                            "repayment_amount" => $request->repayment_amount,
                            "remaining_balance" => $remainingBalanceToBePaid,
                            "payment_method" => $request->payment_method,
                            "due_date" => Carbon::now()->addMonth(),
                            "transaction_reference" => $transaction_id,
                            "repayment_date" => now(),
                            "status" => $request->status
                        ]);
                        $checkLoanReapyment->update([
                            "remaining_balance" => Crypt::encryptString($remainingBalanceToBePaid),
                            "total_paid" => Crypt::encryptString($calculate_total_paid)

                        ]);
                    } else {
                        //  PROCCESS REFUND
                        $transaction_id = 'CONT-' . strtoupper(uniqid() . mt_rand(1000, 9999));

                        $refundOverPaid = bcsub($total_paid, $total_payable, 2);
                        $checkLoanReapyment->update([
                            "status" => "completed"
                        ]);

                        $updateMemberPayment = CpMember::where('user_id', $request->user_id)->where('status', 'active')->first();
                        $getAccountNumber = AccountDetail::where('user_id', $request->user_id)->first();
                        $depositAmounts = bcdiv($refundOverPaid, "2", 0);

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
                            "transaction_id" => $transaction_id,
                            "contribution_type" => 'savings',
                            "amount_contributed" => Crypt::encryptString($depositAmounts),
                            "payment_method" => 'Refund Loan Repayment',
                            "reference_number" => $reference_no,
                            'account_number' => $getAccountNumber->account_number,
                            "contribution_date" => now(),
                            "status" => 'completed',
                            "contribution_deposit_type" => 'cash',
                            "processed_by" => "Automatic Payment"
                        ]);
                        CpContribution::create([
                            "member_id" => $updateMemberPayment->id,
                            "transaction_id" => $transaction_id,
                            "contribution_type" => 'shares',
                            "amount_contributed" => Crypt::encryptString($depositAmounts),
                            "payment_method" => 'Refund Loan Repayment',
                            "reference_number" => $reference_no,
                            'account_number' =>  $getAccountNumber->account_number,
                            "contribution_date" => now(),
                            "status" => 'completed',
                            "contribution_deposit_type" => 'cash',
                            "processed_by" => "Automatic Payment"
                        ]);
                    }
                    return response()->json([
                        "status" => true,
                        "message" => "Repayment successfull",


                    ], 200);
                }
            } else {
                return response()->json([
                    "status" => true,
                    "message" => "Loan as been completed successfuly",


                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
