<?php

namespace Restaurant\ProductionContext\App;

use Restaurant\ProductionContext\Domain\Product;
use Restaurant\ProductionContext\Infra\Repositories\ProductRepositoryInterface;

readonly class ProductAppService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    )
    {
    }

    // Queries

    public function getProduct(string $productId): ?Product
    {
        return $this->productRepository->ofId($productId);
    }

    /** @return Product[] */
    public function getProductList(array $product): array
    {
        return $this->productRepository->byCriteria();
    }

    // Commands

    public function addProduct(string $name, int $price): string
    {
        $product = Product::add(uniqid(), $name, $price);
        $this->productRepository->save($product);

        return $product->id;
    }

    public function changeProduct(string $productId, string $name, int $price): void
    {
        $product = $this->productRepository->ofId($productId);
        assert($product);

        $product->change($name, $price);
        $this->productRepository->save($product);
    }

    public function changeProductSold(string $productId, bool $isSold): void
    {
        $product = $this->productRepository->ofId($productId);
        assert($product);

        $product->changeSold($isSold);
        $this->productRepository->save($product);
    }

    public function changeProductArchived(string $productId, bool $isArchived): void
    {
        $product = $this->productRepository->ofId($productId);
        assert($product);

        $product->changeArchived($isArchived);
        $this->productRepository->save($product);
    }
}