<?php

namespace Restaurant\ServiceContext\App;

use Restaurant\ReservationContext\App\IntegrationEvents\ReservationContextTableSessionUpdateFailed;
use Restaurant\ServiceContext\Domain\OrderCriteria;
use Restaurant\ServiceContext\Infra\Repositories\OrderRepositoryInterface;

readonly class OrderSaga
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderAppService          $orderAppService,
    )
    {
    }

    public function onReservationContextTableSessionUpdateFailed(ReservationContextTableSessionUpdateFailed $event): void
    {
        $criteria = OrderCriteria::from(
            sessionId: $event->sessionId,
            isClosed: false
        );
        array_map(
            fn($id) => $this->orderAppService->cancelOrder($id),
            $this->orderRepository->idsByCriteria($criteria)
        );
    }
}