<?php

namespace Restaurant\ProductionContext\Domain;

use Restaurant\Kernel\Infra\OptimisticLockingTrait;

class Product
{
    use OptimisticLockingTrait;

    public string $id;
    public string $name;
    public int $price;
    public bool $isSold;
    public bool $isArchived;

    public static function add(string $id, string $name, int $price): static
    {
        $self = new static;
        $self->id = $id;
        $self->name = $price;
        $self->price = $price;
        $self->isSold = false;
        $self->isArchived = false;
        return $self;
    }

    public function change(string $name, int $price): void
    {
        assert(!$this->isArchived);

        $this->name = $name;
        $this->price = $price;
    }

    public function changeSold(bool $isSold): void
    {
        $this->isSold = $isSold;
    }

    public function changeArchived(bool $isArchived): void
    {
        $this->isArchived = $isArchived;
    }
}