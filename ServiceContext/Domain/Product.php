<?php

namespace Restaurant\ServiceContext\Domain;

class Product
{
    public string $id;
    public int $price;
    public bool $isRestricted;

    public static function from(string $id, int $price, bool $isRestricted): static
    {
        $self = new static;
        $self->id = $id;
        $self->price = $price;
        $self->isRestricted = $isRestricted;
        return $self;
    }
}