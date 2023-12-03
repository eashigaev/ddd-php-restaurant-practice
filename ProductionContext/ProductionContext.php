<?php

namespace Restaurant\ProductionContext;

use Restaurant\ProductionContext\App\ProductAppService;

readonly class ProductionContext
{
    public function __construct(
        public ProductAppService $productAppService
    )
    {
    }
}