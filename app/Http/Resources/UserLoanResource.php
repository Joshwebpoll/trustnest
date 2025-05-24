<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class UserLoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "loan_number" => $this->loan_number,
            "interest_rate" => $this->interest_rate,
            "duration_months" => $this->duration_months,
            "status" => $this->status,
            "amount" => Crypt::decryptString($this->amount),
            "monthly_repayment" => Crypt::decryptString($this->monthly_repayment),
            "total_payable" => Crypt::decryptString($this->total_payable),
            "remaining_balance" => Crypt::decryptString($this->remaining_balance),
            "total_paid" => Crypt::decryptString($this->total_paid),
            "total_interest_paid" => Crypt::decryptString($this->total_interest_paid),
            "decreasing_amount" => Crypt::decryptString($this->decreasing_amount),
            "increasing_amount" => Crypt::decryptString($this->increasing_amount),
            "over_paid" => Crypt::decryptString($this->over_paid),
            "created_at" => $this->created_at,
        ];
    }
}
