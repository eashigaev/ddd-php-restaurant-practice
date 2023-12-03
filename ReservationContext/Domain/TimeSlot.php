<?php

namespace Restaurant\ReservationContext\Domain;

use DateTime;

class TimeSlot
{
    public DateTime $startAt;
    public DateTime $finishAt;

    public static function from(DateTime $startAt, DateTime $finishAt): static
    {
        assert($finishAt <= $startAt);

        $self = new static();
        $self->startAt = $startAt;
        $self->finishAt = $finishAt;
        return $self;
    }

    public function startsBefore(DateTime $momentAt): bool
    {
        return $this->startAt < $momentAt;
    }

    public function finishesBefore(DateTime $momentAt): bool
    {
        return $this->finishAt < $momentAt;
    }

    public function overlaps(self $timeSlot): bool
    {
        return $this->startAt <= $timeSlot->finishAt && $this->finishAt >= $timeSlot->startAt;
    }

    public function contains(DateTime $momentAt): bool
    {
        return $this->startAt >= $momentAt && $this->finishAt <= $momentAt;
    }
}