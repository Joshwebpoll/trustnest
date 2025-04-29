<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ContributionExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContributionResource;
use App\Models\AccountDetail;
use App\Models\CpContribution;
use App\Models\CpLoan;
use App\Models\CpMember;
use App\Models\CpMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ContributionController extends Controller
{
    public function saveContribution(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                // 'member_id' => 'required|exists:cp_members,id',
                'user_id' => 'required|exists:users,id',
                'account_number' => 'required|max:20|exists:account_details,account_number',
                'contribution_type' => 'required|in:savings,shares,fee',
                'amount_contributed' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                // 'contribution_date' => 'required|date',
                'status' => 'required|in:pending,completed',
                'contribution_deposit_type' => 'required|string|in:cash,transfer',

            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $admin_details = Auth::user();
            $transaction_id = 'CONT-' . strtoupper(uniqid() . mt_rand(1000, 9999));
            $reference = 'CONT' . time() . rand(100, 999);
            $cMember = CpMember::where('user_id', $request->user_id)->where('status', 'active')->first();
            $checkUserExistingLoan = CpLoan::where('user_id', $request->user_id)->first();

            $checkUserHasLoan = CpLoan::where('user_id', $request->user_id)->count();
            $compareAcctNumber = AccountDetail::where('user_id', $request->user_id)->first();

            if ($compareAcctNumber->account_number != $request->account_number) {
                return response()->json([
                    'status' => false,
                    'message' => "Invalid account number"
                ], 400);
            }
            if ($checkUserHasLoan > 0) {
                if ($checkUserExistingLoan->status !== 'disbursed' || $checkUserExistingLoan->status == 'defaulted' || $checkUserExistingLoan->status !== 'approved') {
                    return response()->json([
                        'status' => false,
                        'message' => "The user have an unpaid loan"
                    ], 400);
                }
            }


            if ($request->status == "completed" || $request->status == "pending") {
                $contribution = CpContribution::create([
                    "member_id" => $cMember->id,
                    "user_id" => $request->user_id,
                    "transaction_id" => $transaction_id,
                    "contribution_type" => $request->contribution_type,
                    "amount_contributed" => Crypt::encryptString($request->amount_contributed),
                    "payment_method" => $request->payment_method,
                    "reference_number" => $reference,
                    'account_number' => $request->account_number,
                    "contribution_date" => now(), //$request->contribution_date,
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

    public function getContribution(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = CpContribution::query();
            if ($search) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('transaction_id', 'like', "%$search%")
                            ->orWhere('account_number', 'like', "%$search%");
                    }
                );
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status); // assuming "active", "inactive", etc.
            }
            $getContribution = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "contributions" => ContributionResource::collection($getContribution)->response()->getData(true),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    //excel contribution export

    public function exportContribution()
    {
        return Excel::download(new ContributionExport, 'cp_contributions.xlsx');
    }

    public function getSingleContribution($id)
    {
        try {
            $singleLoan = CpContribution::where('id', $id)->first();
            if (!$singleLoan) {
                return response()->json([
                    "status" => false,
                    "message" => "contribution not found",

                ], 404);
            }

            return response()->json([
                "status" => true,
                "contribution" => new ContributionResource($singleLoan),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
