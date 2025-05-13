<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class UserDashboardContributionResource extends JsonResource
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
            'transaction_id' => $this->transaction_id,
            'contribution_type' => $this->contribution_type,
            'amount_contributed' => number_format(Crypt::decryptString($this->amount_contributed), 2),
            'account_number' => $this->account_number,
            'reference_number' => $this->reference_number,
            'contribution_date' => $this->contribution_date,
            'status' => $this->status,
            'contribution_deposit_type' => $this->contribution_deposit_type,
        ];
    }
}
