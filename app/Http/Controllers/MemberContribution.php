<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContributionResource;
use App\Models\AccountDetail;
use App\Models\CpContribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberContribution extends Controller
{
    public function getContributions(Request $request)
    {


        try {
            $user = Auth::user();
            $trackUser = AccountDetail::where("user_id", $user->id)->first();
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = CpContribution::query();
            $query->where('account_number', $trackUser->account_number);
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
}
