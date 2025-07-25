<?php

namespace Mesalution\LaravelMesms\Exceptions;

class RequestOtpException extends SmsException
{
    protected $message = "Request otp failed [%s]: %s";
    protected string $userMessage = "ขอ OTP ไม่สำเร็จ กรุณาทำรายการใหม่ในภายหลัง";
}
