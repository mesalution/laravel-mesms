<?php

namespace Mesalution\LaravelMesms\Data\Promotech;

use Spatie\LaravelData\Data;

class Error extends Data
{
    public function __construct(
        public string $message,
        public string $description,
    ) {}
}
