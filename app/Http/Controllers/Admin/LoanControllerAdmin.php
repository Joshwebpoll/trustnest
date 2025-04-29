<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminLoanResource;
use App\Mail\LoanEmailToAdmin;
use App\Mail\LoanEmailToUser;
use App\Mail\LoanUpdateByAdmin;
use App\Models\CpInterestRate;
use App\Models\CpLoan;
use App\Models\CpMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoanControllerAdmin extends Controller
{
    public function getAllLoan(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->input('search');
            $query = CpLoan::query();
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
                "loans" => AdminLoanResource::collection($getLoan)->response()->getData(true),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }


    public function approveLoan(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [

                'guarantor_user_id' => 'required|string',
                'status' => 'required|in:pending,approved,disbursed,rejected,defaulted,completed,defaulted',


            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $admin = Auth::user();
            $findLoan = CpLoan::find($id);
            if (!$findLoan) {
                return response()->json([
                    'status' => true,
                    "message" => 'Loan not found'

                ], 404);
            }

            $lookupGurantor = User::find($request->guarantor_user_id);
            $getUserLoan = User::find($findLoan->user_id);
            if (!$lookupGurantor) {
                return response()->json([
                    'status' => true,
                    "message" => 'User not found'

                ], 404);
            }
            $findLoan->update([
                "status" => $request->status,
                "guarantor_user_id" => $request->guarantor_user_id,
                "guarantor_name" => $lookupGurantor->name,
                "guarantor_email" => $lookupGurantor->email,
                "approved_by" => $admin->id,
                "approved_at" => now()
            ]);
            Mail::to($getUserLoan->email)->send(new LoanUpdateByAdmin($findLoan, $getUserLoan));

            return response()->json([
                'status' => true,
                "message" => "Loan updated successfully",

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }


    public function createLoanUser(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "user_id" => 'required|exists:users,id',
                'amount' => 'required|numeric',
                'purpose' => 'required|string',
                "guarantor_user_id" => 'required|exists:users,id',
                'status' => 'required|in:pending,approved,disbursed,rejected,defaulted,completed,defaulted',
                // 'interest_rate' => 'required|numeric',
                // 'duration_months' => 'required|integer|min:1',
                // 'monthly_repayment' => 'required|integer|min:1',
                // 'total_payable' => 'required|integer|min:1',


            ]);

            $user_id = $request->user_id;
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            //Check weather the user as an existing loan
            $checkLoanTable = CpLoan::where("user_id", $user_id)->whereIn('status', ['pending', 'approved', "disbursed", 'defaulted'])
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
            // if ($request->amount > ($totalContributions * 2)) {
            //     return response()->json([
            //         "status" => false,
            //         'message' => 'You can only borrow up to double your contributions.'
            //     ], 400);
            // }

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
                'message' => "Loan created successfully",

            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function getSingleLoan($id)
    {
        try {
            $singleLoan = CpLoan::where('id', $id)->with('guarantor')->first();
            if (!$singleLoan) {
                return response()->json([
                    "status" => false,
                    "message" => "Loan not found",

                ], 404);
            }

            return response()->json([
                "status" => true,
                "loans" => new AdminLoanResource($singleLoan),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
