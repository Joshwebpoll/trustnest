<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CpContribution;
use App\Models\CpLoan;
use App\Models\CpMember;
use App\Models\CpUserReferral;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardSummary extends Controller
{
    public function dashboardSummary()
    {
        try {
            $totalLoan = CpLoan::count();
            $activeLoan = CpLoan::whereIn('status', ['approved', 'disbursed'])->count();
            $pendingLoans = CpLoan::whereIn('status', ['pending'])->count();
            $completedLoans = CpLoan::whereIn('status', ['completed'])->count();


            $totalSavings = CpContribution::where('contribution_type', 'savings')->get()->reduce(function ($carry, $contribution) {
                return $carry + floatval($contribution->decrypted_amount_contributed);
            }, 0);

            $totalShares = CpContribution::where('contribution_type', 'shares')->get()->reduce(function ($carry, $contribution) {
                return $carry + floatval($contribution->decrypted_amount_contributed);
            }, 0);
            $totalUser = User::count();
            $totalMember = CpMember::count();
            $totalref = CpUserReferral::count();


            return response()->json([
                'status' => true,
                "totalLoan" => $totalLoan,
                "activeLoan" => $activeLoan,
                "pendingLoans" => $pendingLoans,
                "completedLoans" => $completedLoans,
                "totalSavings" => $totalSavings,
                "totalShares" => $totalShares,
                "totalUser" => $totalUser,
                "totalMember" => $totalMember,
                "totalref" => $totalref,

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                "message" => $e->$e->getMessage()
            ], 400);
        }
    }
}
