<?php

namespace Restaurant\ServiceContext\App;

use Restaurant\Kernel\Infra\Messaging\MessageBusInterface;
use Restaurant\ServiceContext\App\IntegrationEvents\ServiceContextTableSessionUpdated;
use Restaurant\ServiceContext\Domain\Order;
use Restaurant\ServiceContext\Domain\OrderCriteria;
use Restaurant\ServiceContext\Infra\Repositories\OrderRepositoryInterface;
use Restaurant\ServiceContext\Infra\Repositories\ProductRepositoryInterface;
use Restaurant\ServiceContext\Infra\Repositories\TableRepositoryInterface;

readonly class OrderAppService
{
    public function __construct(
        private TableRepositoryInterface   $tableRepository,
        private OrderRepositoryInterface   $orderRepository,
        private ProductRepositoryInterface $productRepository,
        private MessageBusInterface        $messageBus
    )
    {
    }

    // Queries

    public function getOrder(string $orderId): ?Order
    {
        return $this->orderRepository->ofId($orderId);
    }

    /** @return Order[] */
    public function getOrderList(array $criteria): array
    {
        $criteria = OrderCriteria::from(
            sessionId: $criteria['session_id'] ?? null,
            isClosed: $criteria['is_closed'] ?? null,
        );

        return $this->orderRepository->byCriteria($criteria);
    }

    // Commands

    public function openOrder(string $tableId, string $waiter): string
    {
        $table = $this->tableRepository->ofId($tableId);
        assert($table);

        $order = Order::open(uniqid(), $table, $waiter);
        $this->orderRepository->save($order);

        $this->emitTableSessionUpdated($order->tableSessionId);

        return $order->id;
    }

    public function addOrderPosition(string $orderId, string $productId): string
    {
        $order = $this->orderRepository->ofId($orderId);
        assert($order);

        $product = $this->productRepository->ofId($productId);
        assert($product);

        $position = $order->addPosition(uniqid(), $product);
        $this->orderRepository->save($order);

        return $position->id;
    }

    public function changeOrderPositionCompleted(string $orderId, string $positionId, bool $isCompleted): void
    {
        $order = $this->orderRepository->ofId($orderId);
        assert($order);

        $order->changePositionCompleted($positionId, $isCompleted);
        $this->orderRepository->save($order);
    }

    public function removeOrderPosition(string $orderId, string $positionId): void
    {
        $order = $this->orderRepository->ofId($orderId);
        assert($order);

        $order->removePosition($positionId);
        $this->orderRepository->save($order);
    }

    public function confirmOrderPositions(string $orderId): void
    {
        $order = $this->orderRepository->ofId($orderId);
        assert($order);

        $order->confirmPositions();
        $this->orderRepository->save($order);
    }

    public function completeOrder(string $orderId, int $amount): void
    {
        $order = $this->orderRepository->ofId($orderId);
        assert($orderId);

        $order->complete($amount);
        $this->orderRepository->save($order);

        $this->emitTableSessionUpdated($order->tableSessionId);
    }

    public function cancelOrder(string $id): void
    {
        $order = $this->orderRepository->ofId($id);
        assert($order);

        $order->cancel();
        $this->orderRepository->save($order);

        $this->emitTableSessionUpdated($order->tableSessionId);
    }

    //

    private function emitTableSessionUpdated(string $sessionId): void
    {
        $criteria = OrderCriteria::from(sessionId: $sessionId);

        $orders = $this->orderRepository->byCriteria($criteria);
        $processingOrders = array_filter($orders, fn(Order $item) => !$item->isClosed);

        $this->messageBus->emit(
            ServiceContextTableSessionUpdated::from(
                $sessionId, count($processingOrders), count($orders)
            )
        );
    }
}