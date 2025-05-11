<?php

namespace App\Http\Controllers;

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
        $userid = Auth::user()->id;
        $totalSavings = CpContribution::where('user_id', $userid)->where('contribution_type', 'savings')->sum(Crypt::encryptString("amount_contributed"));
        $totalShares = CpContribution::where('user_id', $userid)->where('contribution_type', 'shares')->sum(Crypt::encryptString("amount_contributed"));
        $activeLoans = CpLoan::where('user_id', $userid)->whereIn('status', ['approved', 'disbursed'])->count();
        $toatlLoanAmount = CpLoan::where('user_id', $userid)->whereIn('status', ['approved', 'disbursed'])->sum(Crypt::encryptString("total_payable"));
    }
}
