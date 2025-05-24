<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserLoanResource;
use App\Mail\LoanEmailToAdmin;
use App\Mail\LoanEmailToUser;
use App\Models\AccountDetail;
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
                'amount' => 'required|numeric',
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
            $checkLoanTable = CpLoan::where("user_id", $user_id)->whereIn('status', ['pending', 'approved', "disbursed", 'defaulted'])
                ->first();;

            if ($checkLoanTable) {
                return response()->json([
                    "status" => false,
                    'message' => 'You already have an existing loan application or active loan.'
                ], 500);
            }

            //Get User Wallet Balance, May Use this Later
            // $getUserAmount = WalletUser::where('user_id', $user_id)->where('status', 'enable')->first();
            // $totalContributions = Crypt::decryptString($getUserAmount->wallet_balance);

            //Get Members Shares and Savings

            $getMembers = CpMember::where('user_id', $user_id)->where('status', 'active')->first();
            $sumSavings = Crypt::decryptString($getMembers->total_shares);
            $sumShares = Crypt::decryptString($getMembers->total_savings);
            $totalContributions = bcadd($sumSavings, $sumShares, 2);
            $getAcctNumber = AccountDetail::where('user_id', $user_id)->first();

            // Check if loan amount is within double the contribution
            if ($request->amount >= ($totalContributions * 2)) {
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
                return response()->json(['status' => false, 'message' => 'No interest rate set.'], 500);
            }
            $requestedAmount = bcmul($request->amount, 2, 2);
            // Calculate total payable (loan amount + interest)
            $totalPayable = $requestedAmount + ($requestedAmount * (intval($interestRate->interest_rate) / 100));
            $durationMonths =  $interestRate->loan_duration;  //intval($interestRate->loan_duration); //12;
            $monthlyRepayment = $totalPayable / $durationMonths;
            $remainingBalance = bcmul($request->amount, 2, 2);
            $startDate = Carbon::now(); // loan starts now
            $endDate = $startDate->copy()->addMonths(intval($durationMonths)); //load ends in

            $loan = CpLoan::create([
                "user_id" => $user_id,
                'loan_number' => 'LOAN' . time() . rand(100, 999),
                "amount" => Crypt::encryptString($requestedAmount),
                "interest_rate" => $interestRate->interest_rate,
                "duration_months" => $durationMonths,
                'monthly_repayment' => Crypt::encryptString($monthlyRepayment),
                "total_payable" => Crypt::encryptString($totalPayable),
                "decreasing_amount" => Crypt::encryptString($requestedAmount),
                "increasing_amount" => Crypt::encryptString(0),
                "remaining_balance" => Crypt::encryptString($remainingBalance),
                "total_paid" => Crypt::encryptString(0),
                "total_interest_paid" => Crypt::encryptString(0),
                "over_paid" => Crypt::encryptString(0),
                "customer_account_number" => $getAcctNumber->account_number,
                "membership_number" => $getMembers->membership_number,
                "start_date" => $startDate,
                "end_date" => $endDate,
                'application_date' => Carbon::now(),
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
                'loans' => $loan

            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function getUserLoan(Request $request)
    {
        try {
            $userId = Auth::user()->id;
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = CpLoan::query();
            $query->where('user_id', $userId);
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
            $getLoan = $query->paginate($perPage);
            return response()->json([
                "status" => true,
                "loans" => UserLoanResource::collection($getLoan)->response()->getData(true),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
