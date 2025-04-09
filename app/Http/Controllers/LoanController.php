<?php

namespace App\Http\Controllers;

use App\Mail\LoanEmailToAdmin;
use App\Mail\LoanEmailToUser;
use App\Models\CpInterestRate;
use App\Models\CpLoan;
use App\Models\CpMember;
use App\Models\User;
use App\Models\WalletUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    public function requestLoan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1000',
                'purpose' => 'required|string'
                // 'interest_rate' => 'required|numeric',
                // 'duration_months' => 'required|integer|min:1',
                // 'monthly_repayment' => 'required|integer|min:1',
                // 'total_payable' => 'required|integer|min:1',


            ]);

            $user_id = Auth::user()->id;
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            //Check weather the user as an existing loan
            $checkLoanTable = CpLoan::where("user_id", $user_id)->whereIn('status', ['pending', 'approved', "disbursed", 'rejected', 'defaulted'])
                ->first();;

            if ($checkLoanTable) {
                return response()->json([
                    'message' => 'You already have an existing loan application or active loan.'
                ], 403);
            }

            //Get User Wallet Balance, May Use this Later
            // $getUserAmount = WalletUser::where('user_id', $user_id)->where('status', 'enable')->first();
            // $totalContributions = Crypt::decryptString($getUserAmount->wallet_balance);

            //Get Members Shares and Savings
            $getMembers = CpMember::where('user_id', $user_id)->where('status', 'active')->first();
            $sumSavings = Crypt::decryptString($getMembers->total_shares);
            $sumShares = Crypt::decryptString($getMembers->total_savings);
            $totalContributions = bcadd($sumSavings, $sumShares, 2);

            // Check if loan amount is within double the contribution
            if ($request->amount > ($totalContributions * 2)) {
                return response()->json([
                    "status" => false,
                    'message' => 'You can only borrow up to double your contributions.'
                ], 400);
            }

            $user = User::find($user_id);

            // Check if user has been registered for at least 6 months
            $minimumDate = Carbon::now()->subMonths(6);
            $interestRate = CpInterestRate::latest()->first();

            // if ($user->created_at > $minimumDate) {
            //     return response()->json([
            //         'message' => 'You must be registered for at least 6 months before requesting a loan.'
            //     ], 403);
            // }
            if (!$interestRate) {
                return response()->json(['message' => 'No interest rate set.'], 400);
            }
            $requestedAmount = $request->amount * 2;
            // Calculate total payable (loan amount + interest)
            $totalPayable = $requestedAmount + ($requestedAmount * ($interestRate->interest_rate / 100));
            $durationMonths = 12;
            $monthlyRepayment = $totalPayable / $durationMonths;
            $startDate = Carbon::now(); // loan starts now
            $endDate = $startDate->copy()->addMonths($durationMonths); //load ends in

            $loan = CpLoan::create([
                "user_id" => $user_id,
                'loan_number' => 'LOAN' . time() . rand(100, 999),
                "amount" => Crypt::encryptString($requestedAmount),
                "interest_rate" => $interestRate->interest_rate,
                "duration_months" => $durationMonths,
                'monthly_repayment' => Crypt::encryptString($monthlyRepayment),
                "total_payable" => Crypt::encryptString($totalPayable),
                "remaining_balance" => Crypt::encryptString($totalPayable),
                "total_paid" => Crypt::encryptString(0),
                "start_date" => $startDate,
                "end_date" => $endDate,
                'application_date' => now(),
                'purpose' => $request->purpose
            ]);

            $getUser = User::where('id', $user_id)->where('status', 'enable')->first();
            $getAdmin = User::where('role', 'admin')->where('status', 'enable')->first();
            //SEND MAIL TO USER
            Mail::to($getUser->email)->send(new LoanEmailToUser($getUser, $loan));
            Mail::to($getAdmin->email)->send(new LoanEmailToAdmin($getUser, $loan));

            return response()->json([
                "status" => true,
                'message' => "Your loan application was successful.",



            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
