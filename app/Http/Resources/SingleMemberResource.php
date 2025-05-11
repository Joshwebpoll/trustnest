<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class SingleMemberResource extends JsonResource
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
            "membership_number" => $this->membership_number,
            "full_name" => $this->full_name,
            "id_number" => $this->id_number,
            "phone" => $this->phone,
            "joining_date" => $this->joining_date,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "total_shares" => number_format(Crypt::decryptString($this->total_shares), 2),
            "total_savings" => number_format(Crypt::decryptString($this->total_savings), 2)
        ];
    }
}
