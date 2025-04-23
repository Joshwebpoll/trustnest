<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class AdminLoanResource extends JsonResource
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
            "monthly_repayment" => Crypt::decryptString($this->monthly_repayment, 2),
            "total_payable" => Crypt::decryptString($this->total_payable, 2),
            "remaining_balance" => Crypt::decryptString($this->remaining_balance, 2),
            "total_paid" => Crypt::decryptString($this->total_paid, 2),
        ];
    }
}
