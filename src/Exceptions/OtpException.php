<?php

namespace Mesalution\LaravelMesms\Exceptions;

class OtpException extends SmsException
{
    protected $message = "Otp error [%s]: %s";
    protected string $userMessage = "OTP ไม่ถูกต้อง";
}
