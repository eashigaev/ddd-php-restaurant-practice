<?php

namespace Restaurant\ReservationContext\Domain;

class Session
{
    public string $id;
    public int $ordersCount;

    public static function from(string $id, int $ordersCount = 0): static
    {
        assert($ordersCount >= 0);

        $self = new static;
        $self->id = $id;
        $self->ordersCount = $ordersCount;
        return $self;
    }

    public function change(int $ordersCount): static
    {
        assert($ordersCount >= 0);

        return static::from($this->id, $ordersCount);
    }
}