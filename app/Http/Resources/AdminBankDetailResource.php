<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminBankDetailResource extends JsonResource
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
            'bank_name' => $this->bank_name,
            'bank_code' => $this->bank_code,
            'bank_type' => $this->bank_type,
            'country_code' => $this->country_code,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
