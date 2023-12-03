<?php

namespace Restaurant\ReservationContext\App;

use Restaurant\Kernel\Infra\Events\MomentHappened;
use Restaurant\Kernel\Infra\Messaging\MessageBusInterface;
use Restaurant\ReservationContext\App\IntegrationEvents\ReservationContextTableSessionUpdateFailed;
use Restaurant\ReservationContext\Infra\Repositories\TableRepositoryInterface;
use Restaurant\ServiceContext\App\IntegrationEvents\ServiceContextTableSessionUpdated;
use Throwable;

readonly class TableSaga
{
    public function __construct(
        private TableAppService          $tableAppService,
        private TableRepositoryInterface $tableRepository,
        private MessageBusInterface      $messageBus
    )
    {
    }

    public function onMomentHappened(MomentHappened $event): string
    {
        $tableIds = $this->tableRepository->allIds();
        foreach ($tableIds as $tableId) {
            $this->tableAppService->cancelTableExpiredReservations($tableId, $event->momentAt);
        }
    }

    public function onServiceContextTableSessionChanged(ServiceContextTableSessionUpdated $event): string
    {
        try {
            $this->tableAppService->updateTableSession(
                $event->sessionId, $event->processingOrdersCount
            );
        } catch (Throwable) {
            $this->messageBus->emit(
                ReservationContextTableSessionUpdateFailed::from($event->sessionId)
            );
        }
    }
}