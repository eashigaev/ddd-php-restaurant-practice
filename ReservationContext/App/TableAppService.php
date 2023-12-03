<?php

namespace Restaurant\ReservationContext\App;

use DateTime;
use Restaurant\Kernel\Infra\Moment\MomentInterface;
use Restaurant\ReservationContext\Domain\Table;
use Restaurant\ReservationContext\Domain\TimeSlot;
use Restaurant\ReservationContext\Infra\Repositories\TableRepositoryInterface;

readonly class TableAppService
{
    public function __construct(
        private MomentInterface          $moment,
        private TableRepositoryInterface $tableRepository
    )
    {
    }

    // Queries

    public function getTable(string $tableId): ?Table
    {
        return $this->tableRepository->ofId($tableId);
    }

    /** @return Table[] */
    public function getTableList(array $criteria): array
    {
        return $this->tableRepository->byCriteria();
    }

    // Commands

    public function addTable(string $name, int $capacity): string
    {
        $table = Table::add(uniqid(), $name, $capacity);
        $this->tableRepository->save($table);

        return $table->id;
    }

    public function changeTable(string $tableId, string $name, int $capacity): string
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $table->change($name, $capacity);
        $this->tableRepository->save($table);
    }

    public function makeTableReservation(
        string $tableId, string $customer, DateTime $startAt, DateTime $finishAt, int $capacity
    ): string
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $timeSlot = TimeSlot::from($startAt, $finishAt);
        $momentAt = $this->moment->now();

        $reservation = $table->makeReservation(
            uniqid(), $customer, $timeSlot, $capacity, $momentAt
        );
        $this->tableRepository->save($table);

        return $reservation->id;
    }

    public function changeTableReservation(
        string $tableId, string $reservationId, string $customer, DateTime $startAt, DateTime $finishAt, int $capacity
    ): void
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $timeSlot = TimeSlot::from($startAt, $finishAt);
        $momentAt = $this->moment->now();

        $table->changeReservation(
            $reservationId, $customer, $timeSlot, $capacity, $momentAt
        );
        $this->tableRepository->save($table);
    }

    public function cancelTableReservations(string $tableId, string $reservationId): void
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $table->cancelReservation($reservationId);
        $this->tableRepository->save($table);
    }

    public function cancelTableExpiredReservations(string $tableId, ?DateTime $momentAt = null): void
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $momentAt = $momentAt ?? $this->moment->now();

        $table->cancelExpiredReservations($momentAt);
        $this->tableRepository->save($table);
    }

    public function takeTableReservation(string $tableId): void
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $momentAt = $this->moment->now();

        $table->takeReservation(uniqid(), $momentAt);
        $this->tableRepository->save($table);
    }

    public function takeTable(string $tableId, DateTime $finishAt): void
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $momentAt = $this->moment->now();

        $table->take(uniqid(), $momentAt, $finishAt);
        $this->tableRepository->save($table);
    }

    public function vacateTable(string $tableId): void
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $table->vacate($this->tableStatService);
        $this->tableRepository->save($table);
    }

    //

    public function updateTableSession(string $sessionId, string $ordersCount): string
    {
        $table = $this->tableRepository->ofSessionId($sessionId);
        assert($table);

        $table->changeSessionOrdersCount($ordersCount);
        $this->tableRepository->save($table);
    }
}