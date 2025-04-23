<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'repayment_amount' => $this->repayment_amount,
            "remaining_balance" => $this->remaining_balance,
            'transaction_reference' => $this->transaction_reference,
            'payment_method' => $this->payment_method,
            'interest_component' => $this->interest_component,
            'due_date' => $this->due_date,
            'repayment_date' => $this->repayment_date,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'comment' => $this->repayment_comment,
        ];
    }
}
