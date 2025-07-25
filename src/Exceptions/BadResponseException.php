<?php

namespace Mesalution\LaravelMesms\Exceptions;

class BadResponseException extends SmsException
{
    protected $message = "Received bad response from provider [%s]: %s";
    protected string $userMessage = "ได้รับข้อมูลที่ไม่ถูกต้องตามรูปแบบที่ต้องการจากผู้ให้บริการ SMS";
}
