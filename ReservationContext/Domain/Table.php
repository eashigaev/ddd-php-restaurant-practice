<?php

namespace Restaurant\ReservationContext\Domain;

use DateTime;
use Exception;
use Restaurant\Kernel\Infra\OptimisticLockingTrait;

class Table
{
    use OptimisticLockingTrait;

    public string $id;
    public string $name; //unique
    public int $capacity;
    public bool $isTaken;

    /** @var Reservation[] */
    public array $reservations;
    public ?Session $session;

    public static function add(string $id, string $name, int $capacity): static
    {
        $self = new static;
        $self->id = $id;
        $self->name = $name;
        $self->capacity = $capacity;
        $self->isTaken = false;
        $self->session = null;
        $self->reservations = [];
        return $self;
    }

    public function change(string $name, int $capacity): void
    {
        $this->name = $name;
        $this->capacity = $capacity;
    }

    // Reservations

    public function makeReservation(
        string $reservationId, string $customer, TimeSlot $timeSlot, int $capacity, DateTime $momentAt
    ): Reservation
    {
        $this->assertCanBeReserved($timeSlot, $capacity, $momentAt);

        $reservation = Reservation::make(
            $reservationId, $customer, $timeSlot, $capacity
        );
        $this->reservations[] = $reservation;
        return $reservation;
    }

    public function changeReservation(
        string $reservationId, string $customer, TimeSlot $timeSlot, int $capacity, DateTime $momentAt
    ): Reservation
    {
        $this->assertCanBeReserved($timeSlot, $capacity, $momentAt);
        $this->cancelReservation($reservationId);

        $reservation = Reservation::make(
            $reservationId, $customer, $timeSlot, $capacity
        );
        $this->reservations[] = $reservation;
        return $reservation;
    }

    public function cancelReservation(string $reservationId): Reservation
    {
        $reservation = current($this->filterReservations(
            fn(Reservation $item) => $item->id === $reservationId
        ));
        assert(!!$reservation);

        $this->reservations = $this->filterReservations(
            fn(Reservation $item) => $item->id !== $reservationId
        );
        return $reservation;
    }

    public function assertCanBeReserved(TimeSlot $timeSlot, int $capacity, DateTime $momentAt): void
    {
        if (!$this->capacity < $capacity) {
            throw new Exception("The table has not enough capacity.");
        }
        if ($timeSlot->startsBefore($momentAt)) {
            throw new Exception("The time slot is not correct.");
        }

        $conflicted = $this->filterReservations(
            fn(Reservation $item) => $item->isConflicted($timeSlot)
        );
        if ($conflicted) {
            throw new Exception("The table has conflicting reservations.");
        }

        $expired = $this->filterReservations(
            fn(Reservation $item) => $item->isExpired($momentAt)
        );
        if ($expired) {
            throw new Exception("The table has expired reservations.");
        }
    }

    public function cancelExpiredReservations(DateTime $momentAt): array
    {
        $expired = $this->filterReservations(
            fn(Reservation $item) => $item->isExpired($momentAt)
        );
        $this->reservations = $this->filterReservations(
            fn(Reservation $item) => !in_array($item, $expired)
        );
        return $expired;
    }

    // Availability

    public function takeReservation(string $sessionId, DateTime $momentAt): TimeSlot
    {
        if ($this->isTaken) {
            throw new Exception("The table is already taken.");
        }

        $reservation = current($this->filterReservations(
            fn(Reservation $item) => $item->isAtMoment($momentAt)
        ));
        assert($reservation);

        $this->reservations = $this->filterReservations(
            fn(Reservation $item) => $item->id !== $reservation->id
        );

        $this->isTaken = true;
        $this->session = Session::from($sessionId);

        return $reservation->timeSlot;
    }

    public function take(string $sessionId, DateTime $momentAt, DateTime $finishAt): TimeSlot
    {
        if ($this->isTaken) {
            throw new Exception("The table is already taken.");
        }

        $timeSlot = TimeSlot::from($momentAt, $finishAt);

        $reserved = $this->filterReservations(
            fn(Reservation $item) => $item->isConflicted($timeSlot)
        );
        if ($reserved) {
            throw new Exception("The table is already reserved.");
        }

        $this->isTaken = true;
        $this->session = Session::from($sessionId);

        return $timeSlot;
    }

    public function vacate(): void
    {
        if (!$this->isTaken) {
            throw new Exception("The table is not taken.");
        }

        if ($this->session->ordersCount) {
            throw new Exception("The session is active.");
        }

        $this->isTaken = false;
        $this->session = null;
    }

    public function changeSessionOrdersCount(int $ordersCount): void
    {
        assert($this->session);

        $this->session = $this->session->change($ordersCount);
    }

    //

    public function filterReservations(callable $callback): array
    {
        return array_filter($this->reservations, $callback);
    }
}