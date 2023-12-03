<?php

namespace Restaurant\ServiceContext\Infra\Repositories;

use Restaurant\ProductionContext\ProductionContext;
use Restaurant\ServiceContext\Domain\Product;

readonly class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private ProductionContext $productionContext
    )
    {
    }

    public function ofId(string $id): ?Product
    {
        $productionProduct = $this->productionContext->productAppService->getProduct($id);

        return $productionProduct
            ? Product::from(
                $productionProduct->id,
                $productionProduct->price,
                $productionProduct->isSold || $productionProduct->isArchived
            )
            : null;
    }
}