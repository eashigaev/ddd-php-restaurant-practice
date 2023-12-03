<?php

namespace Restaurant\ServiceContext\Domain;

class OrderCriteria
{
    public ?string $sessionId;
    public ?string $isClosed;

    public static function from(
        ?string $sessionId = null,
        ?string $isClosed = null
    ): static
    {
        $self = new static;
        $self->sessionId = $sessionId;
        $self->isClosed = $isClosed;
        return $self;
    }
}