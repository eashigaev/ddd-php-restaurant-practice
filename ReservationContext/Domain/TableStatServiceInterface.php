<?php

namespace Restaurant\ReservationContext\Domain;

interface TableStatServiceInterface
{
    public function hasActiveSession(Table $table): bool;
}