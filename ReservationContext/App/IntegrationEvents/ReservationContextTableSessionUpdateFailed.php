<?php

namespace Restaurant\ReservationContext\App\IntegrationEvents;

readonly class ReservationContextTableSessionUpdateFailed
{
    public string $sessionId;

    public static function from(string $sessionId): static
    {
        $self = new static();
        $self->sessionId = $sessionId;
        return $self;
    }
}