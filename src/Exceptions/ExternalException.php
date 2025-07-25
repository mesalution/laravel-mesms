<?php

namespace Mesalution\LaravelMesms\Exceptions;

class ExternalException extends SmsException
{
    protected $message = "Received error from provider [%s]: %s";
    protected string $userMessage = "ได้รับข้อผิดพลาดที่ระบุสาเหตุได้จากผู้ให้บริการ SMS";
}
