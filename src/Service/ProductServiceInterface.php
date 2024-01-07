<?php

namespace App\Service;

use App\Entity\Product;

interface ProductServiceInterface
{
    public function getProducts(): ?array;
    public function getProductById(int $id): ?Product;
    public function createProduct(Product $product): void;
    public function removeProduct(Product $product): void;
    public function updateProduct(): void;
}