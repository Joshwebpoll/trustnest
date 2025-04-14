<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CpContribution;
use App\Models\CpLoan;
use App\Models\CpMember;
use App\Models\CpMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ContributionController extends Controller
{
    public function saveContribution(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'member_id' => 'required|exists:cp_members,id',
                'account_number' => 'required|string|max:20|exists:account_details,account_number',
                'contribution_type' => 'required|in:savings,shares,fee',
                'amount_contributed' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'contribution_date' => 'required|date',
                'status' => 'required|in:pending,completed',
                'contribution_deposit_type' => 'required|string|in:cash,transfer',

            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $admin_details = Auth::user();
            $transaction_id = 'CONT-' . strtoupper(uniqid() . mt_rand(1000, 9999));
            $reference = 'CONT' . time() . rand(100, 999);
            $cMember = CpMember::where('id', $request->member_id)->where('status', 'active')->first();
            $checkUserExistingLoan = CpLoan::where('user_id', $cMember->user_id)->first();
            if ($checkUserExistingLoan->status == 'disbursed' || $checkUserExistingLoan->status == 'defaulted' || $checkUserExistingLoan->status == 'approved') {
                return response()->json([
                    'status' => true,
                    'message' => "The user have an unpaid loan"
                ], 200);
            }

            if ($request->status == "completed") {
                $contribution = CpContribution::create([
                    "member_id" => $request->member_id,
                    "transaction_id" => $transaction_id,
                    "contribution_type" => $request->contribution_type,
                    "amount_contributed" => Crypt::encryptString($request->amount_contributed),
                    "payment_method" => $request->payment_method,
                    "reference_number" => $reference,
                    'account_number' => $request->account_number,
                    "contribution_date" => $request->contribution_date,
                    "status" => $request->status,
                    "contribution_deposit_type" => $request->contribution_deposit_type,
                    "processed_by_id" => $admin_details->id,
                    "processed_by_name" => $admin_details->name . $admin_details->lastname,
                    "processed_by_email" => $admin_details->email,
                ]);
                $contributionDetails = CpContribution::where("transaction_id", $transaction_id)->first();
                //Update member totals
                $member = CpMember::where("id", $contribution->member_id)->first();
                $decryptContribution = Crypt::decryptString($contributionDetails->amount_contributed);
                $decryptShares = Crypt::decryptString($member->total_shares);
                $decryptSavings = Crypt::decryptString($member->total_savings);

                if ($contribution->contribution_type == 'savings') {
                    $addBalanceSavings = bcadd($decryptSavings, $decryptContribution, 2);

                    $member->update([
                        "total_savings" => Crypt::encryptString($addBalanceSavings)
                    ]);
                }
                if ($contribution->contribution_type == 'shares') {
                    $addBalanceShares = bcadd($decryptShares, $decryptContribution, 2);

                    $member->update([
                        "total_shares" => Crypt::encryptString($addBalanceShares)
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'created successfully'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function getContribution()
    {
        try {
            $getContribution = CpContribution::all();
            return response()->json([
                "status" => true,
                "contribution" => $getContribution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
