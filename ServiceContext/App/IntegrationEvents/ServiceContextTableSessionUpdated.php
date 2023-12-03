<?php

namespace Restaurant\ServiceContext\App\IntegrationEvents;

readonly class ServiceContextTableSessionUpdated
{
    public string $sessionId;
    public int $processingOrdersCount;
    public int $totalOrdersCount;

    public static function from(string $sessionId, int $processingOrdersCount, int $totalOrdersCount): static
    {
        $self = new static();
        $self->sessionId = $sessionId;
        $self->processingOrdersCount = $processingOrdersCount;
        $self->totalOrdersCount = $totalOrdersCount;
        return $self;
    }
}