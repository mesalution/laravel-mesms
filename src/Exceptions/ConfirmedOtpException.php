<?php

namespace Mesalution\LaravelMesms\Exceptions;

class ConfirmedOtpException extends OtpException
{
    protected $message = "OTP is confirmed [%s]";
    protected string $userMessage = "OTP ถูกยืนยันไปแล้ว กรุณาขอใหม่อีกครั้ง";
}
