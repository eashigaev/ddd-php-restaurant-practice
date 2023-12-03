<?php

namespace Restaurant\ReservationContext\Domain;

use DateTime;

class Reservation
{
    public string $id;
    public string $customer;
    public TimeSlot $timeSlot;
    public int $capacity;

    public static function make(string $id, string $customer, TimeSlot $timeSlot, int $capacity): static
    {
        $self = new static;
        $self->id = $id;
        $self->customer = $customer;
        $self->timeSlot = $timeSlot;
        $self->capacity = $capacity;
        return $self;
    }

    public function isConflicted(TimeSlot $timeSlot): bool
    {
        return $this->timeSlot->overlaps($timeSlot);
    }

    public function isExpired(DateTime $momentAt): bool
    {
        return $this->timeSlot->finishesBefore($momentAt);
    }

    public function isAtMoment(DateTime $momentAt): bool
    {
        return $this->timeSlot->contains($momentAt);
    }
}