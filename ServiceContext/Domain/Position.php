<?php

namespace Restaurant\ServiceContext\Domain;

class Position
{
    public string $id;
    public string $productId;
    public int $productPrice;
    public bool $isCompleted;

    public static function add(string $id, Product $product): static
    {
        assert(!$product->isRestricted);

        $self = new static;
        $self->id = $id;
        $self->productId = $product->id;
        $self->productPrice = $product->price;
        $self->isCompleted = false;
        return $self;
    }

    public function changeCompleted(bool $isCompleted): void
    {
        $this->isCompleted = $isCompleted;
    }
}