<?php

namespace Restaurant\ServiceContext\Infra\Repositories;

use Restaurant\ServiceContext\Domain\Order;
use Restaurant\ServiceContext\Domain\OrderCriteria;

interface OrderRepositoryInterface
{
    public function ofId(string $id): ?Order;

    public function save(Order $order);

    /** @return string[] */
    public function idsByCriteria(OrderCriteria $criteria): array;

    /** @return Order[] */
    public function byCriteria(OrderCriteria $criteria): array;
}