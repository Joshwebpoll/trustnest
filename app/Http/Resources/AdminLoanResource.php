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

            "total_payable" => Crypt::decryptString($this->total_payable),
            "remaining_balance" => Crypt::decryptString($this->remaining_balance),
            "total_paid" => Crypt::decryptString($this->total_paid),
        ];
    }
}
