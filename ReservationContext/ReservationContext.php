<?php

namespace Restaurant\ReservationContext;

use Restaurant\ReservationContext\App\TableAppService;

readonly class ReservationContext
{
    public function __construct(
        public TableAppService $tableAppService
    )
    {
    }
}