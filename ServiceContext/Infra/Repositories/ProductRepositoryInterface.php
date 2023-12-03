<?php

namespace Restaurant\ServiceContext\Infra\Repositories;

use Restaurant\ServiceContext\Domain\Product;

interface ProductRepositoryInterface
{
    public function ofId(string $id): ?Product;
}