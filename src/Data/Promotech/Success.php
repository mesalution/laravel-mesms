<?php

namespace Mesalution\LaravelMesms\Data\Promotech;

use Spatie\LaravelData\Data;

class Success extends Data
{
    public function __construct(
        public string $message,
        public string $description,
        public string $code,
    ) {}
}
