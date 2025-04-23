<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterestResource extends JsonResource
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
            "min_amount" => $this->min_amount,
            "max_amount" => $this->max_amount,
            "interest_rate" => $this->interest_rate,
            "created_at" => $this->created_at
        ];
    }
}
