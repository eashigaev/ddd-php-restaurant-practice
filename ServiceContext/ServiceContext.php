<?php

namespace Restaurant\ServiceContext;

use Restaurant\ServiceContext\App\OrderAppService;

readonly class ServiceContext
{
    public function __construct(
        public OrderAppService $orderAppService
    )
    {
    }
}