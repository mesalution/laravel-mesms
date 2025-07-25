<?php

namespace Mesalution\LaravelMesms\Data\Promotech;

use Spatie\LaravelData\Data;

class RequestOtpResponse extends Data
{
    public function __construct(
        public string $otcId,
        public string $otpId,
        public string $referrenceCode,
        public ?Success $success = null,
        public ?Error $error = null,

    ) {}
}
