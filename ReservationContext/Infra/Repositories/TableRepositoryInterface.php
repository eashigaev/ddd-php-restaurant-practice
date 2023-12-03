<?php

namespace Restaurant\ReservationContext\Infra\Repositories;

use Restaurant\ReservationContext\Domain\Table;

interface TableRepositoryInterface
{
    public function ofId(string $id): ?Table;

    public function ofSessionId(string $id): ?Table;

    public function save(Table $table);

    /** @return string[] */
    public function allIds(): array;

    /** @return Table[] */
    public function byCriteria(): array;
}