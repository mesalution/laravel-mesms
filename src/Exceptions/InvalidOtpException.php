<?php

namespace Mesalution\LaravelMesms\Exceptions;

class InvalidOtpException extends OtpException
{
    protected $message = "Invalid OTP [%s]";
    protected string $userMessage = "OTP ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง";
}
