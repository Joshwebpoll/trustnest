<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminReferralResource;
use App\Models\CpReferralPercentage;
use App\Models\CpUserReferral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReferralController extends Controller
{
    public function getAllReferral(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = CpUserReferral::query();
            $query->with('referredUser');
            $query->with('referrer');
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
            $getRefferal = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "referrals" => AdminReferralResource::collection($getRefferal)->response()->getData(true),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }


    public function getReferralRwardPercent()
    {
        try {

            $reward = CpReferralPercentage::first();
            return response()->json([
                "status" => true,
                "reward" => $reward

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function updateReferralReward(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [

                'min_amount' => 'nullable|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0',
                'referral_reward_percent' => 'required|numeric|min:0',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $updateReferralPercentage = CpReferralPercentage::find($id);
            if (!$updateReferralPercentage) {
                return response()->json([
                    'status' => false,
                    "message" => "Record not found"
                ], 404);
            }
            $updateReferralPercentage->fill($validator->validated());

            if (!$updateReferralPercentage->isDirty()) {
                return response()->json([
                    'status' => true,
                    "message" => "No changes detected"
                ], 200);
            }
            $updateReferralPercentage->update([
                'referral_reward_percent' => $request->referral_reward_percent,
                'min_amount' => $request->min_amount,
                'max_amount' => $request->max_amount

            ]);
            return response()->json([
                'status' => true,
                "message" => "Referral Updated succesfully"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
