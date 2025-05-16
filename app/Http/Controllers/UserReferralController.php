<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserReferralResource;
use App\Models\CpUserReferral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReferralController extends Controller
{
    public function getReferral(Request $request)
    {
        try {
            $userId = 5;
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = CpUserReferral::query();
            $query->where('referred_user_id', $userId)->with('referrer');
            // $query->with('referredUser');
            //$query->with('referrer');
            if ($search) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('loan_number', 'like', "%$search%")
                            ->orWhere('status', 'like', "%$search%");
                    }
                );
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status); // assuming "active", "inactive", etc.
            }
            $getReferral = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "referral" => UserReferralResource::collection($getReferral)->response()->getData(true)
                // "loans" => UserReferralResource::collection($getReferral)->response()->getData(true),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
