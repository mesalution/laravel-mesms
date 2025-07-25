<?php

namespace Mesalution\Sms\Contracts;

use Mesalution\Sms\Data\Otp;

interface Sms
{
    public function requestOTP(string $mobileNo): Otp;
    public function resendOTP(string $otpId): Otp;
    public function verifyOTP(string $otpId, string $otpCode): void;
}
