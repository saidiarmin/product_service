<?php

namespace App\Service;

use App\Entity\Product;
use App\Model\Paginator;

interface ProductServiceInterface
{
    public function getProducts(): ?array;
    public function getProductsWithPagination(int $page): Paginator;
    public function getProductById(int $id): ?Product;
    public function createProduct(Product $product): void;
    public function removeProduct(Product $product): void;
    public function updateProduct(): void;
}