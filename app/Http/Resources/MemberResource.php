<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "membership_number" => $this->membership_number,
            "full_name" => $this->full_name,
            "id_number " => $this->id_number,
            "phone" => $this->phone,
            "joining_date" => $this->joining_date,
            "status" => $this->status,
            "total_shares" => Crypt::decryptString($this->total_shares),
            "total_savings" => Crypt::decryptString($this->total_savings)
        ];
    }
}
