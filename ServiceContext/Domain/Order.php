<?php

namespace Restaurant\ServiceContext\Domain;

use Restaurant\Kernel\Infra\OptimisticLockingTrait;

class Order
{
    use OptimisticLockingTrait;

    public string $id;
    public string $tableId;
    public string $tableSessionId;
    public string $waiter;
    public OrderStatus $status;
    public bool $isClosed;

    /** @var Position[] */
    public array $positions;

    public static function open(string $id, Table $table, string $waiter): static
    {
        assert(!$table->isRestricted);

        $self = new static();
        $self->id = $id;
        $self->tableId = $table->id;
        $self->tableSessionId = $table->sessionId;
        $self->waiter = $waiter;
        $self->status = OrderStatus::SERVING;
        $self->isClosed = false;
        $self->positions = [];
        return $self;
    }

    // Serving

    public function addPosition(string $positionId, Product $product): Position
    {
        assert(!$this->isClosed && $this->status === OrderStatus::SERVING);

        $position = Position::add($positionId, $product);
        $this->positions[] = $position;
        return $position;
    }

    public function changePositionCompleted(string $positionId, bool $isCompleted): Position
    {
        assert(!$this->isClosed && $this->status === OrderStatus::SERVING);

        $position = current($this->filterPositions(
            fn(Position $item) => $item->id === $positionId
        ));
        assert($position);

        $position->changeCompletion($isCompleted);
        return $position;
    }

    public function removePosition(string $positionId): Position
    {
        assert(!$this->isClosed && $this->status === OrderStatus::SERVING);

        $position = current($this->filterPositions(
            fn(Position $item) => $item->id === $positionId
        ));
        $this->positions = $this->filterPositions(
            fn(Position $item) => $item->id !== $positionId
        );
        return $position;
    }

    public function confirmPositions(): void
    {
        assert(!$this->isClosed && $this->status == OrderStatus::SERVING);

        $this->status = OrderStatus::PAYING;
    }

    // Paying

    public function complete(int $amount): void
    {
        assert(!$this->isClosed && $this->status == OrderStatus::PAYING);
        assert($this->calculateTotal() === $amount);

        $this->status = OrderStatus::COMPLETED;
        $this->isClosed = true;
    }

    //

    public function cancel(): void
    {
        assert(!$this->isClosed);

        $this->isClosed = true;
    }

    public function calculateTotal(): int
    {
        $total = 0;
        foreach ($this->positions as $position) {
            if (!$position->isCompleted) continue;
            $total += $position->productPrice;
        }
        return $total;
    }

    //

    public function filterPositions(callable $callback): array
    {
        return array_filter($this->positions, $callback);
    }
}