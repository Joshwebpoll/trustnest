<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminAccountResource extends JsonResource
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
            "bank_name" => $this->bank_name,
            "bank_account_number" => $this->bank_account_number,
            "bank_account_name" => $this->bank_account_name,
            "status" => $this->status
        ];
    }
}
