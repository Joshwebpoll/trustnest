<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CpContribution;
use Illuminate\Http\Request;
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
            $transaction_id = 'CONT-' . strtoupper(uniqid() . mt_rand(1000, 9999));
            $reference = 'CONT' . time() . rand(100, 999);
            CpContribution::create([
                "member_id" => $request->member_id,
                "transaction_id" => $transaction_id,
                "contribution_type" => $request->contribution_type,
                "amount_contributed" => Crypt::encryptString($request->amount_contributed),
                "payment_method" => $request->payment_method,
                "reference_number" => $reference,
                'account_number' => $request->account_number,
                "contribution_date" => $request->contribution_date,
                "status" => $request->status,
                "contribution_deposit_type" => $request->contribution_deposit_type
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
