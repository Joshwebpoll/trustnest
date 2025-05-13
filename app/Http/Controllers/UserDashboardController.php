<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserDashboardContributionResource;
use App\Models\CpContribution;
use App\Models\CpLoan;
use App\Models\CpRepayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class UserDashboardController extends Controller
{
    public function dashboardSummary()
    {
        try {
            $userid = Auth::user()->id;


            $totalSavings = CpContribution::where('user_id', $userid)->where('contribution_type', 'savings')->get()->reduce(function ($carry, $contribution) {
                return $carry + floatval($contribution->decrypted_amount_contributed);
            }, 0);

            $totalShares = CpContribution::where('user_id', $userid)->where('contribution_type', 'shares')->get()->reduce(function ($carry, $contribution) {
                return $carry + floatval($contribution->decrypted_amount_contributed);
            }, 0);
            $activeLoans = CpLoan::where('user_id', $userid)->whereIn('status', ['approved', 'disbursed'])->count();
            $pendingLoans = CpLoan::where('user_id', $userid)->whereIn('status', ['pending'])->count();
            $completedLoans = CpLoan::where('user_id', $userid)->whereIn('status', ['completed'])->count();
            $totalRepayment = CpRepayment::where('user_id', $userid)->sum('repayment_amount');
            // $toatlLoanAmount = CpLoan::where('user_id', $userid)->whereIn('status', ['approved', 'disbursed'])->get();



            return response()->json([
                'status' => true,
                "total_savings" => $totalSavings,
                "total_shares" => $totalShares,
                "active_loans" => $activeLoans,
                "pending_loans" => $pendingLoans,
                "completedLoans" => $completedLoans,
                "total_repayment" => $totalRepayment
                // "totalLoanAmount" => $toatlLoanAmount
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                "message" => $e->getMessage()
            ], 500);
            //throw $th;
        }
    }
    public function recentContribution()
    {
        try {
            $userid = Auth::user()->id;


            $recentContribution = CpContribution::where('user_id', $userid)->latest()->take(5)->get();





            return response()->json([
                'status' => true,
                "contributions" => UserDashboardContributionResource::collection($recentContribution),

                // "totalLoanAmount" => $toatlLoanAmount
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                "message" => $e->getMessage()
            ], 500);
            //throw $th;
        }
    }
}
