<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class AdminReferralResource extends JsonResource
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
            'referrer_id' => $this->referrer_id,
            'referral' => $this->whenLoaded('referrer', function () {
                return [
                    'name' => $this->referrer->name,
                    'surname' => $this->referrer->surname,
                    'lastname' => $this->referrer->lastname,
                    'email' => $this->referrer->email,
                ];
            }),
            'referred_user' => $this->whenLoaded('referredUser', function () {
                return [
                    'name' => $this->referredUser->name,
                    'surname' => $this->referredUser->surname,
                    'lastname' => $this->referredUser->lastname,
                    'email' => $this->referredUser->email,
                ];
            }),
            'asUserContributed' => $this->asUserContributed,
            'referred_user_id' => $this->referred_user_id,
            'reward_amount' => Crypt::decryptString($this->reward_amount),
            'status' => $this->status,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
        ];
    }
}
