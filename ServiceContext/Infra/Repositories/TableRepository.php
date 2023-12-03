<?php

namespace Restaurant\ServiceContext\Infra\Repositories;

use Restaurant\ReservationContext\ReservationContext;
use Restaurant\ServiceContext\Domain\Table;

readonly class TableRepository implements TableRepositoryInterface
{
    public function __construct(
        private ReservationContext $reservationContext
    )
    {
    }

    public function ofId(string $id): ?Table
    {
        $reservationTable = $this->reservationContext->tableAppService->getTable($id);

        return $reservationTable
            ? Table::from(
                $reservationTable->id,
                $reservationTable->session->id,
                $reservationTable->isTaken
            )
            : null;
    }
}