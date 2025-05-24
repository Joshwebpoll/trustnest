<?php

namespace App\Helper;

class GeneralHelper
{
    /**
     * Create a new class instance.
     */

    public function __construct() {}

    public function CalculateInterest($principal, $rate, $scale = 2)
    {
        $interst = bcdiv(bcmul($principal, $rate, $scale), '100', $scale);
        return $interst;
    }
}
