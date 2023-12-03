<?php

namespace Restaurant\ProductionContext\Infra\Repositories;

use Restaurant\ProductionContext\Domain\Product;

interface ProductRepositoryInterface
{
    public function ofId(string $id): ?Product;

    public function save(Product $product);

    /** @return Product[] */
    public function byCriteria(): array;
}