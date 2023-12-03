<?php

namespace Restaurant\ReservationContext\Infra;

use Restaurant\Kernel\Infra\Events\MomentHappened;
use Restaurant\Kernel\Infra\Messaging\MessageBusInterface;
use Restaurant\ReservationContext\App\TableSaga;
use Restaurant\ServiceContext\App\IntegrationEvents\ServiceContextTableSessionUpdated;

readonly class ReservationContextProvider
{
    public function __construct(
        private MessageBusInterface $messageBus,
    )
    {
    }

    public function bootstrap(): string
    {
        $this->messageBus->listen(
            MomentHappened::class, TableSaga::class
        );
        $this->messageBus->listen(
            ServiceContextTableSessionUpdated::class, TableSaga::class
        );
    }
}