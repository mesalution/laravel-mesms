<?php

namespace Mesalution\LaravelMesms\Data\Promotech;

use Spatie\LaravelData\Data;

class VerifyOtpResponse extends Data
{
    public function __construct(
        public string $otpId,
        public bool $result,
        public bool $isErrorCount,
        public bool $isExprCode,
        public ?Success $success = null,
        public ?Error $error = null,

    ) {}
}
