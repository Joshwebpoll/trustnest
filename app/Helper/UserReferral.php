<?php

namespace App\Helper;

class UserReferral
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    // Register a referral when a user signs up using a referral code
    public static function registerReferral($referal_codes, $user)
    {
        // $referralCode = ReferralCode::where('code', $code)->where('active', true)->first();

        // if (!$referralCode) {
        //     return false;
        // }

        //Make sure user is not referring themselves
        if ($referal_codes === $user->id) {
            return false;
        }

        // Update referred_by on new user
        // $newUser->update(['referred_by' => $referralCode->user_id]);

        // // Create referral record
        // $referral = Referral::create([
        //     'referrer_id' => $referralCode->user_id,
        //     'referred_id' => $newUser->id,
        //     'referral_code_id' => $referralCode->id,
        // ]);

        // Increment uses counter
        // $referralCode->increment('uses');

        // return $referral;
    }
}
