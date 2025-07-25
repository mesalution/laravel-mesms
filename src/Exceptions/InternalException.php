<?php

namespace Mesalution\LaravelMesms\Exceptions;

class InternalException extends SmsException
{
    protected $message = "Internal error [%s]: %s";
    protected string $userMessage = "เกิดข้อผิดพลาดภายในระบบการส่ง SMS";
}
